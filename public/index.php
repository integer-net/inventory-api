<?php
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response;
use React\Http\Server;

require __DIR__ . '/../vendor/autoload.php';

$loop = Factory::create();

$server = new Server(function (ServerRequestInterface $request) {
    $router = new \IntegerNet\InventoryApi\Application\Router();
    return $router->handle($request);
});

//$port = "0"; // dynamically allocate
$port = "8080";
$socket = new \React\Socket\Server(isset($argv[1]) ? $argv[1] : "0.0.0.0:{$port}", $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
