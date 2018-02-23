<?php

namespace DavesWeblab\RestBundle\Serializer;

interface EntityInterface {
    /**
     * @return mixed
     */
    public function getElement();

    /**
     * @param array|null $config
     *
     * @return mixed
     */
    public function setConfig(array $config = null);

    /**
     * @return array|null
     */
    public function getConfig();

    /**
     * @param bool $root
     */
    public function setRoot(bool $root);

    /**
     * @return bool
     */
    public function isRoot(): bool;

    /**
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value);

    /**
     * @param string $key
     * @param $value
     */
    public function setRelation(string $key, $value);

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key);

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getRelation(string $key);
}