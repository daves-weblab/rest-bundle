<?php

namespace DavesWeblab\RestBundle\Serializer\Context\Embedded;

use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;

interface EmbeddedContextInterface extends ContextInterface
{
    /**
     * @return ContextInterface
     */
    public function getParent();

    /**
     * @param ContextInterface $parent
     */
    public function setParent(ContextInterface $parent);

    /**
     * @return ContextInterface
     */
    public function getRootContext();
}