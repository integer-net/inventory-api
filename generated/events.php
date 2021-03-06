<?php

declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain\Process;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class QtyChanged implements SerializablePayload
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
        return new QtyChanged(
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
    public static function withSkuAndDifference(string $sku, int $difference): QtyChanged
    {
        return new QtyChanged(
            $sku,
            $difference
        );
    }
}
