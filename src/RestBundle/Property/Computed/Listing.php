<?php

namespace DavesWeblab\RestBundle\Property\Computed;

use DavesWeblab\RestBundle\Property\Computed;

class Listing implements \Iterator
{
    /**
     * @var Computed[] $computeds
     */
    private $computeds;

    /**
     * @var string[] $attributes
     */
    private $attributes = [];

    /**
     * Listing constructor.
     *
     * @param Computed[] $computeds
     */
    public function __construct(array $computeds = [])
    {
        $this->computeds = $computeds;

        foreach ($computeds as $computed) {
            $this->attributes[] = $computed->getName();
        }
    }

    /**
     * @return string[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $attribute
     *
     * @return Computed|null
     */
    public function getByAttribute(string $attribute)
    {
        foreach ($this->computeds as $computed) {
            if ($computed->getName() === $attribute) {
                return $computed;
            }
        }

        return null;
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function isComputedAttribute(string $attribute)
    {
        return in_array($attribute, $this->attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->computeds);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        next($this->computeds);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->computeds);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return array_key_exists($this->key(), $this->computeds);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->computeds);
    }
}