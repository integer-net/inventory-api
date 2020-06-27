<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Application\Controller;

use IntegerNet\InventoryApi\Application\Service\GetStockStatus;
use IntegerNet\InventoryApi\Domain\InStockReadModel;
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
        $skus = array_map('strval', $skus);
        $result = (new GetStockStatus($this->inStockReadModel))->execute(...$skus);

        return $response->withBody(stream_for(\json_encode($result)));
    }
}
