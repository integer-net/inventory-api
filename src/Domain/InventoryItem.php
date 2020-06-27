<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

class InventoryItem implements InventoryItemInterface
{
    /**
     * @var string
     */
    private string $sku;
    /**
     * @var int
     */
    private int $qty;

    public function __construct(string $sku, int $qty = 0)
    {
        $this->sku = $sku;
        $this->qty = $qty;
    }

    public function addQty(int $difference): void
    {
        $this->qty += $difference;
    }

    public function isInStock(): bool
    {
        return $this->qty > 0;
    }

    public function qty(): int
    {
        return $this->qty;
    }

    public function sku(): string
    {
        return $this->sku;
    }
}