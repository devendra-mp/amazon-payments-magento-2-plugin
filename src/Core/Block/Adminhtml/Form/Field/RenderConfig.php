<?php

namespace Amazon\Core\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

abstract class RenderConfig extends Field
{
    public function render(AbstractElement $element)
    {
        $html = '<td class="label"><label>' . $element->getLabel() . '</label></td>';
        $html .= $this->_renderValue($element);
        $html .= $this->_renderScopeLabel($element);
        $html .= $this->_renderHint($element);

        return $this->_decorateRowHtml($element, $html);
    }
}