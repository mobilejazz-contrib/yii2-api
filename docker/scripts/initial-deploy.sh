#!/bin/bash

# Create symbolic link for the site
rm -rf /var/www/html
ln -s /app/app /var/www/html

source /config


# Create database
mysql -uroot -p${password} -e "CREATE DATABASE ${app}"

# Create user
mysql -uroot -p${password} -e "GRANT ALL PRIVILEGES ON ${database_name}.* TO ${database_name}@localhost IDENTIFIED BY '${mysql_app_password}'"

# Go to app folder
cd /app/app

# Update config files
sed -i "s/%database_name%/${image_name}/g" environments/dev/common/config/main-local.php
sed -i "s/%database_name%/${image_name}/g" environments/prod/common/config/main-local.php
sed -i "s/%app_password%/${mysql_app_password}/g" environments/dev/common/config/main-local.php
sed -i "s/%app_password%/${mysql_app_password}/g" environments/prod/common/config/main-local.php
sed -i "s/%mandrill_key%/${mandrill_key}/g" environments/dev/common/config/main-local.php
sed -i "s/%mandrill_key%/${mandrill_key}/g" environments/prod/common/config/main-local.php
sed -i "s/%parse_appid%/${parse_appid}/g" environments/prod/common/config/main-local.php
sed -i "s/%parse_appid%/${parse_appid}/g" environments/prod/common/config/main-local.php
sed -i "s/%parse_masterkey%/${parse_masterkey}/g" environments/prod/common/config/main-local.php
sed -i "s/%parse_masterkey%/${parse_masterkey}/g" environments/prod/common/config/main-local.php
sed -i "s/%parse_apikey%/${parse_apikey}/g" environments/prod/common/config/main-local.php
sed -i "s/%parse_apikey%/${parse_apikey}/g" environments/prod/common/config/main-local.php


sed -i "s/%app_name%/${app_name}/g" common/config/main.php


# Install composer packages
composer global require "fxp/composer-asset-plugin:~1.0.0"
composer update
composer install

# Init yii
./init --env=Development --overwrite=All

# Yii migate
./yii migrate --interactive=0
./yii migrate --interactive=0 --migrationPath=@vendor/mobilejazz/yii2-oauth2-server/migrations
./yii migrate --interactive=0 --migrationPath=@yii/rbac/migrations

# Add oauth client
mysql -uroot -p${password} -e "USE ${app}; DELETE FROM oauth_clients;";
mysql -uroot -p${password} -e "USE ${app}; INSERT INTO oauth_clients (client_id, client_secret, redirect_uri, grant_types, scope, user_id) VALUES ('${oauth_client_name}', '${oauth_client_pass}', 'http://${image_name}', 'client_credentials password refresh_token', NULL, NULL);"