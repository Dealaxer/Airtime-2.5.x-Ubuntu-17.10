"""
Python part of radio playout (pypo)
"""

from optparse import OptionParser
from datetime import datetime

import telnetlib

import time
import sys
import signal
import logging
import locale
import os
import re

from Queue import Queue
from threading import Lock

from pypopush import PypoPush
from pypofetch import PypoFetch
from pypofile import PypoFile
from recorder import Recorder
from listenerstat import ListenerStat
from pypomessagehandler import PypoMessageHandler
from pypoliquidsoap import PypoLiquidsoap
from timeout import ls_timeout

from pypo.media.update.replaygainupdater import ReplayGainUpdater
from pypo.media.update.silananalyzer import SilanAnalyzer

from configobj import ConfigObj

# custom imports
from api_clients import api_client
#from std_err_override import LogWriter
import pure

LOG_PATH = '/var/log/airtime/pypo/pypo.log'
LOG_LEVEL = logging.INFO

# Set up command-line options
parser = OptionParser()

# help screen / info
usage = "%prog [options]" + " - python playout system"
parser = OptionParser(usage=usage)

# Options
parser.add_option("-v", "--compat",
        help="Check compatibility with server API version",
        default=False,
        action="store_true",
        dest="check_compat")

parser.add_option("-t", "--test",
        help="Do a test to make sure everything is working properly.",
        default=False,
        action="store_true",
        dest="test")

parser.add_option("-b",
        "--cleanup",
        help="Cleanup",
        default=False,
        action="store_true",
        dest="cleanup")

parser.add_option("-c",
        "--check",
        help="Check the cached schedule and exit",
        default=False,
        action="store_true",
        dest="check")

# parse options
(options, args) = parser.parse_args()

LIQUIDSOAP_MIN_VERSION = "1.1.1"

PYPO_HOME='/var/tmp/airtime/pypo/'

def configure_environment():
    os.environ["HOME"] = PYPO_HOME
    os.environ["TERM"] = 'xterm'

configure_environment()

# need to wait for Python 2.7 for this..
logging.captureWarnings(True)

# configure logging
try:
    # Set up logging
    logFormatter = logging.Formatter("%(asctime)s [%(module)s] [%(levelname)-5.5s]  %(message)s")
    rootLogger = logging.getLogger()
    rootLogger.setLevel(LOG_LEVEL)
    logger = rootLogger

    consoleHandler = logging.StreamHandler()
    consoleHandler.setFormatter(logFormatter)
    rootLogger.addHandler(consoleHandler)
except Exception, e:
    print "Couldn't configure logging", e
    sys.exit(1)

def configure_locale():
    """
    Silly hacks to force Python 2.x to run in UTF-8 mode. Not portable at all,
    however serves our purpose at the moment.

    More information available here:
    http://stackoverflow.com/questions/3828723/why-we-need-sys-setdefaultencodingutf-8-in-a-py-script
    """
    logger.debug("Before %s", locale.nl_langinfo(locale.CODESET))
    current_locale = locale.getlocale()

    if current_locale[1] is None:
        logger.debug("No locale currently set. Attempting to get default locale.")
        default_locale = locale.getdefaultlocale()

        if default_locale[1] is None:
            logger.debug("No default locale exists. Let's try loading from \
                    /etc/default/locale")
            if os.path.exists("/etc/default/locale"):
                locale_config = ConfigObj('/etc/default/locale')
                lang = locale_config.get('LANG')
                new_locale = lang
            else:
                logger.error("/etc/default/locale could not be found! Please \
                        run 'sudo update-locale' from command-line.")
                sys.exit(1)
        else:
            new_locale = default_locale

        logger.info("New locale set to: %s", \
                locale.setlocale(locale.LC_ALL, new_locale))

    reload(sys)
    sys.setdefaultencoding("UTF-8")
    current_locale_encoding = locale.getlocale()[1].lower()
    logger.debug("sys default encoding %s", sys.getdefaultencoding())
    logger.debug("After %s", locale.nl_langinfo(locale.CODESET))

    if current_locale_encoding not in ['utf-8', 'utf8']:
        logger.error("Need a UTF-8 locale. Currently '%s'. Exiting..." % \
                current_locale_encoding)
        sys.exit(1)


configure_locale()

# loading config file
try:
    config = ConfigObj('/etc/airtime/airtime.conf')
except Exception, e:
    logger.error('Error loading config file: %s', e)
    sys.exit(1)

class Global:
    def __init__(self, api_client):
        self.api_client = api_client

    def selfcheck(self):
        return self.api_client.is_server_compatible()

    def test_api(self):
        self.api_client.test()

