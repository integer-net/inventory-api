<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application;

use IntegerNet\InventoryApi\Application\Controller\EventController;
use IntegerNet\InventoryApi\Application\Controller\InventoryItemPutController;
use IntegerNet\InventoryApi\Application\Controller\InventoryItemQtyPatchController;
use IntegerNet\InventoryApi\Application\Controller\InventoryPatchController;
use IntegerNet\InventoryApi\Application\Controller\IsInStockController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use React\Http\Response;
use function RingCentral\Psr7\stream_for;

class Router implements RequestHandlerInterface
{
    private IsInStockController $isInStockController;
    private EventController $eventController;
    private InventoryItemPutController $inventoryItemPutController;
    private InventoryItemQtyPatchController $inventoryItemQtyPatchController;
    private InventoryPatchController $inventoryPatchController;

    public function __construct(
        IsInStockController $isInStockController,
        InventoryItemPutController $inventoryItemPutController,
        InventoryItemQtyPatchController $inventoryItemQtyPatchController,
        InventoryPatchController $inventoryPatchController,
        EventController $eventController
    ) {
        $this->inventoryItemPutController = $inventoryItemPutController;
        $this->inventoryItemQtyPatchController = $inventoryItemQtyPatchController;
        $this->isInStockController = $isInStockController;
        $this->eventController = $eventController;
        $this->inventoryPatchController = $inventoryPatchController;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response(
            200,
            [
                'Content-Type' => 'application/json'
            ]
        );
        try {
            switch ($request->getMethod()) {
                case 'GET':
                    switch (trim($request->getUri()->getPath(), '/')) {
                        case 'is_in_stock':
                            $response = $this->isInStockController->execute($request, $response);
                            break;
                        default:
                            $response = $response->withStatus(404)->withBody(
                                \RingCentral\Psr7\stream_for('Endpoint not found: ' . $request->getUri()->getPath())
                            );
                    }
                    break;
                case 'POST':
                    switch (trim($request->getUri()->getPath(), '/')) {
                        case 'event':
                            $response = $this->eventController->execute($request, $response);
                            break;
                        default:
                            $response = $response->withStatus(404)->withBody(
                                \RingCentral\Psr7\stream_for('Endpoint not found: ' . $request->getUri()->getPath())
                            );
                    }
                    break;
                case 'PUT':
                    //TODO use pattern matching instead, e.g. 'inventory/{id}/item', maybe introduce a routing library
                    if (preg_match('{inventory/default/item}', $request->getUri()->getPath())) {
                        $response = $this->inventoryItemPutController->execute($request, $response);
                    } else {
                        $response = $response->withStatus(404)->withBody(
                            \RingCentral\Psr7\stream_for('Endpoint not found: ' . $request->getUri()->getPath())
                        );
                    }
                    break;
                case 'PATCH':
                    //TODO use pattern matching instead, e.g. 'inventory/{id}/item', maybe introduce a routing library
                    if (preg_match(
                        '{inventory/default/item/([^/]+)/qty}',
                        trim($request->getUri()->getPath(), '/'),
                        $matches
                    )) {
                        $response = $this->inventoryItemQtyPatchController->execute($request, $response, $matches[1]);
                    } elseif (preg_match('{^inventory/default$}', trim($request->getUri()->getPath(), '/'))) {
                        $response = $this->inventoryPatchController->execute($request, $response);
                    } else {
                        $response = $response->withStatus(404)->withBody(
                            \RingCentral\Psr7\stream_for('Endpoint not found: ' . $request->getUri()->getPath())
                        );
                    }
                    break;
                default:
                    $response = $response->withStatus(405)->withBody(
                        \RingCentral\Psr7\stream_for('Method not allowed')
                    );
            }
            return $response;
        } catch (\Exception $e) {
            $json = \json_encode(['message' => $e->getMessage()]);
            if ($json === false) {
                $json = '{"message": "An error occured. Then an error occured while rendering the error response"}';
            }
            return $response->withStatus(500)->withBody(stream_for($json));
        }
    }
}
