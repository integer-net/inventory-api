<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Controller;

use EventSauce\EventSourcing\AggregateRootRepository;
use IntegerNet\InventoryApi\Application\Service\SetQty;
use IntegerNet\InventoryApi\Domain\Inventory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// PATCH /inventory/{inventory_id} {items: [ {sku: X, qty: X}, ... ]}
class InventoryPatchController extends AbstractCommandController
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

    public function execute(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        return $this->handleCommand(
            $request,
            $response,
            function (array $jsonRequest) {
                //TODO use own exception type for input exceptions that should lead to a 400 response
                if (!isset($jsonRequest['items'])) {
                    throw new \InvalidArgumentException('Required "items" field missing');
                }
                if (!is_array($jsonRequest['items'])) {
                    throw new \InvalidArgumentException(
                        '"items" field must be array, found: ' . gettype($jsonRequest['items'])
                    );
                }
                foreach ($jsonRequest['items'] as $index => $item) {
                    if (!isset($item['sku'])) {
                        throw new \InvalidArgumentException(
                            "Required field 'sku' missing for item $index"
                        );
                    }
                    if (!isset($item['qty'])) {
                        throw new \InvalidArgumentException(
                            "Required field 'qty' missing for item $index"
                        );
                    }
                }
                foreach ($jsonRequest['items'] as $item) {
                    $this->setQty->execute($this->getInventory(), $item['sku'], $item['qty']);
                }
                $this->persistInventory();
            }
        );
    }
}
