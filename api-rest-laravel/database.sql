/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  USER
 * Created: 27/04/2022
 */

CREATE DATABASE IF NOT EXISTS api_rest_laravel;

USE api_rest_laravel;

CREATE TABLE users(
id INT(255) PRIMARY KEY AUTO_INCREMENT NOT NULL,
name VARCHAR(50) NOT NULL,
surname VARCHAR(100),
role VARCHAR(20),
email VARCHAR(255) NOT NULL,
password VARCHAR(255) NOT NULL,
description TEXT,
image VARCHAR(255),
created_at DATETIME DEFAULT NULL,
updated_at DATETIME DEFAULT NULL,
remember_token VARCHAR(255)
)ENGINE=InnoDb;

CREATE TABLE categories(
id INT(255) PRIMARY KEY AUTO_INCREMENT NOT NULL,
name VARCHAR(100),
created_at DATETIME DEFAULT NULL,
updated_at DATETIME DEFAULT NULL
)ENGINE=InnoDb;

CREATE TABLE posts(
id INT(255) PRIMARY KEY AUTO_INCREMENT NOT NULL,
user_id INT(255) NOT NULL,
category_id INT(255) NOT NULL,
title VARCHAR(255) NOT NULL,
content TEXT NOT NULL,
image VARCHAR(255),
created_at DATETIME DEFAULT NULL,
updated_at DATETIME DEFAULT NULL,
CONSTRAINT fk_users FOREIGN KEY(user_id) REFERENCES users(id),
CONSTRAINT fk_categories FOREIGN KEY(category_id) REFERENCES categories(id)
)ENGINE=InnoDb;