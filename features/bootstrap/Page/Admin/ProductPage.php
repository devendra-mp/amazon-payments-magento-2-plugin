<?php

namespace Page\Admin;

use Page\Type\AdminPage;
use Magento\CatalogInventory\Model\Stock;

class ProductPage extends AdminPage
{
    protected $path = "/admin/catalog/product/edit/id/{id}";

    protected $elements = [
        'Store change button' => ['css' => '#store-change-button'],
        'Qty field' => ['css' => '#qty'],
        'Product save button' => ['css' => '#save-split-button-button'],
        'Loading wheel' => ['css' => '.popup-loading'],
        'Product saved message' => ['css' => '.message-success'],
    ];

    protected function _saveProduct()
    {
        $this->clickElement('Product save button');
        $this->waitUntilElementDisappear('Loading wheel');
        $this->waitForElement('Product saved message');
    }

    protected function _isValidStore($storeName)
    {
        return in_array($storeName, $this->validStores);
    }
}
