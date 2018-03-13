<?php

namespace DavesWeblab\RestBundle\Serializer\Context\Embedded;

use DavesWeblab\RestBundle\Serializer\Context\JsonApiContext;
use DavesWeblab\RestBundle\Serializer\Json\Namespaces\NoNamespaceAvailableException;

class EmbeddedJsonApiContext extends EmbeddedRestContext
{
    protected $json;

    /**
     * {@inheritdoc}
     */
    public function add($data, array $config = null, bool $isEmbedded = false)
    {
        $entity = parent::add($data, $config, $isEmbedded);

        /**
         * @var JsonApiContext $rootContext
         */
        $rootContext = $this->getRootContext();

        try {
            $entity->set("type", $rootContext->getNamespace($data));
        } catch(NoNamespaceAvailableException $e) {
            // no namespace available, do not set type
        }

        return $entity;
    }
}