<?php

namespace DavesWeblab\RestBundle\Serializer\Context\Embedded;

use DavesWeblab\RestBundle\Serializer\Context\JsonApiContext;

class EmbeddedJsonApiContext extends EmbeddedRestContext
{
    protected $json;

    /**
     * {@inheritdoc}
     */
    public function add($data, array $config = null)
    {
        // ignore cyclic dependencies
        // maybe this should be changed if necessary
//        if ($this->hasObject($data)) {
//            return;
//        }
//
//        $this->trackObject($data);
        $entity = parent::add($data, $config);

        /**
         * @var JsonApiContext $rootContext
         */
        $rootContext = $this->getRootContext();

        $entity->set("type", $rootContext->getNamespace($data));

        return $entity;
    }
}