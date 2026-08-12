# Fandoogh Calculator

The calculator is available from **Fandoogh > ماشین حساب** and through:

```text
[fandoogh_calculator]
```

## Product selection

The front-end product list contains:

- published visible products assigned to at least one fixed-price row;
- published visible products in the `fandoogh` product category.

Category slugs can be changed without editing the module:

```php
add_filter('fa_calculator_product_category_slugs', static fn (): array => ['fandoogh', 'metal-fandoogh']);
```

## Fixed prices

Use the public helper to retrieve active rows mapped to a product:

```php
$items = get_active_fixed_prices_for_product($product_id);
```

Supported calculation types:

- `per_meter`: added to the variation price before multiplying by meters;
- `lump_sum`: added once after the metered calculation.

Formula:

```text
(variation price + per-meter fees) × meters + lump-sum fees
```

Amounts entered in the panel are Toman. When WooCommerce uses `IRR`, the
calculator converts WooCommerce prices from Rial to Toman for calculation and
back to Rial when setting the cart item price.

## Security

The browser never submits a trusted total. The add-to-cart endpoint validates
the product, variation, availability and meter range, then recalculates the
quote from current WooCommerce and Fandoogh data. Both public endpoints require
a WordPress nonce; admin writes additionally require `manage_woocommerce`.

The maximum accepted length defaults to 100,000 meters and is filterable:

```php
add_filter('fa_calculator_maximum_meters', static fn (): float => 5000.0);
```
