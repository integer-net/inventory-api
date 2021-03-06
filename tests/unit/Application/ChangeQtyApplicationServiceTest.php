<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application;

use IntegerNet\InventoryApi\Application\Service\ChangeQty;
use IntegerNet\InventoryApi\Domain\Inventory;
use IntegerNet\InventoryApi\Domain\InventoryId;
use PHPUnit\Framework\TestCase;

class ChangeQtyApplicationServiceTest extends TestCase
{
    private Inventory $inventory;
    private ChangeQty $changeQty;

    protected function setUp(): void
    {
        $this->changeQty = new ChangeQty();
        $this->inventory = Inventory::new(InventoryId::default());
    }

    /**
     * @test
     */
    public function changes_qty_in_inventory()
    {
        $this->changeQty->execute($this->inventory, 'sku-1', 10);
        $this->changeQty->execute($this->inventory, 'sku-1', -3);
        $this->assertEquals(7, $this->inventory->getItemBySku('sku-1')->qty(), 'QTY should be saved and updated in inventory');
    }
}