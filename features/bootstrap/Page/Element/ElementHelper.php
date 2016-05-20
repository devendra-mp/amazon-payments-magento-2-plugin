<?php

namespace Page\Element;

trait ElementHelper
{
    /**
     * @param string $selector
     * @param string|array $locator
     * @return \Behat\Mink\Element\NodeElement|null
     * @see \Behat\Mink\Element\ElementInterface::find()
     */
    abstract public function find($selector, $locator);

    /**
     * @param string $cssQuery
     * @param bool $strict
     * @return \Behat\Mink\Element\NodeElement
     * @throws \Exception
     */
    protected function findElement($cssQuery, $strict = true)
    {
        $element = $this->find('css', $cssQuery);

        if ($strict && $element === null) {
            throw new \Exception('No element found with CSS query: ' . $cssQuery);
        }

        return $element;
    }
}
