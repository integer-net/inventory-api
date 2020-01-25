<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

class QtySet
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

    public function sku(): string
    {
        return $this->sku;
    }

    public function qty(): int
    {
        return $this->qty;
    }
}