<?php

namespace DavesWeblab\RestBundle\Serializer\Context\Embedded;

use DavesWeblab\RestBundle\Config\Config;
use DavesWeblab\RestBundle\Data\DataType;
use DavesWeblab\RestBundle\Property\Computed;
use DavesWeblab\RestBundle\Serializer\Context\AbstractContext;
use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;

abstract class AbstractEmbeddedContext extends AbstractContext implements EmbeddedContextInterface
{
    /**
     * @var ContextInterface $parent
     */
    private $parent;

    /**
     * @var ContextInterface $rootContext
     */
    private $rootContext;

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(ContextInterface $parent)
    {
        $this->parent = $parent;
        $this->rootContext = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootContext()
    {
        if (!$this->rootContext) {
            $parent = $this->getParent();

            while ($parent && $parent instanceof EmbeddedContextInterface) {
                $parent = $parent->getParent();
            }

            $this->rootContext = $parent;
        }

        return $this->rootContext;
    }

    /**
     * {@inheritdoc}
     */
    public function hasObject($object)
    {
        if (parent::hasObject($object)) {
            return true;
        }

        if ($this->getParent()) {
            return $this->getParent()->hasObject($object);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setComputeds(array $computeds = [])
    {
        $this->getRootContext()->setComputeds($computeds);
    }

    /**
     * {@inheritdoc}
     */
    public function addComputeds(array $computeds = [])
    {
        $this->getRootContext()->addComputeds($computeds);
    }

    /**
     * {@inheritdoc}
     */
    public function getComputeds($data)
    {
        return $this->getRootContext()->getComputeds($data);
    }

    /**
     * {@inheritdoc}
     */
    public function buildNormalizedValueFromFieldDefinition($value, Data $fieldDefinition = null, $data = null, array $config = [])
    {
        return $this->getRootContext()->buildNormalizedValueFromFieldDefinition($value, $fieldDefinition, $data, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function buildNormalizedValueFromComputed(Computed $computed)
    {
        return $this->getRootContext()->buildNormalizedValueFromComputed($computed);
    }

    /**
     * {@inheritdoc}
     */
    public function setDataType(DataType $dataType)
    {
        $this->getRootContext()->setDataType($dataType);
    }

    /**
     * {@inheritdoc}
     */
    public function isRelation(Data $fieldDefinition = null)
    {
        return $this->getRootContext()->isRelation($fieldDefinition);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(Config $config)
    {
        $this->getRootContext()->setConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->getRootContext()->getConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function setView(string $view)
    {
        $this->getRootContext()->setView($view);
    }

    /**
     * {@inheritdoc}
     */
    public function getView(): string
    {
        return $this->getRootContext()->getView();
    }

    /**
     * {@inheritdoc}
     */
    public static function supports(string $format): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setTransformers(array $transformers)
    {
        $this->getRootContext()->setTransformers($transformers);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransformers(): array
    {
        return $this->getRootContext()->getTransformers();
    }

    /**
     * {@inheritdoc}
     */
    public function transform(Data $fieldDefinition = null, $data, array $config = [])
    {
        return $this->getRootContext()->transform($fieldDefinition, $data, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function stopsNormalization(Data $fieldDefinition = null, $data = null, array $config = [])
    {
        return $this->getRootContext()->stopsNormalization($fieldDefinition, $data, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function areRelationsIncluded(): bool
    {
        return $this->getRootContext()->areRelationsIncluded();
    }

    /**
     * {@inheritdoc}
     */
    public function encode()
    {
        return $this->getRootContext()->encode();
    }

    /**
     * {@inheritdoc}
     */
    public function buildRelationData($id, $data)
    {
        return $this->getRootContext()->buildRelationData($id, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getMandatoryAttributes()
    {
        return $this->getRootContext()->getMandatoryAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function buildEmbeddedContext($data)
    {
        return $this->getRootContext()->buildEmbeddedContext($data);
    }
}