<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;

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

    public function getBySku(string $sku): InventoryItem
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
        $newInventoryItem = new InventoryItem(
            InventoryItemId::new(), $sku, $qty
        );
        $this->items[$newInventoryItem->sku()] = $newInventoryItem;
    }
}