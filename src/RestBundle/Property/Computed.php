<?php

namespace DavesWeblab\RestBundle\Property;

abstract class Computed
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $supportedClass
     */
    protected $supportedClass;

    /**
     * @var mixed $element
     */
    protected $element;

    public function __construct(string $name, string $supportedClass)
    {
        $this->name = $name;
        $this->supportedClass = $supportedClass;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param mixed $element
     *
     * @return $this
     */
    public function setElement($element)
    {
        $this->element = $element;
        return $this;
    }

    /**
     * @param mixed $element
     *
     * @return bool
     */
    public function supports($element = null)
    {
        $element = $element ?: $this->element;

        return is_subclass_of($element, $this->supportedClass);
    }

    /**
     * Get the computed value from the given element.
     *
     * @return mixed
     */
    public abstract function get();

    /**
     * Set a computed value back to the element.
     *
     * @param $value
     *
     * @return mixed
     */
    public abstract function set($value);
}