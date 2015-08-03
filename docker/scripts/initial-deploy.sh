#!/bin/bash

# Create symbolic link for the site
rm -rf /var/www/html
ln -s /app/app /var/www/html

app=$1
password=$2
apass=$3
oauth_app=$4
oauth_pass=$5
mandrill=$6
app_name=$7


# Create database
mysql -uroot -p${password} -e "CREATE DATABASE ${app}"

# Create user
mysql -uroot -p${password} -e "GRANT ALL PRIVILEGES ON ${app}.* TO ${app}@localhost IDENTIFIED BY '${apass}'"

# Go to app folder
cd /app/app

# Update config files
sed -i "s/%app_name%/${app}/g" environments/dev/common/config/main-local.php 
sed -i "s/%app_name%/${app}/g" environments/prod/common/config/main-local.php 
sed -i "s/%app_password%/${apass}/g" environments/dev/common/config/main-local.php 
sed -i "s/%app_password%/${mandrill}/g" common/config/main.php 
sed -i "s/%app_name%/${app_name}/g" common/config/main.php 

sed -i "s/%mandrill_api_key%/${apass}/g" environments/prod/common/config/main-local.php 

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
mysql -uroot -p${password} -e "USE ${app}; INSERT INTO oauth_clients (client_id, client_secret, redirect_uri, grant_types, scope, user_id) VALUES ('${oauth_app}', '${oauth_pass}', 'http://${app}', 'client_credentials password refresh_token', NULL, NULL);"