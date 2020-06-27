<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

use IntegerNet\InventoryApi\Domain\Process\QtyChanged as QtyHasChanged;
use IntegerNet\InventoryApi\Domain\Process\QtySet as QtyHasBeenSet;
use IntegerNet\InventoryApi\Application\SubscribableMessageDispatcher;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\MessageDispatchingEventDispatcher;
use PHPUnit\Framework\TestCase;

class InventoryTest extends TestCase
{
    /**
     * @var Inventory
     */
    private $inventory;

    private MessageDispatchingEventDispatcher $eventDispatcher;

    protected function setUp(): void
    {
        $this->inventory = new Inventory();
        $messageDispatcher = new SubscribableMessageDispatcher();
        $messageDispatcher->subscribe(QtyHasBeenSet::class, $this->inventory->qtySetHandler());
        $messageDispatcher->subscribe(QtyHasChanged::class, $this->inventory->qtyChangedHandler());
        $this->eventDispatcher = new MessageDispatchingEventDispatcher(
            $messageDispatcher,
        );
    }

    public function testItemIsCreatedOnQtySetEvent()
    {
        $this->markTestSkipped('Currently refactoring, event bus not compatible');
        $sku = 'number-of-the-beast';
        $this->eventDispatcher->dispatch(new QtyHasBeenSet($sku, 666));
        $this->assertEquals(
            (new InventoryItem(InventoryItemId::new(), $sku, 666)),
            $this->inventory->getBySku($sku)
        );
    }
    public function testIsUpdatedOnQtyChangeEvent()
    {
        $this->markTestSkipped('Currently refactoring, event bus not compatible');
        $sku = 'answer-to-life';

        $this->eventDispatcher->dispatch(new QtyHasBeenSet($sku, 40));
        $this->eventDispatcher->dispatch(new QtyHasChanged($sku, 2));
        $this->assertEquals(
            new InventoryItem(InventoryItemId::new(), $sku, 42),
            $this->inventory->getBySku($sku)
        );
    }
}