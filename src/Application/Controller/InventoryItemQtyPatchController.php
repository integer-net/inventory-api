<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Controller;

use EventSauce\EventSourcing\AggregateRootRepository;
use IntegerNet\InventoryApi\Application\Service\ChangeQty;
use IntegerNet\InventoryApi\Domain\Inventory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// PATCH /inventory/{inventory_id}/item/{sku}/qty {difference: X}
class InventoryItemQtyPatchController
{
    /**
     * We pass the inventory here directly because it should have already been loaded at application startup and
     * the default aggregate root repository implementation always recreates the aggregate based on persisted events.
     *
     * When multiple inventories should be supported, we can change this into something like an InventoryPool with
     * loaded inventories
     */
    private Inventory $inventory;
    /**
     * The repository is used to persist events after executing a controller action
     */
    private AggregateRootRepository $inventoryRepository;

    private ChangeQty $changeQty;

    public function __construct(
        AggregateRootRepository $inventoryRepository,
        Inventory $inventory,
        ChangeQty $changeQty
    ) {
        $this->inventory = $inventory;
        $this->inventoryRepository = $inventoryRepository;
        $this->changeQty = $changeQty;
    }

    public function execute(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $sku
    ): ResponseInterface {
        $result = ['success' => true];
        $requestBody = $request->getBody()->getContents();
        $jsonRequest = \json_decode($requestBody, true);
        if ($jsonRequest === null) {
            $result['success'] = false;
            $result['message'] = \json_last_error_msg();
            $response = $response->withStatus(400);
        }
        $result['request'] = $jsonRequest;
        try {
            $this->changeQty->execute(
                $this->getInventory(),
                $sku,
                $jsonRequest['difference']
            );
            $this->inventoryRepository->persist($this->getInventory());
        } catch (\Exception $e) {
            $result['success'] = false;
            $result['message'] = $e->getMessage();
            $response = $response->withStatus(500);
        }

        $json = \json_encode($result);
        if ($json === false) {
            throw new \RuntimeException("Error encoding JSON: " . \json_last_error_msg());
        }
        $response = $response->withBody(
            \RingCentral\Psr7\stream_for($json)
        );

        return $response;
    }
    private function getInventory(): Inventory
    {
        return $this->inventory;
    }
}
