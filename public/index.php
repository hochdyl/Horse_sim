<?php

use App\Core\Autoloader\Autoloader;
use App\Core\Config\Config;
use App\Core\System\App;

// Check the PHP version
if (phpversion() <= 8.0) die("Upgrade your PHP version to 8.0 !");

require_once dirname(__DIR__) . '/Core/Autoloader/Autoloader.php';
require_once dirname(__DIR__) . '/Core/Config/Config.php';

// Charger le fichier de config
Config::loadConfig();

// Charger le chargement auto des classes
Autoloader::register();

// Charger toute les routes
App::run();