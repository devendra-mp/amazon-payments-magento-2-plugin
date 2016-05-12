<?php

namespace Amazon\Core\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class RedirectUrl extends RenderConfig
{
    public function _renderValue(AbstractElement $element)
    {
        $values = [];

        $baseUrl = $this->_scopeConfig->getValue(
            Store::XML_PATH_SECURE_BASE_URL,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getId()
        );

        if ($baseUrl) {
            $host     = parse_url($baseUrl, PHP_URL_HOST);
            $path     = parse_url($baseUrl, PHP_URL_PATH);
            $values[] = 'https://' . $host . $path . 'amazon/login/authorize';
            $values[] = 'https://' . $host . $path . 'amazon/login/guest';
        }

        return '<td class="value">' . implode('<br>', $values) . '</td>';
    }
}