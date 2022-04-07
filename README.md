# Dance Of Blades

Simple website about the theme of a fictional computer game

## Table of Contents
* [Genera Info](#general-info)
* [Technologies Used](#technologies)
* [Launch](#launch)
* [Features](#features)

## General info

The purpose of writing this project was to get fully functional website about game theme using Symfony 4 framework and other external tools.
To allow me to get acquainted myself with the **Symfony framework** environment and related tools.

## Technologies
Project is created with:
* [php 7.4](https://www.php.net/)
* [symfony 4](https://react-redux.js.org/)
* [MsSQL server](https://www.microsoft.com/pl-pl/sql-server/sql-server-2019)
* [Bootstrap](https://getbootstrap.com/)
* [Mailtrap](https://mailtrap.io/)

## Launch
#### Common use 

To run this project install [php](https://windows.php.net/download#php-7.4) interpreter and [symfony local server](https://symfony.com/doc/current/setup/symfony_server.html). 
Next move to the directory where you have composer.json and update dependencies:
```
$ composer install
```
Finally run your server:
```
$ symfont server:start --no-tls
```
Now everything is prepared. You can open your browser and go to localhost website. 

#### With mercure feature
To run this project with [Mercure](https://mercure.rocks/) you need to change environment to [Xampp](https://www.apachefriends.org/pl/index.html) and move project files into `htdocs` folder. 

Next [install Mercure](https://mercure.rocks/docs/hub/install) package on your computer and add source path to PATH system variable depending on your operating system.
Now run service in your bash using:
```
$ $env:MERCURE_PUBLISHER_JWT_KEY='mercureSecretToken'; $env:MERCURE_SUBSCRIBER_JWT_KEY='mercureSecretToken'; mercure run -config Caddyfile.dev
```
Set `mercureSecretToken` value on your secret key which is the same as `MERCURE_JWT_SECRET` variable in `.env` project file.

Now everything is prepared. You can open your browser and go to localhost website. 

## Features
* Menu
* Login and register module
* Email authentication
* Friends module - using [Mercure](https://mercure.rocks/) to broadcast new invitations in real-time from server
* Use [Redis](https://redis.io/) to store session

#### To do
* Use [EasyAdmin](https://symfony.com/bundles/EasyAdminBundle/current/index.html) to admin module
* Use [RabbitMQ](https://www.rabbitmq.com/) to asynchronous messaging
* Move to [docker](https://www.docker.com/) environment
