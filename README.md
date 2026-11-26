# Infrangible Catalog Product Customer Price

Prices of products for single customers: a fixed price or a discount in percent, optionally limited to a quantity and
to one website. When the customer is logged in, the price is shown on product pages and in product lists and charged in
the cart. The prices are calculated by
[Infrangible Catalog Product Price Calculation](https://www.infrangible.dev/modules/catalog-product-price-calculation),
so pages in the full page cache stay the same for every visitor.

## Customer prices

A customer price consists of:

- Customer and Product
- Price (a fixed price in the base currency) or Discount (in percent of the final price), not both
- Limit: the quantity that the customer can buy with the price. The quantity in the cart cannot exceed the limit that is
  left, and the ordered quantities are counted in "Used". Once the limit is used up, the price is no longer offered.
- Priority: if a customer has several prices of a product, the one with the highest priority is used
- Website: the website of the price, or all websites
- Active

The prices are managed under Infrangible > Customer Price > Manage. The form of a customer in the admin gets the tab
"Customer Prices" with the prices of the customer.

When a customer price is added, changed or deleted, the cache of the product (product pages and product lists) is
cleaned.

The items of an order get the product option `price_calculation` with the value `customer_price_<id>`, the ID of the
customer price.

## Requirements

- Magento Open Source or Adobe Commerce 2.4.6 or newer
- PHP 8.1 or newer

## Installation

```bash
composer require infrangible/m2-catalog-product-customer-price
bin/magento module:enable Infrangible_CatalogProductCustomerPrice
bin/magento setup:upgrade
```

In production mode, also run `bin/magento setup:di:compile` and `bin/magento setup:static-content:deploy`.

## License

Infrangible Catalog Product Customer Price is licensed under the MIT License - see the LICENSE file for details.
