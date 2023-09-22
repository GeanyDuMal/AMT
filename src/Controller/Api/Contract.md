# API Contract

-----

## Endpoints

### GET /api/product/getAll

Returns all users in the system.

* **Headers**  
  Content-Type: application/json
* Authorization: Basic
* **Success Response:**
* **Code:** 200  
  **Content:**

```
{
  products: [
    <product_object>,
    <product_object>,
    ...
  ]
}
```

---

## Structures

### Product

```
product : {
  id: integer,
  name: string,
  productType: string,
  quantityStock: integer,
  imageLink: string (not usable),
  prices: [
    <price_object>,
    <price_object>,
    ...
  ]
}
```

### Price

```
price : {
  clientType: string,
  price: decimal
}
```