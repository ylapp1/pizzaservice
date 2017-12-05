#!/usr/bin/env bash

# array usage in apt-get: https://gist.github.com/edouard-lopez/10008944

# Package list
packages=(

  # PHP7
  php7.0-common php7.0-cli

  ## MySQL driver
  php7.0-mysql

  ## The zip package is needed for composer
  php7.0-zip


  # Apache
  apache2 libapache2-mod-php7.0

  # Mysql
  mysql-server

  # Git
  git

  # Pear
  php-pear
)

apt-get update

# Settings for the root password prompt of the mysql-server package
# Sets the password for the mysql "root" user to "root"
echo "mysql-server-5.5 mysql-server/root_password password root" | debconf-set-selections
echo "mysql-server-5.5 mysql-server/root_password_again password root" | debconf-set-selections
apt-get install -y "${packages[@]}"


# Create a link to /vagrant in /var/www/html
if ! [ -L /var/www/html ]; then
  rm -rf /var/www/html
  ln -fs /vagrant /var/www/html
fi


# Install composer
cd /usr/local/bin
EXPECTED_SIGNATURE=$(wget -q -O - https://composer.github.io/installer.sig)
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_SIGNATURE=$(php -r "echo hash_file('SHA384', 'composer-setup.php');")

if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]
then
    >&2 echo 'ERROR: Invalid installer signature'
    rm composer-setup.php
    exit 1
fi

php composer-setup.php --quiet --filename=composer
RESULT=$?
rm composer-setup.php
chmod a+x composer
cd

# Install propel dependencies
pear channel-discover pear.phing.info
pear install phing/phing
pear install Log
