<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

use EventSauce\EventSourcing\AggregateRootId;

class InventoryItemId implements AggregateRootId
{
    private string $uuid;

    private function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function toString(): string
    {
        return $this->uuid;
    }

    public static function fromString(string $aggregateRootId): AggregateRootId
    {
        return new self($aggregateRootId);
    }

    /**
     * Generate new ID
     *
     * @return static
     */
    public static function new(): self
    {
        return self::fromString(md5(uniqid('', true))); //TODO use UUID instead
    }

}