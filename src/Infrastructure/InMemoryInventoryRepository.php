<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Infrastructure;

use IntegerNet\InventoryApi\Domain\Inventory;
use IntegerNet\InventoryApi\Domain\InventoryRepository;

/**
 * Non-persistent inventory repository implementation with a single inventory for testing
 */
class InMemoryInventoryRepository implements InventoryRepository
{
    private $defaultInventory;

    public function __construct()
    {
        $this->defaultInventory = new Inventory();
    }

    public function getDefaultInventory(): Inventory
    {
        return $this->defaultInventory;
    }

}