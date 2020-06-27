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
        $this->inventoryItem = new InventoryItem(
            InventoryItemId::new(), self::SKU
        );
    }

    /**
     * @test
     */
    public function has_default_qty_of_zero()
    {
        $this->assertEquals(false, $this->inventoryItem->isInStock());
        $this->assertEquals(0, $this->inventoryItem->qty());
    }

    /**
     * @test
     */
    public function has_unique_item_id()
    {
        $this->assertNotEquals(
            InventoryItemId::new(),
            $this->inventoryItem->aggregateRootId()
        );
    }

    /**
     * @test
     */
    public function can_change_qty()
    {
        $this->inventoryItem->addQty(1);
        $this->inventoryItem->addQty(2);
        $this->assertEquals(3, $this->inventoryItem->qty());
    }

    /**
     * @test
     */
    public function records_qty_change_events()
    {
        $this->inventoryItem->addQty(1);
        $this->inventoryItem->addQty(2);
        $events = $this->inventoryItem->releaseEvents();
        $this->assertEquals(
            [
                new QtyHasChanged(self::SKU, 1),
                new QtyHasChanged(self::SKU, 2),
            ],
            $events
        );
    }

    /**
     * @test
     */
    public function can_set_qty()
    {
        $this->inventoryItem->setQty(1);
        $this->inventoryItem->setQty(2);
        $this->assertEquals(2, $this->inventoryItem->qty());
    }

    /**
     * @test
     */
    public function records_qty_set_events()
    {
        $this->inventoryItem->setQty(10);
        $this->inventoryItem->setQty(5);
        $events = $this->inventoryItem->releaseEvents();
        $this->assertEquals(
            [
                new QtyHasBeenSet(self::SKU, 10),
                new QtyHasBeenSet(self::SKU, 5),
            ],
            $events
        );
    }

}
