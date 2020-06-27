<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

use PHPUnit\Framework\TestCase;

class InventoryTest extends TestCase
{
    /**
     * @var Inventory
     */
    private $inventory;

    protected function setUp(): void
    {
        $this->inventory = Inventory::new(InventoryId::default());
    }

}