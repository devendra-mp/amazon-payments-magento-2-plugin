<?php

namespace Amazon\Core\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field as BaseField;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\UrlInterface;

class RedirectUrl extends BaseField
{
    public function _renderValue(AbstractElement $element)
    {
        $values = [];
        $store = $this->_storeManager->getStore();

        $baseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_WEB, true);

        if ($baseUrl) {
            $values[] = $baseUrl . 'amazon/login/authorize';
            $values[] = $baseUrl . 'amazon/login/guest';
        }

        return '<td class="value">' . implode('<br>', $values) . '</td>';
    }

    public function _renderInheritCheckbox(AbstractElement $element)
    {
        return '<td class="use-default"></td>';
    }
}