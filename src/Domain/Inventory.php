<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;
use IntegerNet\InventoryApi\Domain\Process\QtyChanged;

class Inventory implements AggregateRoot
{
    use AggregateRootBehaviour;

    /**
     * @var InventoryItem[]
     */
    private $items = [];

    public static function new(InventoryId $id): self
    {
        return new self($id);
    }

    public function hasSku(string $sku): bool
    {
        return isset($this->items[$sku]);
    }

    /**
     * @todo to ensure consistency, we must only allow changing items through the root, where the events are dispatched
     *       for queries like is_in_stock we should create a projection with a read model instead of using the inventory
     *       directly. See https://eventsauce.io/docs/reacting-to-events/projections-and-read-models/
     * @param string $sku
     * @return InventoryItem
     */
    public function getBySku(string $sku): InventoryItem
    {
        return $this->getItemBySku($sku);
    }

    private function getItemBySku(string $sku): InventoryItem
    {
        return $this->items[$sku];
    }
    /**
     * Creates new inventory item. There must be no existing item with the given SKU
     *
     * @param string $sku
     * @param int $qty
     */
    public function createItem(string $sku, int $qty): void
    {
        if ($this->hasSku($sku)) {
            throw new \DomainException('Tried to create an item that already exists (duplicate SKU)');
        }
        $newInventoryItem = new InventoryItem($sku, $qty);
        $this->items[$newInventoryItem->sku()] = $newInventoryItem;
    }

    public function addQty(string $sku, int $difference)
    {
        $this->recordThat(QtyChanged::withSkuAndDifference($sku, $difference));
    }

    /**
     * Used by EventSauce to apply/replay events
     */
    public function applyQtyChanged(QtyChanged $event)
    {
        $sku = $event->sku();
        if (! $this->hasSku($sku)) {
            $this->createItem($sku, 0);
        }
        $this->getItemBySku($sku)->addQty($event->difference());
    }
}