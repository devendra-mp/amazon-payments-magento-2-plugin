<?php

namespace Page\Admin;

use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Order extends Page
{
    use PageTrait;

    protected $elements
        = [
            'invoice' => ['css' => '#order_invoice'],
            'submit-invoice' => ['css' => '#invoice_totals .submit-button']
        ];

    protected $path = '/admin/sales/order/view/order_id/{id}';

    public function openWithOrderId($orderId)
    {
        $this->open(['id' => (int)$orderId]);
    }

    public function openCreateInvoice()
    {
        $this->clickElement('invoice');
    }

    public function submitInvoice()
    {
        $this->clickElement('submit-invoice');
    }
}