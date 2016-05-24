<?php

namespace Page\Admin;

use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class CreditMemo extends Page
{
    use PageTrait;

    protected $elements
        = [
            'credit-memo' => ['css' => '.actions button.refund']
        ];

    protected $path = '/admin/sales/order_creditmemo/new/order_id/{orderId}/invoice_id/{invoiceId}';

    public function submitCreditMemo()
    {
        $this->clickElement('credit-memo');
    }
}