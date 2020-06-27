<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

use IntegerNet\InventoryApi\Domain\Process\QtyChanged as QtyHasChanged;
use PHPUnit\Framework\TestCase;

class InventoryTest extends TestCase
{
    private const SKU = 'sku-1';
    /**
     * @var Inventory
     */
    private $inventory;

    protected function setUp(): void
    {
        $this->inventory = Inventory::new(InventoryId::default());
    }

    /**
     * @test
     */
    public function can_change_item_qty()
    {
        $this->inventory->addQty(self::SKU, 12);
        $this->inventory->addQty(self::SKU, 30);
        $this->assertEquals(42, $this->inventory->getBySku(self::SKU)->qty());
    }

    /**
     * @test
     */
    public function records_qty_change_events()
    {
        $this->inventory->addQty(self::SKU, 1);
        $this->inventory->addQty(self::SKU, 2);
        $events = $this->inventory->releaseEvents();
        $this->assertEquals(
            [
                new QtyHasChanged(self::SKU, 1),
                new QtyHasChanged(self::SKU, 2),
            ],
            $events
        );
    }

}