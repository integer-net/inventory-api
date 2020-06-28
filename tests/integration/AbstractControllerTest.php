<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi;

use IntegerNet\InventoryApi\Application\Router;
use IntegerNet\InventoryApi\Application\RouterFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RingCentral\Psr7\ServerRequest;
use function json_encode;

abstract class AbstractControllerTest extends TestCase
{
    protected Router            $router;
    protected ResponseInterface $lastResponse;
    protected Domain\Inventory  $defaultInventory;

    protected function setUp(): void
    {
        $routerFactory = new RouterFactory;
        $this->router = $routerFactory->create();
        $this->defaultInventory = $routerFactory->getDefaultInventory();
    }

    protected function then_response_should_be(array $expectedResponse): void
    {
        $this->assertEquals($expectedResponse, \json_decode($this->lastResponse->getBody()->getContents(), true));
    }

    protected function get(string $path, array $query, int $expectedStatusCode): ResponseInterface
    {
        return $this->doRequest('GET', $path, $expectedStatusCode, $query);
    }

    protected function post(string $path, array $body, int $expectedStatusCode = 200): ResponseInterface
    {
        return $this->doRequest(
            'POST',
            $path,
            $expectedStatusCode,
            [],
            ['Content-type' => 'application/json'],
            \json_encode($body)
        );
    }

    protected function put(string $path, array $body, int $expectedStatusCode = 200): ResponseInterface
    {
        return $this->doRequest(
            'PUT',
            $path,
            $expectedStatusCode,
            [],
            ['Content-type' => 'application/json'],
            \json_encode($body)
        );
    }

    protected function given_inventory_status(array $qtyBySku)
    {
        foreach ($qtyBySku as $sku => $qty) {
            $this->post(
                '/event',
                [
                    'name'    => 'qty_set',
                    'payload' => ['sku' => $sku, 'qty' => $qty],
                ]
            );
        }
    }

    public function doRequest(
        string $method,
        string $path,
        int $expectedStatusCode,
        array $query,
        $headers = [],
        $body = null
    ): ResponseInterface {
        $this->lastResponse = $this->router->handle(
            (new ServerRequest(
                $method, 'http://localhost' . $path . '', $headers, $body
            ))->withQueryParams($query)
        );
        $this->assertEquals(
            $expectedStatusCode,
            $this->lastResponse->getStatusCode(),
            "Expected status code for {$method} {$path}"
        );
        return $this->lastResponse;
    }

    /**
     * @param int $expectedQty
     * @param string $sku
     */
    protected function then_item_should_be_in_inventory_with(string $sku, int $expectedQty): void
    {
        $this->assertEquals(
        $expectedQty,
        $this->defaultInventory->getItemBySku($sku)->qty(),
        'Item should be in inventory with qty ' . $expectedQty . ''
    );
    }
}