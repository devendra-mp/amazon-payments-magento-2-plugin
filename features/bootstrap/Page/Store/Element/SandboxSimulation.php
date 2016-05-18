<?php

namespace Page\Store\Element;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

class SandboxSimulation extends Element
{
    protected $selector = '.amazon-sandbox-simulator';

    const SIMULATION_REJECTED = 'Authorization:Declined:AmazonRejected';
    const SIMILATION_TIMEOUT = 'Authorization:Declined:TransactionTimedOut';

    public function selectSimulation($simulation)
    {
        $this->find('css', '#amazon-sandbox-simulator-heading')->click();
        $this->find('css', 'input[value="' . $simulation . '"]')->click();
    }
}