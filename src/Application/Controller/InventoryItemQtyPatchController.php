<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Controller;

use EventSauce\EventSourcing\AggregateRootRepository;
use IntegerNet\InventoryApi\Application\Service\ChangeQty;
use IntegerNet\InventoryApi\Domain\Inventory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// PATCH /inventory/{inventory_id}/item/{sku}/qty {difference: X}
class InventoryItemQtyPatchController extends AbstractCommandController
{
    private ChangeQty $changeQty;

    public function __construct(
        AggregateRootRepository $inventoryRepository,
        Inventory $inventory,
        ChangeQty $changeQty
    ) {
        parent::__construct($inventoryRepository, $inventory);
        $this->changeQty = $changeQty;
    }

    public function execute(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $sku
    ): ResponseInterface {
        return $this->handleCommand(
            $request,
            $response,
            function (array $jsonRequest) use ($sku) {
                $this->changeQty->execute(
                    $this->getInventory(),
                    $sku,
                    $jsonRequest['difference']
                );
                $this->persistInventory();
            }
        );
    }
}
