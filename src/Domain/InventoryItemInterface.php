<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

/**
 * Public interface for inventory items, only query methods
 */
interface InventoryItemInterface
{
    public function isInStock(): bool;

    public function qty(): int;

    public function sku(): string;
}