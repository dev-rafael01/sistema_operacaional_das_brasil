<?php
session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../app/core/Router.php';


$url = $_GET['url'] ?? '';
$router = new Router();
$router->dispatch($url);




?>