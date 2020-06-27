<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application;

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
        $eventBus = new EventBus();
        $inventory = new Inventory();
        $eventBus->subscribe(QtyChanged::class, $inventory->qtyChangedHandler());
        $eventBus->subscribe(QtySet::class, $inventory->qtySetHandler());
        $router = new Router(
            new Controller\IsInStockController($inventory),
            new Controller\EventController($eventBus)
        );
        return $router;
    }
}