<?php

declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain\Process;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class QtyChange implements SerializablePayload
{
    private string $sku;

    private int $difference;

    public function __construct(
        string $sku,
        int $difference
    ) {
        $this->sku = $sku;
        $this->difference = $difference;
    }

    public function sku(): string
    {
        return $this->sku;
    }

    public function difference(): int
    {
        return $this->difference;
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        return new QtyChange(
            (string) $payload['sku'],
            (int) $payload['difference']
        );
    }

    public function toPayload(): array
    {
        return [
            'sku' => (string) $this->sku,
            'difference' => (int) $this->difference,
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public static function withSkuAndDifference(string $sku, int $difference): QtyChange
    {
        return new QtyChange(
            $sku,
            $difference
        );
    }
}

final class QtySet implements SerializablePayload
{
    private string $sku;

    private int $qty;

    public function __construct(
        string $sku,
        int $qty
    ) {
        $this->sku = $sku;
        $this->qty = $qty;
    }

    public function sku(): string
    {
        return $this->sku;
    }

    public function qty(): int
    {
        return $this->qty;
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        return new QtySet(
            (string) $payload['sku'],
            (int) $payload['qty']
        );
    }

    public function toPayload(): array
    {
        return [
            'sku' => (string) $this->sku,
            'qty' => (int) $this->qty,
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public static function withSkuAndQty(string $sku, int $qty): QtySet
    {
        return new QtySet(
            $sku,
            $qty
        );
    }
}
