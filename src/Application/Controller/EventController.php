<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Controller;

use EventSauce\EventSourcing\EventDispatcher;
use IntegerNet\InventoryApi\Application\SubscribableMessageDispatcher;
use IntegerNet\InventoryApi\Domain\Process\QtyChanged;
use IntegerNet\InventoryApi\Domain\Process\QtySet;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EventController
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
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
            switch ($jsonRequest['name']) {
                case 'qty_change':
                    $this->eventDispatcher->dispatch(
                        QtyChanged::fromPayload($jsonRequest['payload'])
                    );
                    break;
                case 'qty_set':
                    $this->eventDispatcher->dispatch(
                        QtySet::fromPayload($jsonRequest['payload'])
                    );
                    break;
            }

        } catch (\Exception $e) {
            $result['success'] = false;
            $result['message'] = $e->getMessage();
            $response = $response->withStatus(500);
        }

        $json = \json_encode($result);
        $response = $response->withBody(
            \RingCentral\Psr7\stream_for($json)
        );

        return $response;

    }
}
