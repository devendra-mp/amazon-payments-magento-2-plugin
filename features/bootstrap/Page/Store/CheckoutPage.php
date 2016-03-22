<?php

namespace Page\Store;

use Page\Type\StorePage;

class CheckoutPage extends StorePage
{
    protected $path = "/checkout";

    protected $elements = [
        'Email' => ['css' => '#customer-email'],
        'Fistname' => ['css' => '[name="shippingAddress.firstname"] input'],
        'Lastname' => ['css' => '[name="shippingAddress.lastname"] input'],
        'Country' => ['css' => '[name="shippingAddress.country_id"] select'],
        'Street' => ['css' => '[name="shippingAddress.street.0"] input'],
        'City' => ['css' => '[name="shippingAddress.city"] input'],
        'State' => ['css' => '[name="shippingAddress.region_id"] select'],
        'Postcode' => ['css' => '[name="shippingAddress.postcode"] input'],
        'Telephone' => ['css' => '[name="shippingAddress.telephone"] input'],
        'Shipping method next button' => ['css' => '#shipping-method-buttons-container button'],
        'Place Order Button' => ['css' => 'button[title="Place Order"]'],
        'Order Confirmation' => ['css' => 'span:contains("Order Confirmation")'],
        'Order Number' => ['css' => '.order-number'],
        'Sample Fragrance Form' => ['css' => '.samples__form'],
        'First Friend Name' => ['css' => '#name_1'],
        'First Friend Email' => ['css' => '#email_1'],
        'Send Samples' => ['css' => '.samples__form .button'],
        'Ajax Loader' => ['css' => '.ajax-loading'],
        'CheckMo Payment Method' => ['css' => '#checkmo'],
        'GB country id' => ['css' => 'option[value="GB"]']
    ];

    private $testData = [
        'UK' => [
            'Fistname' => "Session",
            'Lastname' => "Test",
            'Country' => 'GB',
            'Street' => "63-69 Test Street",
            'City' => "Test City",
            'Postcode' => "WC1A 1DG",
        ],
        'US' => [
            'Fistname' => "Session",
            'Lastname' => "Test",
            'Country' => 'US',
            'Street' => "63-69 Test Street",
            'City' => "Test City",
            'State' => "1",
            'Postcode' => "12345",
        ]
    ];

    public static $lastOrderNumber = '';

    public function fillShippingFormWithTestData($countryId)
    {
        $randomNumber = mt_rand();

        $formData = $this->testData[$countryId];
        $formData['Telephone'] = $randomNumber;
        $formData['Email'] = "behat+{$randomNumber}@sessiondigital.com";

        $this->fillShippingForm($formData);
    }

    public function fillShippingForm($formData = [])
    {
        foreach ($formData as $elementName => $elementValue) {
            $this->setElementValue($elementName, $elementValue);
        }
        $this->clickElement('Shipping method next button');
        $this->waitForPageLoad();
    }

    public function placeOrder()
    {
        $this->waitForElement('Place Order Button');
        if ($this->isElementVisible('CheckMo Payment Method')) {
            $this->clickElement('CheckMo Payment Method');
        }
        $this->clickElement('Place Order Button');
        $this->waitForPageLoad();
    }

    public function canSeeConfirmationPage()
    {
        self::$lastOrderNumber = $this->getElementText('Order Number');
        return !empty(self::$lastOrderNumber);
    }

    public function getLastOrderNumber()
    {
        return self::$lastOrderNumber;
    }

    public function canSeeSampleFragranceForm()
    {
        return $this->hasElement('Sample Fragrance Form');
    }

    public function fillSampleFragranceFormWithTestData()
    {
        $testData = [
            'First Friend Name' => 'Test Friend',
            'First Friend Email' => 'myawesomefriend@sessiondigital.com'
        ];
        $this->fillSampleFragranceForm($testData);
    }

    public function fillSampleFragranceForm($formData = [])
    {
        foreach ($formData as $elementName => $elementValue) {
            $this->setElementValue($elementName, $elementValue);
        }
    }

    public function sendSampleFragrances()
    {
        $this->clickElement('Send Samples');
        $this->waitUntilElementDisappear('Ajax Loader');
    }

    public function canSeeSampleFragranceSuccessfullySentMessage()
    {
        return $this->hasContent('successfully sent');
    }
}
