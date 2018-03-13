<?php

namespace DavesWeblab\RestBundle\Normalizer\Transformer;

use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data\Objectbricks;
use Pimcore\Model\DataObject\Objectbrick;

class ObjectBrickAsIdTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function supports($data, array $config = [], bool $supportOnly = false)
    {
        if ($this->isEmbed($data, $config) && !$supportOnly) {
            return false;
        }

        return $data instanceof Objectbrick\Data\AbstractData;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmbed($data, array $config = [])
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function stopsNormalization()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($data, ContextInterface $context, array $config = null)
    {
        return self::transformObjectbrickId($data);
    }

    /**
     * @param Objectbrick\Data\AbstractData $item
     *
     * @return string
     */
    public static function transformObjectbrickId(Objectbrick\Data\AbstractData $item)
    {
        return "{$item->getObject()->getId()}-{$item->getType()}";
    }
}