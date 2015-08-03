# Yii REST API template generator

## Description

This generator will create a Yii application with the following features:

* A docker machine with apache and mysql already setup
* A Yii application with the following components
	* api -> the REST Api (http://<docker_ip>/api)
	* backend -> the admin site (http://<docker_ip>/admin)
	* fronted -> the public site (http://<docker_ip>/)
* OAuth2 server
* i18 support
* Mandrill for sending emails
* User management:
	* Basic RBAC implemented using roles (implemented roles: USER, ADMIN)
	* User admin backoffice
	* Backend protected by roles
	* Password reset features with email sending via Mandrill (also available in the Rest API)
* XDebug configured in the server
* Adminer installed (http://<docker_ip>/adminer) *Note: Mysql root access is disabled*

## Installation

### Prerequisites


* [Docker](https://www.docker.com/) (we recommend you to use [Kitematic](https://kitematic.com/))
* A [Mandrill](http://mandrill.com/) account, it's required to send emails to the users
 


### How to install

You just need to run the following command

	./build <image_name> <mysql_root_password> <mysql_app_password> <oauth_client_name> <oauth_client_password> <mandrill_key> <app_name>
	
* **<image_name>**: Image of the docker image to create. This will be used to create the mysql database and database user.
* **<mysql_root_password>**: Mysql root user will have this password.
* **<mysql_app_password>**: The mysql password to be used by the app.
* **<oauth_client_name>**: Name of the oauth client
* **<oauth_client_password>**:Password to be used by the oauth client
* **<mandrill_key>**: Mandrill key to be used to send emails
* **<app_name>**: The name of the app, mostly used to show it in the menus and emails


## After installation steps

* Edit `common/config/params.php` to put the correct email adresses
* Edit the emails tempaltes in `emails` folder and upload them to Mandrill
* The app has support for English, if you want to add a new language:
	* Edit `common/config/main.php` and add your languages
	* Create a new folder for your language in `common/messages`
	* Add the email templates to Mandrill for the new language


## Useful classes

### RestActiveController

You can use this class as a base class for you REST controllers. It adds the following behaviors:

* OAuth authentication
* No-cache policy by default
* It has a `checkOwner` function that you can use to a better acces control (see `UserController` to learn how to use it).

`checkOwner` uses `user_id` property to check the ownership of an object, so if you use another field you will need to write your own code or change it.

### AdminController

* You can use this class as a base class for all your backend controllers, it checks that the user is an admin to allow access (or the object owner)
