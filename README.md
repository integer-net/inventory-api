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