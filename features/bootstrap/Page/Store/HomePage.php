<?php

namespace Page\Store;

use Page\Type\StorePage;

class Homepage extends StorePage
{
    protected $path = "/";

    protected $elements = [
        'Newsletter form link' => ['css' => '#newsletter-subscribe-link'],
        'Newsletter form' => ['css' => '#newsletter-subscribe-panel'],
        'Subscriber FirstName' => ['css' => '#first_name'],
        'Subscriber LastName' => ['css' => '#last_name'],
        'Subscriber Email' => ['css' => '#newsletter'],
        'Newsletter Sign up button' => ['css' => '#newsletter-validate-detail button']
    ];

    public function openNewsletterForm()
    {
        $this->scrollToBottom();
        $this->clickElement('Newsletter form link');
        $this->waitForElement('Newsletter form');
    }

    public function fillNewsletterSubscribeFormWithTestData()
    {
        $randomNumber = mt_rand();
        $testData = [
            'Subscriber FirstName' => 'Session',
            'Subscriber LastName' => 'Test',
            'Subscriber Email' => "behat+{$randomNumber}@sessiondigital.com"
        ];

        $this->fillNewsletterSubscribeForm($testData);
    }

    public function fillNewsletterSubscribeForm($formData = [])
    {
        foreach ($formData as $elementName => $elementValue) {
            $this->setElementValue($elementName, $elementValue);
        }
    }

    public function submitNewsletterSubscribeForm()
    {
        $this->waitTime(5000); // fixme
        $this->clickElement('Newsletter Sign up button');
        $this->waitTime(2500); // fixme
    }
}
