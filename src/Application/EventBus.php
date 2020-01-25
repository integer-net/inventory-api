<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application;

class EventBus
{
    /**
     * @var callable[]
     */
    private $subscribers;

    public function subscribe(string $eventClass, callable $eventHandler): void
    {
        $this->subscribers[$eventClass][] = $eventHandler;
    }

    public function dispatch(object $event): void
    {
        foreach ($this->subscribers[get_class($event)] ?? [] as $subscriber) {
            $subscriber($event);
        }
    }
}