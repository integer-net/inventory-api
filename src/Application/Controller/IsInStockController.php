<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Controller;

use IntegerNet\InventoryApi\Domain\Inventory;
use IntegerNet\InventoryApi\Domain\InventoryRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function RingCentral\Psr7\stream_for;

class IsInStockController
{
    private InventoryRepository $inventoryRepository;

    public function __construct(InventoryRepository $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $skus = (array)($request->getQueryParams()['skus'] ?? []);

        //TODO extract the following statement to a service
        $result = array_map(function ($sku) {
                return [
                    'sku' => $sku,
                    'is_in_stock' => $this->isInStock($sku),
                ];
            },
            $skus
        );

        return $response->withBody(stream_for(\json_encode($result)));
    }

    private function isInStock(string $sku): bool
    {
        $inventory = $this->inventoryRepository->getDefaultInventory();
        if ($inventory->hasSku($sku)) {
            return $inventory->getBySku($sku)->isInStock();
        }

        //TODO return n/a item for nonexisting skus
        return false;
    }
}