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
                            $controller = new IsInStockController();
                            $response = $controller->execute($request, $response);
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
                            $controller = new EventController();
                            $response = $controller->execute($request, $response);
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