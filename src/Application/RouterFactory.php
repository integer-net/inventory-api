<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application;

use EventSauce\EventSourcing\ConstructingAggregateRootRepository;
use EventSauce\EventSourcing\InMemoryMessageRepository;
use EventSauce\EventSourcing\SynchronousMessageDispatcher;
use IntegerNet\InventoryApi\Application\Consumer\InStockProjection;
use IntegerNet\InventoryApi\Application\Service\ChangeQty;
use IntegerNet\InventoryApi\Application\Service\SetQty;
use IntegerNet\InventoryApi\Domain\InStockReadModel;
use IntegerNet\InventoryApi\Domain\Inventory;
use IntegerNet\InventoryApi\Domain\InventoryId;

/**
 * The router is the main application entry point. This factory is wiring its dependencies
 */
class RouterFactory
{
    private Inventory $defaultInventory;

    public function create(): Router
    {
        $inStockReadModel = new InStockReadModel();
        $inventoryRepository = new ConstructingAggregateRootRepository(
            Inventory::class,
            new InMemoryMessageRepository(),
            new SynchronousMessageDispatcher(
                new InStockProjection($inStockReadModel)
            )
        );
        /**
         * @var Inventory $defaultInventory
         */
        $defaultInventory = $inventoryRepository->retrieve(InventoryId::default());
        $this->defaultInventory = $defaultInventory;
        $router = new Router(
            new Controller\IsInStockController($inStockReadModel),
            new Controller\InventoryItemPutController($inventoryRepository, $this->defaultInventory, new SetQty()),
            new Controller\InventoryItemQtyPatchController(
                $inventoryRepository,
                $this->defaultInventory,
                new ChangeQty()
            ),
            new Controller\EventController($inventoryRepository, $this->defaultInventory)
        );
        return $router;
    }

    /**
     * Retrieve the default inventory (only after create() has been called)
     *
     * Intended for tests
     *
     * @return Inventory
     */
    public function getDefaultInventory(): Inventory
    {
        return $this->defaultInventory;
    }
}
