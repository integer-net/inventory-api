<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EventController
{
    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $result = ['success' => true];
        $requestBody = $request->getBody()->getContents();
        $jsonRequest = \json_decode($requestBody);
        if ($jsonRequest === null) {
            $result['success'] = false;
            $result['message'] = \json_last_error_msg();
            $response = $response->withStatus(400);
        }
        $result['request'] = $jsonRequest;

        //TODO create Event from JSON
        //     dispatch event

        $json = \json_encode($result);
        $response = $response->withBody(
            \RingCentral\Psr7\stream_for($json)
        );

        return $response;

    }
}
