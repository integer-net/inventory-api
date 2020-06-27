<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application;

use IntegerNet\InventoryApi\Application\Service\SetQty;
use IntegerNet\InventoryApi\Domain\Inventory;
use IntegerNet\InventoryApi\Domain\InventoryId;
use PHPUnit\Framework\TestCase;

class SetQtyApplicationServiceTest extends TestCase
{
    private Inventory $inventory;
    private SetQty $setQty;

    protected function setUp(): void
    {
        $this->setQty = new SetQty();
        $this->inventory = Inventory::new(InventoryId::default());
    }

    /**
     * @test
     */
    public function sets_qty_in_inventory()
    {
        $this->setQty->execute($this->inventory, 'sku-1', 5);
        $this->setQty->execute($this->inventory, 'sku-1', 10);
        $this->assertEquals(10, $this->inventory->getItemBySku('sku-1')->qty(), 'QTY should be saved in inventory');
    }
}