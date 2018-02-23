<?php

namespace DavesWeblab\RestBundle\Serializer\Json;

use DavesWeblab\RestBundle\Serializer\EntityInterface;

class JsonEntity implements EntityInterface, \JsonSerializable
{
    protected $element;
    protected $config;
    protected $data = [];
    protected $root = false;

    public function __construct($element)
    {
        $this->element = $element;
    }

    /**
     * @return mixed
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config = null)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoot(bool $root)
    {
        $this->root = $root;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot(): bool
    {
        return $this->root;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function setRelation(string $key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return @$this->data[$key];
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getRelation(string $key)
    {
        return $this->get($key);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->data;
    }
}