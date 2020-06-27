<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

/**
 * Read-only inventory item, can be used safely outside of the Inventory aggregate
 *
 * @todo InventoryItem can be considered a value object, make it immutable and we don't need the separate read model anymore
 */
class InventoryItemReadModel implements InventoryItemInterface
{
    private InventoryItem $inventoryItem;

    public function __construct(InventoryItem $inventoryItem)
    {
        $this->inventoryItem = $inventoryItem;
    }

    public function isInStock(): bool
    {
        return $this->inventoryItem->isInStock();
    }

    public function qty(): int
    {
        return $this->inventoryItem->qty();
    }

    public function sku(): string
    {
        return $this->inventoryItem->sku();
    }

}