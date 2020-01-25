<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi;

class CallableMock
{
    public $events = [];

    public function __invoke($event)
    {
        $this->events[] = $event;
    }
}