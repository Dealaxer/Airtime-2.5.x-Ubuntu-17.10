# Airtime 2.5.x for Ubuntu 17.10
A reworked version of the web application Airtime-2.5.x to install on Ubuntu 17.10 with PHP5.6

![Airtime Login Page](http://s010.radikal.ru/i313/1711/27/4511afd9e0b1.png)

## Initial steps for installing Airtime

### Adding repositories
<code>sudo add-apt-repository ppa:ondrej/php</code><br>
<code>sudo add-apt-repository ppa:flexiondotorg/audio</code><br>
<code>sudo apt-get update</code>

### Install PHP5.6
<code>sudo apt-get install php5.6 php5.6-bcmath</code>

#### If you install php5.6 the default is version 7.2(php -v) then execute the following commands:
```
Apache:
sudo a2dismod php7.2 ; sudo a2enmod php5.6 ; sudo service apache2 restart

CLI:
sudo update-alternatives --set php /usr/bin/php5.6
```

### Install MP3Gain
<code>sudo apt-get install mp3gain</code>

## Install Airtime 2.5.x
<code>./install</code>

## After installation
Open a browser and enter in the address bar <strong>localhost</strong> or Your <strong>IP address</strong> to enter the configuration page of Airtime!

### 5 configuration step, You must start the services through the terminal:
<code>sudo service airtime-playout start</code><br>
<code>sudo service airtime-liquidsoap start</code><br>
<code>sudo service airtime-media-monitor start</code><br>

#### If you fail to run a service, first stop them and then run
![Airtime Status](http://s015.radikal.ru/i330/1711/38/3402ab9ecddf.png)

## Important features
When you add audio files, these files are stored in /tmp/plupload and if apache uses privacy, then it is better to disable this:<br>
<code>nano /etc/systemd/system/multi-user.target.wants/apache2.service</code><br>
And change the value PrivateTmp=<strong>true</strong> to <strong>false</strong>

### Directories
Also for some, it is possible to use storage of audio files in the following directories with permissions 777:
```
/srv/airtime/stor/
/tmp/plupload
```
