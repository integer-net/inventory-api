<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

use IntegerNet\InventoryApi\Domain\Process\QtyChanged as QtyHasChanged;
use IntegerNet\InventoryApi\Domain\Process\QtySet as QtyHasBeenSet;
use PHPUnit\Framework\TestCase;

class InventoryItemTest extends TestCase
{
    private const SKU = 'sku-1';

    private InventoryItem $inventoryItem;

    protected function setUp(): void
    {
        $this->inventoryItem = new InventoryItem(self::SKU);
    }

    /**
     * @test
     */
    public function has_default_qty_of_zero()
    {
        $this->assertEquals(false, $this->inventoryItem->isInStock());
        $this->assertEquals(0, $this->inventoryItem->qty());
    }

}
