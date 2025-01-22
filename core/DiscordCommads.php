<?php

namespace app;

use Discord\Discord;
use Discord\WebSockets\Intents;
use Discord\Parts\Channel\Message;
use React\EventLoop\Factory;

class DiscordCommads {
    private $discord;
    private $loop;
    private string $token;
    private string $idGrupoDev;
    private string $idGrupoLapou;

    public function __construct() {
        $this->loop = Factory::create();

        $this->token        = $_ENV['DISCORD_TOKEN'];
        $this->idGrupoDev   = $_ENV['ID_GRUPO_DEV'];
        $this->idGrupoLapou = $_ENV['ID_GRUPO_LAPOU'];

        $this->discord = new Discord([
            'token' => $this->token,
            'intents' => Intents::getDefaultIntents() | Intents::MESSAGE_CONTENT,
        ]);
    }

    public function run($all) {
        $this->discord->on('ready', function (Discord $discord) use ($all) {
            echo 'Bot is ready!', PHP_EOL;

            echo 'Número de servidores conectados: ' . count($discord->guilds) . PHP_EOL;

            foreach ($discord->guilds as $guild) {
                echo "Conectado ao servidor: {$guild->name} ({$guild->id})\n";
            }

            if ($all) {
                foreach ($discord->guilds as $guild) {
                    $this->loop->addPeriodicTimer(60, function () use ($guild) {
                        $this->enviarMensagemAutomaticoTodosGrupos($guild);
                    });
                }
            } else {
                $this->loop->addPeriodicTimer(60, function () use ($discord) {
                    $this->enviarMensagemAutomaticoGrupoEspecifico();
                });
            }

            $discord->on('message', function (Message $message, Discord $discord) {
                $this->commandsMessageBot($message);
            });
        });

        $this->loop->run();
    }

    private function enviarMensagemAutomaticoTodosGrupos($guild) {
        $generalChannel = $guild->channels->filter(function ($channel) {
            return $channel->type == \Discord\Parts\Channel\Channel::TYPE_TEXT;
        })->first();
        exit;

        // IDs dos usuários que você deseja mencionar
        $userIdList = [

        ];

        //pegar randomicamente
        $userId = $userIdList[array_rand($userIdList)];
        //marcando eles no chat
        $namesDefault = "<@{$userId}>";

        $horaAtual   = date('H:i');
        $minutoAtual = date('i');

        if ($minutoAtual == '00') {
            $generalChannel->sendMessage("Agora são {$horaAtual}, {$namesDefault}.");
        }
    }

    private function enviarMensagemAutomaticoGrupoEspecifico() {
        $channel = $this->discord->getChannel($this->idGrupoDev);

        if ($channel === null) {
            echo "Canal com ID {$this->idGrupoDev} não encontrado.", PHP_EOL;

            return;
        }

        $namesDefault = "@everyone";

        $horaAtual   = date('H:i');
        $minutoAtual = date('i');

        $channel->sendMessage("Agora são {$horaAtual}, {$namesDefault}.");
    }

    private function commandsMessageBot($message) {
        if ($message->author->bot) {
            return;
        }

        if ($message->content == '!PHP') {
            $message->channel->sendMessage('Você sabia que dá para criar um bot com PHP?');
        }

        if ($message->content == '!temperatura') {
            $data = new WeatherChecker;

            $cityResponse = $data->getWeather('Florianópolis');
            $responseTemp = $cityResponse['temperature'] > 30 ? 'TÁ MT CALOR PITBULL' : 'tolerável dog, tolerável';

            $message->channel->sendMessage('Está ' . $cityResponse['temperature'] . ' °C em '. $cityResponse['city'] .', '. $responseTemp .'');
        }

        if ($message->content == '!foto') {
            $message->channel->sendMessage('Decidi tirar esta selfie porque, ao me olhar no espelho, notei uma semelhança impressionante com o Kratos, personagem de God of War.');

            $imagePath = realpath(__DIR__ . '/../assets/img/IMG-20200510-WA0002.png');

            if ($imagePath === false) {
                $message->channel->sendMessage('Erro ao encontrar a imagem.');

                return;
            }

            $message->channel->sendFile($imagePath, 'IMG-20200510-WA0002.png', '');
        }
    }
}

?>