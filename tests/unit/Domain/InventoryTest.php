<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

use IntegerNet\InventoryApi\Infrastructure\InMemoryInventoryRepository;
use PHPUnit\Framework\TestCase;

class InventoryTest extends TestCase
{
    /**
     * @var Inventory
     */
    private $inventory;

    protected function setUp(): void
    {
        $this->inventory = (new InMemoryInventoryRepository())->getDefaultInventory();
    }

}