<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

class InventoryItem
{
    /**
     * @var string
     */
    private $sku;
    /**
     * @var int
     */
    private $qty;

    public function __construct(string $sku, int $qty)
    {
        $this->sku = $sku;
        $this->qty = $qty;
    }

    public function addQty(int $difference): void
    {
        $this->qty += $difference;
    }

    public function setQty(int $qty)
    {
        $this->qty = $qty;
    }

    public function isInStock()
    {
        return $this->qty > 0;
    }
}