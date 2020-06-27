<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Consumer;

use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;
use IntegerNet\InventoryApi\Domain\InStockReadModel;
use IntegerNet\InventoryApi\Domain\Process\QtyChanged;

/**
 * This consumer listens to all events and updates the InStockReadModel
 */
class InStockProjection implements Consumer
{
    private InStockReadModel $inStockReadModel;

    public function __construct(InStockReadModel $inStockReadModel)
    {
        $this->inStockReadModel = $inStockReadModel;
    }

    public function handle(Message $message)
    {
        $event = $message->event();

        if ($event instanceof QtyChanged) {
            $this->inStockReadModel->updateQty($event->sku(), $event->difference());
        }
    }

}