<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IsInStockController
{
    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $skus = (array)($request->getQueryParams()['skus'] ?? []);
        $result = array_map(function ($sku) {
                return [
                    'sku' => $sku,
                    'is_in_stock' => $this->isInStock($sku),
                ];
            },
            $skus
        );

        $json = \json_encode($result);
        $response = $response->withBody(
            \RingCentral\Psr7\stream_for($json)
        );

        return $response;
    }

    private function isInStock(string $sku): bool
    {
        return (bool)random_int(0, 1);
    }
}