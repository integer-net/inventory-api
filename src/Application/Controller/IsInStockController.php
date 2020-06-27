<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Controller;

use EventSauce\EventSourcing\AggregateRootRepository;
use IntegerNet\InventoryApi\Domain\InStockReadModel;
use IntegerNet\InventoryApi\Domain\Inventory;
use IntegerNet\InventoryApi\Domain\InventoryId;
use IntegerNet\InventoryApi\Domain\InventoryItemNotFound;
use IntegerNet\InventoryApi\Domain\InventoryRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function RingCentral\Psr7\stream_for;

class IsInStockController
{
    private InStockReadModel $inStockReadModel;

    public function __construct(InStockReadModel $inStockReadModel)
    {
        $this->inStockReadModel = $inStockReadModel;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $skus = (array)($request->getQueryParams()['skus'] ?? []);

        //TODO extract the following statement to a service
        $result = array_map(function ($sku) {
                try {
                    return [
                        'sku' => $sku,
                        'is_in_stock' => $this->isInStock($sku),
                    ];
                } catch (InventoryItemNotFound $e) {
                    return [
                        'sku' => $sku,
                        'is_in_stock' => false,
                        'error' => true,
                    ];
                }
            },
            $skus
        );

        return $response->withBody(stream_for(\json_encode($result)));
    }

    private function isInStock(string $sku): bool
    {
        return $this->inStockReadModel->isInStock($sku);
    }
}