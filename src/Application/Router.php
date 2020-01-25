<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application;

use IntegerNet\InventoryApi\Application\Controller\EventController;
use IntegerNet\InventoryApi\Application\Controller\IsInStockController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use React\Http\Response;
use function RingCentral\Psr7\stream_for;

class Router implements RequestHandlerInterface
{
    /**
     * @var IsInStockController
     */
    private $isInStockController;
    /**
     * @var EventController
     */
    private $eventController;

    public function __construct(IsInStockController $isInStockController, EventController $eventController)
    {
        $this->isInStockController = $isInStockController;
        $this->eventController = $eventController;
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
                default:
                    $response = $response->withStatus(405)->withBody(
                        \RingCentral\Psr7\stream_for('Method not allowed')
                    );
            }
            return $response;
        } catch (\Exception $e) {
            return $response->withStatus(500)->withBody(stream_for(\json_encode(['message' => $e->getMessage()])));
        }
    }

}