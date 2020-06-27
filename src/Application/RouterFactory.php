<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application;

use IntegerNet\InventoryApi\Domain\Inventory;

/**
 * The router is the main application entry point. This factory is wiring its dependencies
 */
class RouterFactory
{
    public static function create(): Router
    {
        $inventory = new Inventory();
        $router = new Router(
            new Controller\IsInStockController($inventory),
            new Controller\EventController($inventory)
        );
        return $router;
    }
}