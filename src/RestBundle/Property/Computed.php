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

    /**
     * @var bool $isRelationType
     */
    protected $isRelationType;

    public function __construct(string $name, string $supportedClass, bool $isRelationType = false)
    {
        $this->name = $name;
        $this->supportedClass = $supportedClass;
        $this->isRelationType = $isRelationType;
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
     * @return bool
     */
    public function isRelation()
    {
        return $this->isRelationType;
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

    /**
     * @return null|array
     */
    public function getConfig()
    {
        return null;
    }
}