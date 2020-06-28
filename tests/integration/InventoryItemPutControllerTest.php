<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi;

class InventoryItemPutControllerTest extends AbstractControllerTest
{
    /**
     * @test
     */
    public function put_creates_new_item()
    {
        $this->when_put_successful('/inventory/default/item/new-sku-123', ['sku' => 'new-sku-123', 'qty' => 3]);
        $this->then_item_should_be_in_inventory_with('new-sku-123', 3);
    }

    /**
     * @test
     */
    public function put_updates_existing_item()
    {
        $this->given_inventory_status(['existing-sku-123' => 10]);
        $this->then_item_should_be_in_inventory_with('existing-sku-123', 10);
        $this->when_put_successful('/inventory/default/item/existing-sku-123', ['sku' => 'existing-sku-123', 'qty' => 3]);
        $this->then_item_should_be_in_inventory_with('existing-sku-123', 3);
    }

}
