<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi;

class InventoryPatchControllerTest extends AbstractControllerTest
{

    /**
     * @test
     */
    public function update_qtys_of_existing_item()
    {
        $this->given_inventory_status(['existing-sku-1' => 10, 'existing-sku-2' => 20]);
        $this->when_patch_successful(
            '/inventory/default',
            ['items' => [['sku' => 'existing-sku-1', 'qty' => 3], ['sku' => 'existing-sku-2', 'qty' => 4]]]
        );
        $this->then_item_should_be_in_inventory_with('existing-sku-1', 3);
        $this->then_item_should_be_in_inventory_with('existing-sku-2', 4);
    }

    /**
     * @test
     */
    public function initializes_qtys_of_nonexisting_items()
    {
        $this->when_patch_successful(
            '/inventory/default',
            ['items' => [['sku' => 'new-sku-1', 'qty' => 100], ['sku' => 'new-sku-2', 'qty' => 150]]]
        );
        $this->then_item_should_be_in_inventory_with('new-sku-1', 100);
        $this->then_item_should_be_in_inventory_with('new-sku-2', 150);
    }

    /**
     * @test
     * @dataProvider data_invalid_parameters
     */
    public function bad_request_on_invalid_items_parameter(array $parameters)
    {
        $this->patch('/inventory/default', $parameters, 400);
    }

    public static function data_invalid_parameters(): \Generator
    {
        yield 'missing "items"' => [[]];
        yield 'invalid "items" type' => [['items' => 0]];
        yield 'missing "qty"' => [['items' => [['sku' => 'sku-1']]]];
        yield 'missing "sku"' => [['items' => [['qty' => '1']]]];
    }
}
