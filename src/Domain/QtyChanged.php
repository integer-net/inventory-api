<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

class QtyChanged
{
    /**
     * @var string
     */
    private $sku;
    /**
     * @var int
     */
    private $difference;

    public function __construct(string $sku, int $difference)
    {
        $this->sku = $sku;
        $this->difference = $difference;
    }

    public function sku(): string
    {
        return $this->sku;
    }

    public function difference(): int
    {
        return $this->difference;
    }

}