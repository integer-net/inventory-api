<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageDispatchingEventDispatcher;

/**
 * Message dispatcher used by the event sauce event dispatcher. Event handlers can subscribe to events by event class
 *
 * Instantiate:
 *
 *     $messageDispatcher = new SubscribableMessageDispatcher();
 *
 *     // ... subscribe to events at $messageDispatcher, here or later ...
 *
 *     $eventDispatcher = new MessageDispatchingEventDispatcher($messageDispatcher);
 *
 *     // dispatch events with $eventDispatcher
 *
 */
class SubscribableMessageDispatcher implements MessageDispatcher
{
    /**
     * @var callable[]
     */
    private $subscribers;

    public function subscribe(string $eventClass, callable $eventHandler): void
    {
        $this->subscribers[$eventClass][] = $eventHandler;
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