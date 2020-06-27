<?php
declare(strict_types=1);

namespace unit\Domain;

use IntegerNet\InventoryApi\Domain\InStockReadModel;
use PHPUnit\Framework\TestCase;

class InStockReadModelTest extends TestCase
{
    private InStockReadModel $inventoryReadModel;

    protected function setUp(): void
    {
        $this->inventoryReadModel = new InStockReadModel();
    }

    /**
     * @test
     */
    public function item_is_in_stock_with_positive_qty()
    {
        $this->inventoryReadModel->updateQty('sku-1', 1);
        $this->assertTrue($this->inventoryReadModel->isInStock('sku-1'), 'Item with QTY 1 should be in stock' );
    }
    /**
     * @test
     */
    public function item_is_out_of_stock_with_negative_qty()
    {
        $this->inventoryReadModel->updateQty('sku-1', -1);
        $this->assertFalse($this->inventoryReadModel->isInStock('sku-1'), 'Item with QTY 1 should be out of stock' );
    }
}
