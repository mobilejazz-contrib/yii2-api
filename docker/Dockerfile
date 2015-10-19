FROM linode/lamp:latest

RUN apt-get update		#20151019 Change the date if you add any new package as apt-get update might use cache and fail later

# Install supervisor
RUN apt-get install -y supervisor

# Install command line utils
RUN apt-get install -y curl mcrypt git

# Install PHP additional modules
RUN apt-get install -y adminer php5-gd php5-dev php-pear php5-xdebug php5-mysql php5-curl aha

# Instal composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# Setup xdebug
RUN echo "xdebug.remote_enable=1" >> /etc/php5/mods-available/xdebug.ini
RUN echo "xdebug.remote_autostart=1" >> /etc/php5/mods-available/xdebug.ini
RUN echo "xdebug.remote_connect_back=1" >> /etc/php5/mods-available/xdebug.ini
RUN echo "xdebug.remote_port=9000" >> /etc/php5/mods-available/xdebug.ini

# Enable modules
RUN php5enmod mcrypt
RUN a2enmod rewrite

# Folder permissions
RUN usermod -u 1000 www-data

# Configuration file
ADD conf/config /config

# Apache configuration file
ADD conf/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN ln -s /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/
RUN rm /etc/apache2/sites-enabled/example.com.conf

# Supervisord configuration file
ADD conf/supervisord/apache2.conf /etc/supervisor/conf.d/apache2.conf
ADD conf/supervisord/mysql.conf /etc/supervisor/conf.d/mysql.conf

# Scripts
ADD scripts/run.sh /run.sh
ADD scripts/start-apache2.sh /start-apache2.sh
ADD scripts/start-mysqld.sh /start-mysqld.sh
RUN chmod 777 /run.sh
RUN chmod 777 /start-apache2.sh
RUN chmod 777 /start-mysqld.sh

# Data volumnes
VOLUME ['/app']

# Ports
EXPOSE 80 3306

# Start
CMD ["/run.sh"]
