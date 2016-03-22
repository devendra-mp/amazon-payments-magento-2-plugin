<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;

class FileContentGenerator extends BaseFixture
{
    /**
     * @param  string $orderNumber
     * @param  int    $orderRemoteStatus
     *
     * @return string
     */
    public function createEuOrderStatusUpdateFileContent($orderNumber, $orderRemoteStatus)
    {
        $paddedOrderId = str_pad($orderNumber, 30, " ", STR_PAD_RIGHT);

        // @codingStandardsIgnoreStart
        $content = "97.50 DERET                       20150902170614BYR15000025000{$paddedOrderId}1{$orderRemoteStatus}15054006CK000001         00323024\n";
        // @codingStandardsIgnoreEnd

        return $content;
    }

    /**
     * @param  string $orderNumber
     *
     * @return string
     */
    public function createUsOrderStatusUpdateFileContent($orderNumber)
    {
        // @codingStandardsIgnoreStart
        $content = "<ShippingConfirmation><Details><OrderHeader><LookupCode>$orderNumber</LookupCode></OrderHeader><ShipmentHeader><Carrier>UPS</Carrier><TrackingNumber>123456789</TrackingNumber></ShipmentHeader></Details></ShippingConfirmation>";
        // @codingStandardsIgnoreEnd

        return $content;
    }

    /**
     * @param  string $productSku
     * @param  int    $qty
     *
     * @return string
     */
    public function createProductStockUpdateFileContent($productSku, $qty)
    {
        // @codingStandardsIgnoreStart
        $content = "<Stock><Details><Detail><ArRef>$productSku</ArRef><Qty>$qty</Qty></Detail></Details></Stock>";
        // @codingStandardsIgnoreEnd

        return $content;
    }
}
