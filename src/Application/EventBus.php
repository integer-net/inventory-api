<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;

class EventBus implements MessageDispatcher
{
    /**
     * @var callable[]
     */
    private $subscribers;

    public function subscribe(string $eventClass, callable $eventHandler): void
    {
        $this->subscribers[$eventClass][] = $eventHandler;
    }

    /**
     * @deprecated use dispatch() of MessageDispatcher interface
     */
    public function _dispatch(object $event): void
    {
        $this->dispatchEvent($event);
    }

    public function dispatch(Message ...$messages)
    {
        foreach ($messages as $message) {
            $this->dispatchEvent($message->event());
        }
    }

    private function dispatchEvent(object $event): void
    {
        foreach ($this->subscribers[get_class($event)] ?? [] as $subscriber) {
            $subscriber($event);
        }
    }

}