<?php
declare(strict_types=1);

namespace IntegerNet\InventoryApi\Domain;

use EventSauce\EventSourcing\AggregateRootId;

final class InventoryId implements AggregateRootId
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

    /**
     * @return static
     */
    public static function fromString(string $aggregateRootId): self
    {
        return new static($aggregateRootId);
    }

    public static function default(): self
    {
        return self::fromString(self::DEFAULT);
    }
}
