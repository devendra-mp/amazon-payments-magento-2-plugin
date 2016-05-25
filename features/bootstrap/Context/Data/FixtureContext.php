<?php

namespace Context\Data;

use Behat\Behat\Context\Context;
use Magento\Framework\Exception\NoSuchEntityException;

class FixtureContext implements Context
{
    protected static $fixtures = [];

    public static function trackFixture($entity, $repository = null)
    {
        self::$fixtures[] = [
            'entity'     => $entity,
            'repository' => $repository
        ];
    }

    /**
     * @AfterScenario
     */
    public function deleteFixtures()
    {
        if (count(self::$fixtures)) {
            foreach (self::$fixtures as $fixture) {
                try {
                    if  (null !== $fixture['repository']) {
                        $fixture['repository']->delete($fixture['entity']);
                    } else {
                        $fixture['entity']->delete();
                    }
                } catch (NoSuchEntityException $e) {
                    //should have been deleted already sometimes items are tracked twice
                }
            }
        }

        self::$fixtures = [];
    }
}