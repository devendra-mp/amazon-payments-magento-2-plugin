<?php
namespace Context\General;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\RawMinkContext;
use Fixtures\MailCatcher as MailCatcherFixture;

class MailCatcherContext extends RawMinkContext
{
    /**
     * @var MailCatcherFixture
     */
    private $mailCatcherFixture;

    public function __construct()
    {
        $this->mailCatcherFixture = new MailCatcherFixture();
    }

    /**
     * @Then the email with :subject subject should be sent
     */
    public function theEmailWithSubjectShouldBeSent($subject)
    {
        $lastEmail = $this->mailCatcherFixture->getLastEmailData();
        if (empty($lastEmail) || ($lastEmail['subject'] != $subject)) {
            throw new \Exception("I can't see the email");
        }
    }

    /**
     * @Then the shippment email should have been sent
     */
    public function theShipmentEmailShouldHaveBeenSent()
    {
        $lastEmail = $this->mailCatcherFixture->getLastEmailData();
        if  (empty($lastEmail) || (strpos($lastEmail['subject'], 'Shipment #') === false)) {
            throw new \Exception("Shipment email wasn't sent");
        }
    }
}
