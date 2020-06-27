<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application;

use EventSauce\EventSourcing\ConstructingAggregateRootRepository;
use EventSauce\EventSourcing\InMemoryMessageRepository;
use EventSauce\EventSourcing\SynchronousMessageDispatcher;
use IntegerNet\InventoryApi\Domain\Inventory;
use IntegerNet\InventoryApi\Domain\InventoryId;
use IntegerNet\InventoryApi\Infrastructure\InMemoryInventoryRepository;

/**
 * The router is the main application entry point. This factory is wiring its dependencies
 */
class RouterFactory
{
    public static function create(): Router
    {
        $inventoryRepository = new ConstructingAggregateRootRepository(
            Inventory::class,
            new InMemoryMessageRepository(),
            new SynchronousMessageDispatcher(
                //TODO add projectors for read model
            )
        );
        $defaultInventory = $inventoryRepository->retrieve(InventoryId::default());
        $router = new Router(
            new Controller\IsInStockController($defaultInventory),
            new Controller\EventController($defaultInventory)
        );
        return $router;
    }
}