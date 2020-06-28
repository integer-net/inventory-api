<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Controller;

use EventSauce\EventSourcing\AggregateRootRepository;
use IntegerNet\InventoryApi\Application\Service\SetQty;
use IntegerNet\InventoryApi\Domain\Inventory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// PUT /inventory/{inventory_id}/item/{sku} {sku: X, qty: X}
class InventoryItemPutController extends AbstractCommandController
{

    private SetQty $setQty;

    public function __construct(
        AggregateRootRepository $inventoryRepository,
        Inventory $inventory,
        SetQty $setQty
    ) {
        parent::__construct($inventoryRepository, $inventory);
        $this->setQty = $setQty;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->handleCommand(
            $request,
            $response,
            function (array $jsonRequest) {
                $this->setQty->execute(
                    $this->getInventory(),
                    $jsonRequest['sku'],
                    $jsonRequest['qty']
                );
                $this->persistInventory();
            }
        );
    }
}
