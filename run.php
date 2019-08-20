<?php

use React\Socket\ConnectionInterface;

require_once 'vendor/autoload.php';

$loop = React\EventLoop\Factory::create();


$currentSocketServer = new React\Socket\Server(8080, $loop);
$limitingServer = new \React\Socket\LimitingServer($currentSocketServer, null);

$currentSocketServer->on(
    'connection',
    static function (ConnectionInterface $newConnection) use ($limitingServer) {
        if (!$newConnection->isReadable() || !$newConnection->isWritable()) {
            return;
        }

        echo sprintf(
                'New incoming connection %s. Total connections: %s', $newConnection->getRemoteAddress(),
                count($limitingServer->getConnections())
            ) . PHP_EOL;

        $welcomeMessage = sprintf('Hi new connection: %s', $newConnection->getRemoteAddress()) . PHP_EOL;
        $newConnection->write($welcomeMessage);

        $newConnection->on(
            'data',
            static function ($data) use ($newConnection, $limitingServer) {
                $allSystemConnections = $limitingServer->getConnections();
                foreach ($allSystemConnections as $currentConnection) {
                    if ($currentConnection === $newConnection) {
                        continue;
                    }

                    $dataToShare = sprintf('%s says: %s%s', $newConnection->getRemoteAddress(), $data, PHP_EOL);
                    $currentConnection->write($dataToShare);
                }
            }
        );
    }
);

echo sprintf('Server running at %s', 'http://localhost:8080');

$loop->run();