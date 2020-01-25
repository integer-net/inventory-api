<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Controller;

use IntegerNet\InventoryApi\Domain\Inventory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IsInStockController
{
    /**
     * @var Inventory
     */
    private $inventory;

    public function __construct(Inventory $inventory)
    {
        $this->inventory = $inventory;
    }

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
        if ($this->inventory->hasSku($sku)) {
            return $this->inventory->getBySku($sku)->isInStock();
        }

        //TODO exception handling
        return false;
    }
}