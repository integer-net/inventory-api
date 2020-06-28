<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Controller;

use EventSauce\EventSourcing\AggregateRootRepository;
use IntegerNet\InventoryApi\Application\Service\SetQty;
use IntegerNet\InventoryApi\Domain\Inventory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Base class for controllers that change the state of the system
 */
class AbstractCommandController
{
    /**
     * The repository is used to persist events after executing a controller action
     */
    private AggregateRootRepository $inventoryRepository;

    /**
     * We pass the inventory here directly because it should have already been loaded at application startup and
     * the default aggregate root repository implementation always recreates the aggregate based on persisted events.
     *
     * When multiple inventories should be supported, we can change this into something like an InventoryPool with
     * loaded inventories
     */
    protected Inventory $inventory;

    public function __construct(
        AggregateRootRepository $inventoryRepository,
        Inventory $inventory
    ) {
        $this->inventory = $inventory;
        $this->inventoryRepository = $inventoryRepository;
    }

    protected function getInventory(): Inventory
    {
        return $this->inventory;
    }

    protected function persistInventory(): void
    {
        $this->inventoryRepository->persist($this->getInventory());
    }

    /**
     * Executes the command and returns a ['success' => bool, 'message' => string] response
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $command with signature (array $jsonRequest): void
     * @return ResponseInterface
     */
    protected function handleCommand(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $command
    ): ResponseInterface {
        $result = ['success' => true];
        $requestBody = $request->getBody()->getContents();
        $jsonRequest = \json_decode($requestBody, true);
        if ($jsonRequest === null) {
            $result['success'] = false;
            $result['message'] = \json_last_error_msg();
            $response = $response->withStatus(400);
        } else {
            $result['request'] = $jsonRequest;
            try {
                $command($jsonRequest);
            } catch (\InvalidArgumentException $e) {
                $result['success'] = false;
                $result['message'] = $e->getMessage();
                $response = $response->withStatus(400);
            } catch (\Exception $e) {
                $result['success'] = false;
                $result['message'] = $e->getMessage();
                $response = $response->withStatus(500);
            }
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
}
