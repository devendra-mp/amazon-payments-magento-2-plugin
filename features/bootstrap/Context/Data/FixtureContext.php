<?php

namespace Context\Data;

use Behat\Behat\Context\Context;

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
                if  (null !== $fixture['repository']) {
                    $fixture['repository']->delete($fixture['entity']);
                } else {
                    $fixture['entity']->delete();
                }
            }
        }

        self::$fixtures = [];
    }
}