<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

class InventoryItemNotFound extends \RuntimeException
{
    public static function withSku(string $sku): self
    {
        return new self("No inventory item found for SKU {$sku}");
    }
}
