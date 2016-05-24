<?php

namespace Page\Admin;

use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Invoice extends Page
{
    use PageTrait;

    protected $elements
        = [
            'credit-memo' => ['css' => 'button.credit-memo']
        ];

    protected $path = '/admin/sales/order_invoice/view/invoice_id/{id}';

    public function openWithInvoiceId($invoiceId)
    {
        $this->open(['id' => (int)$invoiceId]);
    }

    public function openCreateCreditMemo()
    {
        $this->clickElement('credit-memo');
    }
}