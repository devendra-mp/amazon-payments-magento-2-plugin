<?php

namespace Context\Data;

use Behat\Behat\Context\Context;

class FixtureContext implements Context
{
    protected static $fixtures = [];

    public static function trackFixture($entity, $repository)
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
                $fixture['repository']->delete($fixture['entity']);
            }
        }

        self::$fixtures = [];
    }
}