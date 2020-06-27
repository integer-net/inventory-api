<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi;

use IntegerNet\InventoryApi\Application\Router;
use IntegerNet\InventoryApi\Application\RouterFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RingCentral\Psr7\ServerRequest;

class IsInStockControllerTest extends TestCase
{
    /**
     * @var Router
     */
    private $router;
    /**
     * @var ResponseInterface
     */
    private $lastResponse;

    protected function setUp(): void
    {
        $this->router = RouterFactory::create();
    }

    /**
     * @test
     */
    public function returns_not_in_stock_with_error_for_nonexisting_sku()
    {
        $this->whenControllerCalledWithParams(['skus' => ['sku_1']]);
        $this->thenResponseShouldBe([['sku' => 'sku_1', 'is_in_stock' => false, 'error' => true]]);
    }

    /**
     * @test
     */
    public function returns_in_stock_for_existing_sku_with_positive_qty()
    {
        $this->givenInventoryStatus(['sku_1' => 10]);
        $this->whenControllerCalledWithParams(['skus' => ['sku_1']]);
        $this->thenResponseShouldBe([['sku' => 'sku_1', 'is_in_stock' => true]]);
    }

    /**
     * @test
     */
    public function returns_not_in_stock_for_existing_sku_with_zero_qty()
    {
        $this->givenInventoryStatus(['sku_1' => 0]);
        $this->whenControllerCalledWithParams(['skus' => ['sku_1']]);
        $this->thenResponseShouldBe([['sku' => 'sku_1', 'is_in_stock' => false]]);
    }

    /**
     * @test
     */
    public function returns_new_result_after_updating_qty()
    {
        $this->givenInventoryStatus(['sku_1' => 1]);
        $this->post(
            '/event',
            [
                'name'    => 'qty_change',
                'payload' => ['sku' => 'sku_1', 'difference' => -1],
            ]
        );
        $this->whenControllerCalledWithParams(['skus' => ['sku_1']]);
        $this->thenResponseShouldBe([['sku' => 'sku_1', 'is_in_stock' => false]]);
    }

    /**
     * @test
     */
    public function returns_result_for_multiple_skus()
    {
        $this->givenInventoryStatus(
            [
                'sku_1' => 4,
                'sku_2' => 3,
                'sku_3' => 0,
            ]
        );
        $this->whenControllerCalledWithParams(['skus' => ['sku_1', 'sku_2', 'sku_3']]);
        $this->thenResponseShouldBe(
            [
                ['sku' => 'sku_1', 'is_in_stock' => true],
                ['sku' => 'sku_2', 'is_in_stock' => true],
                ['sku' => 'sku_3', 'is_in_stock' => false],
            ]
        );
    }

    private function givenInventoryStatus(array $qtyBySku)
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

    private function post(string $path, array $body, int $expectedStatusCode = 200): ResponseInterface
    {
        $response = $this->router->handle(
            new ServerRequest(
                'POST', 'http://localhost' . $path . '', ['Content-type' => 'application/json'], \json_encode($body)
            )
        );
        $this->assertEquals($expectedStatusCode, $response->getStatusCode(), "Expected status code for POST {$path}");
        return $response;
    }

    private function whenControllerCalledWithParams(array $query): void
    {
        $this->lastResponse = $this->router->handle(
            (new ServerRequest('GET', 'http://localhost/is_in_stock'))->withQueryParams($query)
        );
        $this->assertEquals(200, $this->lastResponse->getStatusCode(), 'Response status should be 200 OK');
        $this->assertEquals(
            'application/json',
            $this->lastResponse->getHeaderLine('Content-type'),
            'Content type should be JSON'
        );
    }

    private function thenResponseShouldBe(array $expectedResponse): void
    {
        $this->assertEquals($expectedResponse, \json_decode($this->lastResponse->getBody()->getContents(), true));
    }
}