<?php

namespace DavesWeblab\RestBundle\Serializer\Json\Namespaces\Rest;

use DavesWeblab\RestBundle\Serializer\Json\Namespaces\JsonNamespace;
use ICanBoogie\Inflector;
use Pimcore\Model\DataObject\Concrete;

class ObjectNamespace implements JsonNamespace
{
    /**
     * {@inheritdoc}
     */
    public function supports($data)
    {
        return $data instanceof Concrete;
    }

    /**
     * @param Concrete $data
     *
     * @return string
     */
    public function getNamespace($data)
    {
        $inflector = Inflector::get();

        return $inflector->dasherize($inflector->underscore($data->getClassName()));
    }
}