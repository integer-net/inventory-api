<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Service;

use IntegerNet\InventoryApi\Domain\InStockReadModel;
use IntegerNet\InventoryApi\Domain\InventoryItemNotFound;

class GetStockStatus
{
    private InStockReadModel $inStockReadModel;

    public function __construct(InStockReadModel $inStockReadModel)
    {
        $this->inStockReadModel = $inStockReadModel;
    }

    /**
     * @param string ...$skus
     * @return array[] with items in the form [sku => string, is_in_stock => bool, error => bool]
     */
    public function execute(string ...$skus): array
    {
        $result = array_map(
            function ($sku) {
                try {
                    return [
                        'sku'         => $sku,
                        'is_in_stock' => $this->isInStock($sku),
                    ];
                } catch (InventoryItemNotFound $e) {
                    return [
                        'sku'         => $sku,
                        'is_in_stock' => false,
                        'error'       => true,
                    ];
                }
            },
            $skus
        );

        return $result;
    }

    private function isInStock(string $sku): bool
    {
        return $this->inStockReadModel->isInStock($sku);
    }
}