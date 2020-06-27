<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

class InventoryItem
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

    public function withAddedQty(int $difference): self
    {
        return new self($this->sku, $this->qty + $difference);
    }

    /**
     * @todo we will probably need an InStockPolicy tied to the Inventory which determines when an item is considered
     *       in stock, e.g. with a different threshold than 0
     */
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
