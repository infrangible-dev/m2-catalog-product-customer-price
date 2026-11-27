# Changelog

## 4.0.2

### Changed

- The price calculation reads the customer prices and the order placement counts their use with `getData()` and
  `setData()` and the column names instead of magic methods.

## 4.0.1

### Changed

- Supports PHP 8.1 again, like Magento 2.4.6: the Adobe Commerce Marketplace requires modules to allow every PHP
  version of the Magento versions they support.

## 4.0.0

### Breaking changes

- Requires Magento 2.4.6 or newer and PHP 8.2 or newer. Older versions of Magento and PHP 7.1 to 8.1 are no longer
  supported; use version 3.x for these.
- Requires `infrangible/m2-catalog-product-price-calculation` 4.0, `infrangible/m2-backend-widget` 4.0,
  `infrangible/m2-core` 5.0, `infrangible/m2-foundation` 3.0, `fewedev/base` 2.4 and the Magento modules it uses.
- All classes have native types, and the constructors use constructor property promotion. The constructors of the
  calculation, the helpers, the observers and the tab of the customer form changed.
- The order items get the product option `price_calculation` with the value `customer_price_<id>` instead of the ID of
  the customer price alone. Orders of quotes from before the update are still counted.
- `Observer\ProductCustomerPriceSaveCommitAfter` and `Observer\ProductCustomerPriceDeleteCommitAfter` are replaced by
  `Observer\CleanProductCache`.
- The table is declared in `db_schema.xml` instead of setup scripts.

### Fixed

- Every order with a price of another calculation (for example a customer benefit) saved an empty customer price, which
  failed after the order was placed.
- The used quantity of a customer price grew by 1 per order instead of the ordered quantity, so the limit allowed more
  than its quantity.
- A customer price that did not exist any more was saved again as an empty price when an order still had its ID.
- New customer prices did not clean the cache of their products, and only one price per product was compared, so the
  product pages kept the old prices. The state of the prices was always saved in the flag of the customer prices, also
  for the customer benefits that use the helper, so both cleaned the cache again and again.
- The cache is cleaned with the event `clean_cache_by_tags`, so Varnish is cleaned as well.
- The quantity in the cart counted items without ID (new items) and child items of configurable products.
- Used quantities without value made a limited price unusable.
- Invalid values (negative prices and limits, discounts above 100 percent, texts) were saved; the messages are
  translated now.

### Added

- The website in the grid and form of the customer prices in the tab of the customer form.
- Translations in 24 languages.
- Unit tests.
- README with the customer prices.
