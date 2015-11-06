#!/bin/bash
if [ "$#" -lt 3 ]; then
    echo "Illegal number of parameters. You should use:"
    echo "  ./server-deploy <current_mysql_root_password> <git_url> <environment>"
    echo "  <environment> can be: Development or Production (first letter uppercase)"
    exit
fi

apt-get update
apt-get install -y curl mcrypt git adminer php5-gd php5-dev php-pear php5-xdebug php5-mysql php5-curl aha
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
php5enmod mcrypt
a2enmod rewrite

echo "Generating the ssh key"
ssh-keygen
cat .ssh/id_rsa.pub
echo "Add the public key to Bitbucket deployment keys..."

echo "Press enter to continue"
read repo
git clone $2 /app

cp /app/docker/conf/config /config

source /config

mysql -uroot -p$1 -e "SET PASSWORD FOR 'root'@'localhost' = PASSWORD('${mysql_root_password}');"


chmod 700 /app/docker/scripts/initial-deploy.sh
/app/docker/scripts/initial-deploy.sh $3

chmod 777 /app/deploy

cp /app/docker/conf/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
service apache2 restart


echo " "
echo "-----------------------"
echo "Server deployment script executed."
echo " "
echo "Please check /app/deploy/config.php file to put the required data there"
echo "You should also add the webhook to Bitbucket repository"