<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi;

class IsInStockControllerTest extends AbstractControllerTest
{

    /**
     * @test
     */
    public function returns_not_in_stock_with_error_for_nonexisting_sku()
    {
        $this->when_controller_called_with_params(['skus' => ['sku_1']]);
        $this->then_response_should_be([['sku' => 'sku_1', 'is_in_stock' => false, 'error' => true]]);
    }

    /**
     * @test
     */
    public function returns_in_stock_for_existing_sku_with_positive_qty()
    {
        $this->given_inventory_status(['sku_1' => 10]);
        $this->when_controller_called_with_params(['skus' => ['sku_1']]);
        $this->then_response_should_be([['sku' => 'sku_1', 'is_in_stock' => true]]);
    }

    /**
     * @test
     */
    public function returns_not_in_stock_for_existing_sku_with_zero_qty()
    {
        $this->given_inventory_status(['sku_1' => 0]);
        $this->when_controller_called_with_params(['skus' => ['sku_1']]);
        $this->then_response_should_be([['sku' => 'sku_1', 'is_in_stock' => false]]);
    }

    /**
     * @test
     */
    public function returns_new_result_after_updating_qty()
    {
        $this->given_inventory_status(['sku_1' => 1]);
        $this->post(
            '/event',
            [
                'name'    => 'qty_change',
                'payload' => ['sku' => 'sku_1', 'difference' => -1],
            ]
        );
        $this->when_controller_called_with_params(['skus' => ['sku_1']]);
        $this->then_response_should_be([['sku' => 'sku_1', 'is_in_stock' => false]]);
    }

    /**
     * @test
     */
    public function returns_result_for_multiple_skus()
    {
        $this->given_inventory_status(
            [
                'sku_1' => 4,
                'sku_2' => 3,
                'sku_3' => 0,
            ]
        );
        $this->when_controller_called_with_params(['skus' => ['sku_1', 'sku_2', 'sku_3']]);
        $this->then_response_should_be(
            [
                ['sku' => 'sku_1', 'is_in_stock' => true],
                ['sku' => 'sku_2', 'is_in_stock' => true],
                ['sku' => 'sku_3', 'is_in_stock' => false],
            ]
        );
    }

    private function when_controller_called_with_params(array $query): void
    {
        $this->get('/is_in_stock', $query, 200);
        $this->assertEquals(
            'application/json',
            $this->lastResponse->getHeaderLine('Content-type'),
            'Content type should be JSON'
        );
    }

}