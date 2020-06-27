<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Controller;

use EventSauce\EventSourcing\EventDispatcher;
use IntegerNet\InventoryApi\Application\Service\ChangeQty;
use IntegerNet\InventoryApi\Application\Service\SetQty;
use IntegerNet\InventoryApi\Application\SubscribableMessageDispatcher;
use IntegerNet\InventoryApi\Domain\Inventory;
use IntegerNet\InventoryApi\Domain\Process\QtyChanged;
use IntegerNet\InventoryApi\Domain\Process\QtySet;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @deprecated use inentory end points instead
 */
class EventController
{
    private EventDispatcher $eventDispatcher;

    private Inventory $inventory;

    //TODO get inventory from inventory repository
    public function __construct(Inventory $inventory)
    {
        $this->inventory = $inventory;
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
                    (new ChangeQty())->execute(
                        $this->inventory,
                        $jsonRequest['payload']['sku'],
                        $jsonRequest['payload']['difference']
                    );
                    break;
                case 'qty_set':
                    (new SetQty())->execute(
                        $this->inventory,
                        $jsonRequest['payload']['sku'],
                        $jsonRequest['payload']['qty']
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
