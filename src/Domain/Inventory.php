<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

use IntegerNet\InventoryApi\Domain\Process\QtyChanged as QtyHasChanged;
use IntegerNet\InventoryApi\Domain\Process\QtySet as QtyHasBeenSet;

class Inventory
{
    /**
     * @var InventoryItem[]
     */
    private $items = [];

    /**
     * Event handler that takes a QtyChanged event and updates the inventory
     *
     * @todo extract each event handler to a service
     * @return callable
     */
    public function qtyChangedHandler(): callable
    {
        return function (QtyHasChanged $event) {
            $this->getBySku($event->sku())->addQty($event->difference());
        };
    }

    /**
     * Event handler that takes a QtySet event and updates the inventory
     *
     * @return callable
     */
    public function qtySetHandler(): callable
    {
        return function (QtyHasBeenSet $event) {
            if (! $this->hasSku($event->sku())) {
                $this->items[$event->sku()] = new InventoryItem(
                    InventoryItemId::new(),
                    $event->sku(), 0
                );
            }
            $this->getBySku($event->sku())->setQty($event->qty());
        };
    }

    public function hasSku(string $sku): bool
    {
        return isset($this->items[$sku]);
    }

    public function getBySku(string $sku): InventoryItem
    {
        return $this->items[$sku];
    }
}