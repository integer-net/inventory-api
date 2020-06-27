<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

/**
 * This read model (aka view model) is used to query the current state of the inventory. It is automatically updated
 * based on the event stream
 *
 * @see https://eventsauce.io/docs/reacting-to-events/projections-and-read-models/
 */
class InStockReadModel
{
    /**
     * @var array<int>
     */
    private array $qtys = [];

    public function updateQty(string $sku, int $difference): void
    {
        $this->qtys[$sku] = ($this->qtys[$sku] ?? 0) + $difference;
    }

    public function isInStock(string $sku): bool
    {
        if (! isset($this->qtys[$sku])) {
            throw InventoryItemNotFound::withSku($sku);
        }
        $qty = $this->qtys[$sku] ?? 0;
        return (new InventoryItem($sku, $qty))->isInStock();
    }
}
