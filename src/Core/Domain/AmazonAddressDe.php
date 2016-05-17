<?php

namespace Amazon\Core\Domain;

class AmazonAddressDe extends AmazonAddress
{
    /**
     * @param array $address
     * @param AmazonNameFactory $addressNameFactory
     */
    public function __construct(array $address, AmazonNameFactory $addressNameFactory)
    {
        parent::__construct($address, $addressNameFactory);
        $this->processValues();
    }

    /**
     * @return void
     */
    private function processValues()
    {
        $line1 = (string) $this->getLine(1);
        $line2 = (string) $this->getLine(2);
        $line3 = (string) $this->getLine(3);

        if (!empty($line3)) {
            // replace all lines
            $this->lines = [ $line3 ];
            $firstTwoLines = $line1 . ' ' . $line2;

            if (is_numeric($line1) || $this->isPackstationAddress($firstTwoLines)) {
                // PO Box
                $this->lines[] = $firstTwoLines;
            } else {
                $this->company = $firstTwoLines;
            }

        } elseif (!empty($line2)) {
            // replace all lines
            $this->lines = [ $line2 ];

            if (is_numeric($line1) || $this->isPackstationAddress($line1)) {
                // PO Box
                $this->lines[] = $line1;
            } else {
                $this->company = $line1;
            }
        } elseif (!empty($line1)) {
            // replace all lines
            $this->lines = [ $line1 ];
        }
    }

    /**
     * @link https://en.wikipedia.org/wiki/Packstation
     * @param string $address
     * @return bool
     */
    protected function isPackstationAddress($address)
    {
        return stripos($address, 'packstation') !== false;
    }
}
