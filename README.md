# IntegerNet Inventory API

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

Update stock quty:

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

For eventual consistency the QtySet event is a problem. I.e. when events are stored asynchronously, a different order should not cause problems, and in the end the system is consistent. This only works if we always store increments (QtyChanged events). I had the idea to allow the service to run in two modes: primary (eventual consistent), or secondary (not eventual consistant, external system is source of truth). So that if Magento is the source of truth and updates qtys via indexer, we allow QtySet events. On second thought, it should be possible to always have eventual consistency, if we handle "qty set" requests like this: store a QtyChanged event with the difference between requested qty and in-memory qty

#### Next refactoring steps

- make items private in inventory, return read model in getBySku if needed
- finally remove getBySku and always use read model from projector