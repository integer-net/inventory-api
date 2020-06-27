<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application;

use IntegerNet\InventoryApi\Domain\Inventory;
use IntegerNet\InventoryApi\Infrastructure\InMemoryInventoryRepository;

/**
 * The router is the main application entry point. This factory is wiring its dependencies
 */
class RouterFactory
{
    public static function create(): Router
    {
        $inventoryRepository = new InMemoryInventoryRepository();
        $router = new Router(
            new Controller\IsInStockController($inventoryRepository),
            new Controller\EventController($inventoryRepository)
        );
        return $router;
    }
}