def keyboardInterruptHandler(signum, frame):
    logger = logging.getLogger()
    logger.info('\nKeyboard Interrupt\n')
    sys.exit(0)

@ls_timeout
def liquidsoap_get_info(telnet_lock, host, port, logger):
    logger.debug("Checking to see if Liquidsoap is running")
    try:
        telnet_lock.acquire()
        tn = telnetlib.Telnet(host, port)
        msg = "version\n"
        tn.write(msg)
        tn.write("exit\n")
        response = tn.read_all()
    except Exception, e:
        logger.error(str(e))
        return None
    finally:
        telnet_lock.release()

    return get_liquidsoap_version(response)

def get_liquidsoap_version(version_string):
    m = re.match(r"Liquidsoap (\d+.\d+.\d+)", version_string)

    if m:
        return m.group(1)
    else:
        return None


    if m:
        current_version = m.group(1)
        return pure.version_cmp(current_version, LIQUIDSOAP_MIN_VERSION) >= 0
    return False

def liquidsoap_startup_test():

    liquidsoap_version_string = \
            liquidsoap_get_info(telnet_lock, ls_host, ls_port, logger)
    while not liquidsoap_version_string:
        logger.warning("Liquidsoap doesn't appear to be running!, " + \
               "Sleeping and trying again")
        time.sleep(1)
        liquidsoap_version_string = \
                liquidsoap_get_info(telnet_lock, ls_host, ls_port, logger)

    while pure.version_cmp(liquidsoap_version_string, LIQUIDSOAP_MIN_VERSION) < 0:
        logger.warning("Liquidsoap is running but in incorrect version! " + \
                "Make sure you have at least Liquidsoap %s installed" % LIQUIDSOAP_MIN_VERSION)
        time.sleep(1)
        liquidsoap_version_string = \
                liquidsoap_get_info(telnet_lock, ls_host, ls_port, logger)

    logger.info("Liquidsoap version string found %s" % liquidsoap_version_string)


if __name__ == '__main__':
    logger.info('###########################################')
    logger.info('#             *** pypo  ***               #')
    logger.info('#   Liquidsoap Scheduled Playout System   #')
    logger.info('###########################################')

    #Although all of our calculations are in UTC, it is useful to know what timezone
    #the local machine is, so that we have a reference for what time the actual
    #log entries were made
    logger.info("Timezone: %s" % str(time.tzname))
    logger.info("UTC time: %s" % str(datetime.utcnow()))

    signal.signal(signal.SIGINT, keyboardInterruptHandler)

    api_client = api_client.AirtimeApiClient()
    g = Global(api_client)

    while not g.selfcheck():
        time.sleep(5)

    success = False
    while not success:
        try:
            api_client.register_component('pypo')
            success = True
        except Exception, e:
            logger.error(str(e))
            time.sleep(10)

    telnet_lock = Lock()

    ls_host = config['pypo']['ls_host']
    ls_port = config['pypo']['ls_port']

    liquidsoap_startup_test()

    if options.test:
        g.test_api()
        sys.exit(0)


    ReplayGainUpdater.start_reply_gain(api_client)
    SilanAnalyzer.start_silan(api_client, logger)

    pypoFetch_q = Queue()
    recorder_q = Queue()
    pypoPush_q = Queue()

    pypo_liquidsoap = PypoLiquidsoap(logger, telnet_lock,\
            ls_host, ls_port)

    """
    This queue is shared between pypo-fetch and pypo-file, where pypo-file
    is the consumer. Pypo-fetch will send every schedule it gets to pypo-file
    and pypo will parse this schedule to determine which file has the highest
    priority, and retrieve it.
    """
    media_q = Queue()

    # Pass only the configuration sections needed; PypoMessageHandler only needs rabbitmq settings
    pmh = PypoMessageHandler(pypoFetch_q, recorder_q, config['rabbitmq'])
    pmh.daemon = True
    pmh.start()

    pfile = PypoFile(media_q, config['pypo'])
    pfile.daemon = True
    pfile.start()

    pf = PypoFetch(pypoFetch_q, pypoPush_q, media_q, telnet_lock, pypo_liquidsoap, config['pypo'])
    pf.daemon = True
    pf.start()

    pp = PypoPush(pypoPush_q, telnet_lock, pypo_liquidsoap, config['pypo'])
    pp.daemon = True
    pp.start()

    recorder = Recorder(recorder_q)
    recorder.daemon = True
    recorder.start()

    stat = ListenerStat()
    stat.daemon = True
    stat.start()

    # Just sleep the main thread, instead of blocking on pf.join().
    # This allows CTRL-C to work!
    while True:
        time.sleep(1)

    logger.info("System exit")
