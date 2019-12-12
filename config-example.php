<?php
defined('ACCESS') or die('Access denied');

//const HTTP = 'http';
define('SITE_URL', 'http://stack-developers.loc/');
const APP_KEY = 'iuy6ftrduzxidviohviurebkewrfrfgu';

const DEBUG = true;

const DRIVER = 'mysql';
const HOST = 'localhost';
const USER = 'user_name';
const PASS = 'password';
const DB_NAME = 'database_name';

const MAIL_DRIVER = 'sendmail'; // sendmail | smtp
const MAIL_HOST = 'localhost'; // localhost | smtp.gmail.com | smtp.mail.ru
const MAIL_PORT = 25; //587, 465, 25
const MAIL_USERNAME = 'email@domen.com'; // email@domen.com
const MAIL_PASSWORD = ''; // password | ''
const MAIL_RECIPIENT = 'email@domen.com'; // email@domen.com
const MAIL_SECURE = ''; // ssl, tls, ''
const MAIL_AUTH = true;
