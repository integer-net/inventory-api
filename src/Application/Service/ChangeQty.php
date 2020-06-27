<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Service;

use IntegerNet\InventoryApi\Domain\Inventory;

class ChangeQty
{
    public function execute(Inventory $inventory, string $sku, int $difference)
    {
        $inventory->addQty($sku, $difference);
    }
}
