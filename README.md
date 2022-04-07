# Dance Of Blades - docker edition

Simple website about the theme of a fictional computer game

**This branch is fixed for run this project onto docker using [specially prepared environment](https://github.com/kabix09/DoB-environoment)**

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
Follow the instruction described in this project - [DoB environment](https://github.com/kabix09/DoB-environoment)


## Features
* Menu
* Login and register module
* Email authentication - using [RabbitMQ](https://www.rabbitmq.com/) to asynchronous messaging
* Friends module - using [Mercure](https://mercure.rocks/) to broadcast new invitations in real-time from server
* Use [Redis](https://redis.io/) to store session

#### To do
* Use [EasyAdmin](https://symfony.com/bundles/EasyAdminBundle/current/index.html) to admin module
* Use [RabbitMQ](https://www.rabbitmq.com/) to asynchronous messaging