<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

use EventSauce\EventSourcing\AggregateRootId;

class InventoryId implements AggregateRootId
{
    private const DEFAULT = 'default';

    private string $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public function toString(): string
    {
        return $this->id;
    }

    public static function fromString(string $aggregateRootId): self
    {
        return new self($aggregateRootId);
    }

    public static function default(): self
    {
        return self::fromString(self::DEFAULT);
    }
}
