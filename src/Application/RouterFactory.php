<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application;

use EventSauce\EventSourcing\MessageDispatchingEventDispatcher;
use IntegerNet\InventoryApi\Domain\Inventory;
use IntegerNet\InventoryApi\Domain\Process\QtyChanged;
use IntegerNet\InventoryApi\Domain\Process\QtySet;

/**
 * The router is the main application entry point. This factory is wiring its dependencies
 */
class RouterFactory
{
    public static function create(): Router
    {
        $messageDispatcher = new SubscribableMessageDispatcher();
        $inventory = new Inventory();
        $messageDispatcher->subscribe(QtyChanged::class, $inventory->qtyChangedHandler());
        $messageDispatcher->subscribe(QtySet::class, $inventory->qtySetHandler());
        $eventDispatcher = new MessageDispatchingEventDispatcher($messageDispatcher);
        $router = new Router(
            new Controller\IsInStockController($inventory),
            new Controller\EventController($eventDispatcher)
        );
        return $router;
    }
}