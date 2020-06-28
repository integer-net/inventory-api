# IntegerNet Inventory API

[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]

Current status: Proof of Concept

## Installation

```
composer install integer-net/inventory-api
```
## Run

To run the server on localhost:8000, start the following as background process:

```
php public/index.php 0.0.0.0:8000
```

## Test

To run the test suite:

```
composer test
```

Static analyzis is performed as pre-commit hook, you can do it manually:

```
vendor/bin/captainhook hook:pre-commit
```

If the message "PHPCBF CAN FIX THE MARKED SNIFF VIOLATIONS AUTOMATICALLY" appears, you can do so by:

```
composer fix
```

## API Usage

Request stock status for skus:
```
GET /is_in_stock?skus[]=sku1&skus[]=sku2
```

Response

```
[
     {
        'sku': 'sku1',
        'is_in_stock': true
     },
     {
        'sku': 'sku2',
        'is_in_stock': false
     }
]
```

Increase/decrease stock qty for single item (for nonexisting sku, item is created)

```
PATCH /inventory/default/item/{sku}/qty
{
    'difference' => 100
}
``` 

Set stock qty for single item (for nonexisting sku, item is created):

```
PUT /inventory/default/item/foobar
{
    'sku' => 'foobar',
    'qty' => 123
}
```

Set stock qty for multiple items (for nonexisting sku, items are created):

```
PATCH /inventory/default {items: [ {sku: X, qty: X}, ... ]}
{
    'items' => [
        {
            'sku' => 'sku-1',
            'qty' => 1000,
        },
        {
            'sku' => 'sku-2',
            'qty' => 1000,
        },
    ]
}
```

### Deprecated endpoint `/event`

Update stock qty:

```
POST /event
{
  'name' => 'qty_changed'
  'payload' => {
    'sku' => 'sku1',
    'difference' => -2
  }
}
```

Set stock qty to specific value:

```
POST /event
{
  'name' => 'qty_set'
  'payload' => {
    'sku' => 'sku1',
    'value' => 123
  }
}
```

Response
```
{
    success: true
}
```

## Architecture

This API started as a hackathon project to build a quick proof of concept, but with the potential to be built upon for customer projects.

### Decisions made during the PoC phase:

- For a quick start, we used `react/react`, should be reduced to the actual needed components later
- For the event bus, we considered an existing library ([SimpleBus](https://simplebus.io) looks nice), but then went for a minimalistic custom implementation. As soon as event sourcing comes into play, [EventSauce](https://eventsauce.io/) shall be used.
- The `qty_set` event has been introduced to be able to connect the API to a Magento indexer, in case where Magento is still the source of truth and the Inventory API serves as an index for quick data retrieval in the frontend. It could also be useful with other systems that primarily manage inventory.
- High level integration tests are implemented on router level, there is no need to spin up the actual ReactPHP server. The router is instantiated exactly like in production.
- For persistence of the events, the [React MySQL client](https://github.com/friends-of-reactphp/mysql) shall be used, which allows non blocking DB queries. It can write events asynchronously to the database, and would only need to read them on startup.
- ReactPHP has its own middleware system. To reuse PSR-15 middlewares, the [PSR-15 adapter](https://github.com/friends-of-reactphp/http-middleware-psr15-adapter) can be used. Keep that in mind when it comes to authorization for the `events` endpoint. Maybe interesting: https://appelsiini.net/projects/slim-jwt-auth/

### Thoughts during second hackathon

#### Events

Decision: does it make sense to receive events from the outside or should we rather design a concrete API and handle events only internally?

Advantages:
- no more event dispatcher needed
- single source of events
- inventory item ids can stay implementation detail, don't need to be exposed

Resolution:
- remove event dispatcher and subscriber, create application services instead
- `EventController` still works but uses application services. Deprecated in favor of `InventoryController`

#### Inventory item IDs

Decision: Do `InventoryItem`s need a UUID?

- for a multi source inventory, we can always instantiate multiple Inventory objects with different parameters
- within one inventory, the SKU should be sufficient as primary key, so we don't even need uuids
- this makes the `Inventory` the aggregate root which makes sense since we always will load a complete inventory into memory based on the event stream

Resolution:
- remove InventoryItemId class, introduce InventoryId class (for now with a constant value)
- make Inventory the aggregate and move event handlers there

#### Eventual Consistency

Decision: how to ensure eventual consistency?

For eventual consistency the QtySet event is a problem. I.e. when events are stored asynchronously, a different order should not cause problems, and in the end the system is consistent.

This only works if we always store increments (QtyChanged events). I had the idea to allow the service to run in two modes: primary (eventual consistent), or secondary (not eventual consistant, external system is source of truth). So that if Magento is the source of truth and updates qtys via indexer, we allow QtySet events.

On second thought, it should be possible to always have eventual consistency, if we handle "qty set" requests like this: store a QtyChanged event with the difference between requested qty and in-memory qty

Resolution: resort to safe QtyChanged events only

#### API design

**Decision:** should we follow REST?

The usual `POST /inventory/{inventory_id}/item/{sku}` does not work well when we want to do things like "add X", or eventually "reserve X", "buy X", "restock X".

It only makes sense for the "set qty" approach, that is used if the InventoryApi is a secondary system,
e.g. updated by a Magento indexer. But even then, it is preferred to update items in batches,
which again does not work that well with REST. Or does it?

**Resolution:**

```
PUT /inventory/{inventory_id}/item/{sku} {sku: X, qty: X}
PATCH /inventory/{inventory_id}/item/{sku}/qty {diff: X}
PATCH /inventory/{inventory_id} {items: [{sku: X, qty: X}, {sku: Y, qty: Y}]}
```

- PUT is used to *create or update* a resource with a client defined URI (the SKU).
- PATCH requests are used for partial changes

For later, possible semantic actions:
```
POST /inventory/{inventory_id}/item/{sku}/reserve
POST /inventory/{inventory_id}/item/{sku}/buy
POST /inventory/{inventory_id}/item/{sku}/restock
```

If the actions are links in the item resource, this would even be good REST: https://softwareengineering.stackexchange.com/a/338669/120379


#### Next refactoring steps

- use a routing library, e.g. nikic/fast-route


[ico-version]: https://img.shields.io/packagist/v/integer-net/inventory-api.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/integer-net/inventory-api/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/integer-net/inventory-api.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/integer-net/inventory-api.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/integer-net/inventory-api.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/integer-net/inventory-api
[link-travis]: https://travis-ci.org/integer-net/inventory-api
[link-scrutinizer]: https://scrutinizer-ci.com/g/integer-net/inventory-api/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/integer-net/inventory-api
[link-downloads]: https://packagist.org/packages/integer-net/inventory-api
[link-author]: https://github.com/schmengler
[link-contributors]: ../../contributors