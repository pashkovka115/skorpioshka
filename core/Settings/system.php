<?php
defined('ACCESS') or die('Access denied');

define('SITE_PATH', dirname(__DIR__, 2));
const MODULES = SITE_PATH . '/modules';
const LOGS_PATH = SITE_PATH . '/logs';
const MIGRATIONS_PATH = SITE_PATH . '/database/migrations';
const SEED_PATH = SITE_PATH . '/database/seeds';
const MODULES_STORAGE = SITE_PATH . '/storage/modules';
const CONFIG_PATH = SITE_PATH . '/config';
const LANG_PATH = SITE_PATH . '/core/lang';