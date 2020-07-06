# GiaoHangNhanh


## Installation

##### Using Composer (we recommended)

```
composer require boolfly/module-giaohangnhanh
```

## Configuration

First of all, we need to run command-line: bin/magento region:generate

This command-line will import region information data into database (required).

### Setup Currency

We need to make sure our website supporting Vietnamese Dong.

Log in to Admin, **STORES > Configurations > GENERAL > Currency Setup > Currency Options > Allowed Currencies**. Make sure the Vietnamese Dong is selected.

![GiaoHangNhanh currency](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/giaohangnhanh/currency-setup.png)

Go to Currency Rates, **STORES > Currency > Currency Rates**

![GiaoHangNhanh currency](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/giaohangnhanh/currency-rate.png)

### Config Store Information
GiaoHangNhanh extension supports Vietnam store only.

Log in to Admin, **STORES > Configurations > GENERAL > General > Store Information**

 - Country: Select Vietnam.

### Config API
Log in to Admin, **STORES > Configurations > SALES > Shipping Methods > Giao Hang Nhanh**

![GiaoHangNhanh Configuration](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/giaohangnhanh/config-1.png)

![GiaoHangNhanh Configuration](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/giaohangnhanh/config-2.png)

Read more here:

- https://api.ghn.vn
- https://api.ghn.vn/home/faq

Create Giao Hang Nhanh account to get the api token.

Configuration info to integrate with GiaoHangNhanh API.

   - Sandbox Mode: when testing, we should enable this mode
   - Api Token: Use the info above.
   - Payment type: Choose who will pay the payment fee.
   - Note Code: Rule for the shipping.
   - Debug: Enable to allow writing log.

GiaoHangNhanh extension consists of Giao Hang Nhanh Express and Giao Hang Nhanh Standard. We need to fill neccessary information for these solutions (Advanced Settings).


## How does it work?

### Checkout
 After enabling this method, go to the checkout, we can see this method.

 ![GiaoHangNhanh Checkout](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/giaohangnhanh/checkout.png)

Contribution
---
Want to contribute to this extension? The quickest way is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests)

Magento 2 Extensions
---

- [Ajax Wishlist](https://github.com/boolfly/ajax-wishlist)
- [Quick View](https://github.com/boolfly/quick-view)
- [Banner Slider](https://github.com/boolfly/banner-slider)
- [Product Label](https://github.com/boolfly/product-label)
- [ZaloPay](https://github.com/boolfly/zalo-pay)
- [Momo](https://github.com/boolfly/momo-wallet)
- [Blog](https://github.com/boolfly/blog)
- [Brand](https://github.com/boolfly/brand)
- [Product Question](https://github.com/boolfly/product-question)
- [Sales Sequence](https://github.com/boolfly/sales-sequence)

Support
---
Need help settings up or want to customize this extension to meet your business needs? Please email boolfly.inc@gmail.com and if we like your idea we will add this feature for free or at a discounted rate.


