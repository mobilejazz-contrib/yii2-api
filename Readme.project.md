# README

## Requirements

* [Docker](https://www.docker.com/) (we recommend you to use [Kitematic](https://kitematic.com/))


## Setup project (using Docker)

To setup the project you should run the following script

	./build <environment> <branch>

This script will generate a Docker machine with Ubuntu, MySQL and all the configuration needed to run the project.

## Project Overview

The project has the following features:

* A Yii2 application with the following components
	* api -> the REST Api (`http://<docker_ip>/api`)
	* backend -> the admin site (`http://<docker_ip>/admin`)
	* fronted -> the public site (`http://<docker_ip>/`)
* OAuth2 server
* i18 support
* Mandrill for sending emails
* User management:
	* Basic RBAC implemented using roles (implemented roles: USER, ADMIN)
	* User admin backoffice
	* Backend protected by roles
	* Password reset features with email sending via Mandrill (also available in the Rest API)
	* User Profile Entity
* XDebug configured in the server
* Adminer installed (`http://<docker_ip>/adminer`) *Note: Mysql root access is disabled*
* Gii is available here: `http://<docker_ip>/admin/gii`
* Push Notifications support via Parse