<?php

namespace Page\Element\Checkout;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

class SandboxSimulation extends Element
{
    protected $selector = '.amazon-sandbox-simulator';

    const SIMULATION_REJECTED = 'Authorization:Declined:AmazonRejected';
    const SIMILATION_TIMEOUT = 'Authorization:Declined:TransactionTimedOut';
    const SIMULATION_INVALID_PAYMENT = 'Authorization:Declined:InvalidPaymentMethod';
    const NO_SIMULATION = 'default';

    public function selectSimulation($simulation)
    {
        $this->find('css', '#amazon-sandbox-simulator-heading')->click();
        $this->find('css', 'input[value="' . $simulation . '"]')->click();
        $this->find('css', '#amazon-sandbox-simulator-heading')->click();
    }
}