<?php

namespace DavesWeblab\RestBundle\Serializer\Json;

use DavesWeblab\RestBundle\Serializer\EntityInterface;

class JsonApiEntity extends JsonEntity
{
    /**
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value)
    {
        if (in_array($key, ["id", "type"])) {
            $this->data[$key] = $value;
        } else {
            if (empty($this->data["attributes"])) {
                $this->data["attributes"] = [];
            }

            $this->data["attributes"][$key] = $value;
        }
    }

    /**
     * @param string $key
     * @param $value
     */
    public function setRelation(string $key, $value)
    {
        if (empty($this->data["relationships"])) {
            $this->data["relationships"] = [];
        }

        $this->data["relationships"][$key]["data"] = $value;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        if (array_key_exists($key, @$this->data["attributes"] ?: [])) {
            return $this->data["attributes"][$key];
        }

        if (array_key_exists($key, @$this->data["relationships"] ?: [])) {
            return $this->data["relationships"][$key];
        }

        return null;
    }
}