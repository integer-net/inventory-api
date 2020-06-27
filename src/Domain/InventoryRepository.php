<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

interface InventoryRepository
{
    public function getDefaultInventory(): Inventory;
}