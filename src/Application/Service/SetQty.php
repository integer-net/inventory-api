<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Service;

use IntegerNet\InventoryApi\Domain\Inventory;

class SetQty
{
    public function execute(Inventory $inventory, string $sku, int $qty)
    {
        if (!$inventory->hasSku($sku)) {
            $inventory->createItem($sku, 0);
        }

        $inventoryItem = $inventory->getBySku($sku);
        $difference = $qty - $inventoryItem->qty();
        $inventory->addQty($sku, $difference);
    }
}
