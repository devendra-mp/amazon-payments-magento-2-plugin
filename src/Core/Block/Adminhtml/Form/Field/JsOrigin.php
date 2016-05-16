<?php

namespace Amazon\Core\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field as BaseField;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\UrlInterface;
use Zend\Uri\UriFactory;

class JsOrigin extends BaseField
{
    protected function _renderValue(AbstractElement $element)
    {
        $value = '';
        $store = $this->_storeManager->getStore($this->getRequest()->getParam('store', 0));

        $baseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_WEB, true);

        if ($baseUrl) {
            $uri   = UriFactory::factory($baseUrl);
            $value = $uri->getScheme() . '://' . $uri->getHost();
        }

        return '<td class="value">' . $value . '</td>';
    }

    protected function _renderInheritCheckbox(AbstractElement $element)
    {
        return '<td class="use-default"></td>';
    }
}