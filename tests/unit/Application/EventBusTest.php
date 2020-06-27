<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application;

use IntegerNet\InventoryApi\CallableMock;
use IntegerNet\InventoryApi\Domain\Process\QtyChanged;
use IntegerNet\InventoryApi\Domain\Process\QtySet;
use PHPUnit\Framework\TestCase;

class EventBusTest extends TestCase
{
    /**
     * @var EventBus
     */
    private $eventBus;

    protected function setUp(): void
    {
        $this->eventBus = new EventBus();
    }

    public function testDispatchesEventsToObservers()
    {
        $this->givenSubscribedObserver($observer, QtyChanged::class);
        $this->givenQtyChangedEvent($event);
        $this->whenEventIsDispatched($event);
        $this->thenObserverHasBeenCalledWith($event, $observer);
    }

    public function testDispatchesEventsOnlyToSubscribedObservers()
    {
        $this->givenSubscribedObserver($observer, QtySet::class);
        $this->givenQtyChangedEvent($event);
        $this->whenEventIsDispatched($event);
        $this->thenObserverHasNotBeenCalled($observer);
    }

    private function givenQtyChangedEvent(&$event): void
    {
        $event = new QtyChanged('xxx', 1);
    }

    private function givenSubscribedObserver(&$observerMock, $eventClass): void
    {
        $observerMock = new CallableMock();
        $this->eventBus->subscribe($eventClass, $observerMock);
    }

    private function whenEventIsDispatched($event): void
    {
        $this->eventBus->_dispatch($event);
    }

    private function thenObserverHasBeenCalledWith($event, CallableMock $observer): void
    {
        $this->assertEquals([$event], $observer->events);
    }

    private function thenObserverHasNotBeenCalled(CallableMock $observer)
    {
        $this->assertEquals([], $observer->events);
    }

}
