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
