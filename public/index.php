<?php

require __DIR__ . '/../vendor/autoload.php';

use app\DiscordCommads;
use Dotenv\Dotenv;

date_default_timezone_set('America/Sao_Paulo');

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$bot = new DiscordCommads();
$bot->run(false);
