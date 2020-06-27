<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application;

use EventSauce\EventSourcing\ConstructingAggregateRootRepository;
use EventSauce\EventSourcing\InMemoryMessageRepository;
use EventSauce\EventSourcing\SynchronousMessageDispatcher;
use IntegerNet\InventoryApi\Application\Consumer\InStockProjection;
use IntegerNet\InventoryApi\Domain\InStockReadModel;
use IntegerNet\InventoryApi\Domain\Inventory;
use IntegerNet\InventoryApi\Domain\InventoryId;

/**
 * The router is the main application entry point. This factory is wiring its dependencies
 */
class RouterFactory
{
    public static function create(): Router
    {
        $inStockReadModel = new InStockReadModel();
        $inventoryRepository = new ConstructingAggregateRootRepository(
            Inventory::class,
            new InMemoryMessageRepository(),
            new SynchronousMessageDispatcher(
                new InStockProjection($inStockReadModel)
            )
        );
        $defaultInventory = $inventoryRepository->retrieve(InventoryId::default());
        $router = new Router(
            new Controller\IsInStockController($inStockReadModel),
            new Controller\EventController($inventoryRepository, $defaultInventory)
        );
        return $router;
    }
}