<?php

use IntegerNet\InventoryApi\Application\RouterFactory;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory as EventLoopFactory;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;

require __DIR__ . '/../vendor/autoload.php';

$loop = EventLoopFactory::create();
$router = (new RouterFactory)->create();
$server = new HttpServer(function (ServerRequestInterface $request) use ($router) {
    return $router->handle($request);
});

$port = "0"; // dynamically allocate
$socket = new SocketServer($argv[1] ?? "0.0.0.0:{$port}", $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
