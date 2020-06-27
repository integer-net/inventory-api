<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

use IntegerNet\InventoryApi\Application\EventBus;
use PHPUnit\Framework\TestCase;

class InventoryTest extends TestCase
{
    /**
     * @var Inventory
     */
    private $inventory;
    /**
     * @var EventBus
     */
    private $eventBus;

    protected function setUp(): void
    {
        $this->inventory = new Inventory();
        $this->eventBus = new EventBus();
        $this->eventBus->subscribe(QtySet::class, $this->inventory->qtySetHandler());
        $this->eventBus->subscribe(QtyChanged::class, $this->inventory->qtyChangedHandler());
    }

    public function testItemIsCreatedOnQtySetEvent()
    {
        $this->markTestSkipped('Currently refactoring, event bus not compatible');
        $sku = 'number-of-the-beast';
        $this->eventBus->_dispatch(new QtySet($sku, 666));
        $this->assertEquals(
            new InventoryItem(InventoryItemId::new(), $sku, 666),
            $this->inventory->getBySku($sku)
        );
    }
    public function testIsUpdatedOnQtyChangeEvent()
    {
        $this->markTestSkipped('Currently refactoring, event bus not compatible');
        $sku = 'answer-to-life';

        $this->eventBus->_dispatch(new QtySet($sku, 40));
        $this->eventBus->_dispatch(new QtyChanged($sku, 2));
        $this->assertEquals(
            new InventoryItem(InventoryItemId::new(), $sku, 42),
            $this->inventory->getBySku($sku)
        );
    }
}