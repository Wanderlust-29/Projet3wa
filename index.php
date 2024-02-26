<?php

// charge l'autoload de composer
require "vendor/autoload.php";

// charge le contenu du .env dans $_ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Utilise le Router
require_once 'services/Router.php';

$router = new Router();
$router->handleRequest($_GET);