<?php

namespace Page\Store;

use Page\Type\StorePage;

class CartPage extends StorePage
{
    protected $path = "/checkout/cart";

    protected $elements = [
        'Show coupon link' => ['css' => '#block-discount-heading'],
        'Coupon code field' => ['css' => '#coupon_code'],
        'Apply coupon code button' => ['css' => '#discount-coupon-form button']
    ];

    public function applyCouponCode($couponCode)
    {
        $this->clickElement('Show coupon link');
        $this->setElementValue('Coupon code field', $couponCode);
        $this->clickElement('Apply coupon code button');
        $this->waitForPageLoad();
    }
}
