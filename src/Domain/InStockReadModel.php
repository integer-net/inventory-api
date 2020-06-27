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
    private array $qtys = [];

    public function updateQty(string $sku, int $difference)
    {
        $this->qtys[$sku] = ($this->qtys[$sku] ?? 0) + $difference;
    }

    public function isInStock(string $sku)
    {
        //TODO throw exception if SKU not known?
        $qty = $this->qtys[$sku] ?? 0;
        return (new InventoryItem($sku, $qty))->isInStock();
    }
}