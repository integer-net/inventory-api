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
        $sku = 'number-of-the-beast';
        $this->eventBus->dispatch(new QtySet($sku, 666));
        $this->assertEquals(
            new InventoryItem($sku, 666),
            $this->inventory->getBySku($sku)
        );
    }
    public function testIsUpdatedOnQtyChangeEvent()
    {
        $sku = 'answer-to-life';

        $this->eventBus->dispatch(new QtySet($sku, 40));
        $this->eventBus->dispatch(new QtyChanged($sku, 2));
        $this->assertEquals(
            new InventoryItem($sku, 42),
            $this->inventory->getBySku($sku)
        );
    }
}