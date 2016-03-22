<?php

namespace Page\Admin;

use Page\Type\AdminPage;
use Magento\CatalogInventory\Model\Stock;

class ProductPage extends AdminPage
{
    protected $path = "/admin/catalog/product/edit/id/{id}";

    protected $elements = [
        'All store' => ['css' => 'a:contains("All Store Views")'],
        'Store change button' => ['css' => '#store-change-button'],
        'Qty field' => ['css' => '#qty'],
        'Stock status field' => ['css' => '#quantity_and_stock_status'],
        'Product save button' => ['css' => '#save-split-button-button'],
        'Loading wheel' => ['css' => '.popup-loading'],
        'Product saved message' => ['css' => '.message-success'],
        'Accept store switch button' => ['css' => 'button.action-accept']
    ];

    protected $validStores = [
        'All store',
    ];

    public function setStockValue($storeName, $quantity)
    {
        if (!$this->_isValidStore($storeName)) {
            throw new \Exception("The " . $storeName . " store does not exists");
        }

        $this->_setQty($storeName, $quantity);
        $this->_saveProduct();
    }

    public function hasEqualStockValues($firstStore, $secondStore)
    {
        $firstQty = $this->_getQty($firstStore);
        $secondQty = $this->_getQty($secondStore);

        return $firstQty == $secondQty;
    }

    protected function _saveProduct()
    {
        $this->clickElement('Product save button');
        $this->waitUntilElementDisappear('Loading wheel');
        $this->waitForElement('Product saved message');
    }

    protected function _getQty($storeName)
    {
        $this->_switchStore($storeName);
        return $this->getElementValue('Qty field');
    }

    protected function _setQty($storeName, $quantity)
    {
        $this->_switchStore($storeName);
        $this->setElementValue('Qty field', $quantity);
        $this->setElementValue('Stock status field', Stock::STOCK_IN_STOCK);
    }

    protected function _switchStore($storeName)
    {
        if ($this->_getCurrentStore() != $storeName) {
            $this->clickElement('Store change button');
            $this->clickElement($storeName);
            $this->_acceptSwitchStore();
            $this->waitForPageLoad();
        }
    }

    protected function _acceptSwitchStore()
    {
        $this->clickElement('Accept store switch button');
    }

    protected function _getCurrentStore()
    {
        $currentStore = $this->getElementText('Store change button');

        return trim($currentStore);
    }

    protected function _isValidStore($storeName)
    {
        return in_array($storeName, $this->validStores);
    }
}
