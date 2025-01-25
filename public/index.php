<?php

require __DIR__ . '/../vendor/autoload.php';

use app\controller\DiscordCommadsController;
use Dotenv\Dotenv;

date_default_timezone_set('America/Sao_Paulo');

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

do {
    $input = readline("Gostaria de adicionar o parâmetro run() como true ou false? (true/false): ");

    $run = filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

    if ($run === null) {
        echo "Entrada inválida. Por favor, insira 'true' ou 'false'.\n";
    }
} while ($run === null);

$bot = new DiscordCommadsController();
$bot->enviarMensagem($run);
