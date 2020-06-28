<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi;

class InventoryItemPutControllerTest extends AbstractControllerTest
{
    private const INVENTORY_ID = 'default';

    /**
     * @test
     */
    public function put_creates_new_item()
    {
        $this->when_controller_called_with_params('new-sku', 3);
        $this->then_item_should_be_in_inventory_with('new-sku', 3);
    }

    /**
     * @test
     */
    public function put_updates_existing_item()
    {
        $this->given_inventory_status(['existing-sku' => 10]);
        $this->when_controller_called_with_params('existing-sku', 3);
        $this->then_item_should_be_in_inventory_with('existing-sku', 3);
    }

    private function when_controller_called_with_params(string $sku, int $qty): void
    {
        $this->put('/inventory/' . self::INVENTORY_ID . '/item/' . $sku, ['sku' => $sku, 'qty' => $qty]);
    }
}
