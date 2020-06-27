<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;
use IntegerNet\InventoryApi\Domain\Process\QtyChanged as QtyHasChanged;
use IntegerNet\InventoryApi\Domain\Process\QtySet as QtyHasBeenSet;

class InventoryItem implements AggregateRoot
{
    use AggregateRootBehaviour;

    /**
     * @var string
     */
    private string $sku;
    /**
     * @var int
     */
    private int $qty;

    public function __construct(InventoryItemId $id, string $sku, int $qty = 0)
    {
        $this->aggregateRootId = $id;
        $this->sku = $sku;
        $this->qty = $qty;
    }

    public function addQty(int $difference): void
    {
        $this->recordThat(QtyHasChanged::withSkuAndDifference($this->sku, $difference));
    }

    /**
     * Used by EventSauce to apply/replay events
     */
    public function applyQtyChanged(QtyHasChanged $event)
    {
        $this->qty += $event->difference();
    }

    public function setQty(int $qty)
    {
        $this->recordThat(QtyHasBeenSet::withSkuAndQty($this->sku, $qty));
    }

    /**
     * Used by EventSauce to apply/replay events
     */
    public function applyQtySet(QtyHasBeenSet $event)
    {
        $this->qty = $event->qty();
    }

    public function isInStock()
    {
        return $this->qty > 0;
    }

    public function qty(): int
    {
        return $this->qty;
    }
}