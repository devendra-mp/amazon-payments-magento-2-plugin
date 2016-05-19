<?php

namespace Page\Store;

use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Success extends Page
{
    use PageTrait;

    protected $path = '/checkout/onepage/success';

    protected $elements = [
        'create-account-btn' => ['css' => 'input[type="submit"]'],
    ];

    /**
     * @return bool
     */
    public function createAccountButtonIsVisible()
    {
        $createAccount = $this->getElementWithWait('create-account-btn', 5000);
        return ($createAccount !== null) && $createAccount->isVisible();
    }

    /**
     * @param string $email
     * @throws \Exception
     */
    public function clickCreateAccountHavingEmail($email)
    {
        if (!$this->createAccountButtonIsVisible()) {
            throw new \Exception('Create account button is not existent or visible');
        }

        $this->getElement('create-account-btn')->click();
        $this->waitForAjaxRequestsToComplete();

        // delete customer
        (new \Fixtures\Customer)->track($email);

    }
}
