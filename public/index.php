<?php

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Server;

require __DIR__ . '/../vendor/autoload.php';

$loop = Factory::create();

$eventBus = new \IntegerNet\InventoryApi\Application\EventBus();
$inventory = new \IntegerNet\InventoryApi\Domain\Inventory();
$eventBus->subscribe(\IntegerNet\InventoryApi\Domain\QtyChanged::class, $inventory->qtyChangedHandler());
$eventBus->subscribe(\IntegerNet\InventoryApi\Domain\QtySet::class, $inventory->qtySetHandler());
$router = new \IntegerNet\InventoryApi\Application\Router(
    new \IntegerNet\InventoryApi\Application\Controller\IsInStockController($inventory),
    new \IntegerNet\InventoryApi\Application\Controller\EventController($eventBus)
);

$server = new Server(function (ServerRequestInterface $request) use ($router) {
    return $router->handle($request);
});

//$port = "0"; // dynamically allocate
$port = "8080";
$socket = new \React\Socket\Server(isset($argv[1]) ? $argv[1] : "0.0.0.0:{$port}", $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
