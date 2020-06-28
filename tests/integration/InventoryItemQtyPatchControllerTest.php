<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi;

class InventoryItemQtyPatchControllerTest extends AbstractControllerTest
{

    /**
     * @test
     */
    public function patch_increases_qty_of_existing_item()
    {
        $this->given_inventory_status(['existing-sku-123' => 10]);
        $this->when_patch_successful('/inventory/default/item/existing-sku-123/qty', ['difference' => 3]);
        $this->then_item_should_be_in_inventory_with('existing-sku-123', 13);
    }

    /**
     * @test
     */
    public function patch_decreases_qty_of_existing_item()
    {
        $this->given_inventory_status(['existing-sku-123' => 10]);
        $this->when_patch_successful('/inventory/default/item/existing-sku-123/qty', ['difference' => -3]);
        $this->then_item_should_be_in_inventory_with('existing-sku-123', 7);
    }

    /**
     * This is how it currently works and as long as (sku, qty) makes a valid item, why not
     * @test
     */
    public function patch_initializes_qty_of_nonexisting_item()
    {
        $this->when_patch_successful('/inventory/default/item/new-sku-456/qty', ['difference' => 5]);
        $this->then_item_should_be_in_inventory_with('new-sku-456', 5);
    }

}
