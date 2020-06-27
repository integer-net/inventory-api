<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Infrastructure;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\AggregateRootRepository;
use InvalidArgumentException;
use IntegerNet\InventoryApi\Domain\Inventory;
use IntegerNet\InventoryApi\Domain\InventoryId;
use IntegerNet\InventoryApi\Domain\InventoryRepository;

/**
 * Non-persistent inventory repository implementation with a single inventory for testing
 */
class InMemoryInventoryRepository implements InventoryRepository, AggregateRootRepository
{
    private $defaultInventory;

    public function __construct()
    {
        $this->defaultInventory = Inventory::new(InventoryId::default());
    }

    public function getDefaultInventory(): Inventory
    {
        return $this->defaultInventory;
    }

    public function retrieve(AggregateRootId $aggregateRootId): object
    {
        if ($aggregateRootId == InventoryId::default()) {
            return $this->getDefaultInventory();
        }
        throw new InvalidArgumentException('Multiple inventories are not supported yet');
    }

    public function persist(object $aggregateRoot)
    {
        // noop
    }

    public function persistEvents(AggregateRootId $aggregateRootId, int $aggregateRootVersion, object ...$events)
    {
        // noop
    }

}