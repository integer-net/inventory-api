# API Usage

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