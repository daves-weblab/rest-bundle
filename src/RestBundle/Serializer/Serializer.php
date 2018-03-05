<?php

namespace DavesWeblab\RestBundle\Serializer;

use DavesWeblab\RestBundle\Data\DataType;
use DavesWeblab\RestBundle\Factory\Factory;
use DavesWeblab\RestBundle\Normalizer\NormalizerInterface;
use DavesWeblab\RestBundle\Property\Computed;
use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection;
use Symfony\Component\HttpFoundation\JsonResponse;

class Serializer
{
    /**
     * @var DataType $dataType
     */
    private $dataType;

    /**
     * @var Factory $factory
     */
    private $factory;

    public function __construct(DataType $dataType, Factory $factory)
    {
        $this->dataType = $dataType;
        $this->factory = $factory;
    }

    /**
     * @param $data
     *
     * @return NormalizerInterface|null
     */
    protected function getNormalizer($data)
    {
        foreach ($this->factory->getNormalizers() as $normalizer) {
            if ($normalizer->supports($data)) {
                return $normalizer;
            }
        }

        return null;
    }

    /**
     * @param $data
     *
     * @return bool
     */
    protected function needsNormalization($data)
    {
        return $this->getNormalizer($data) !== null;
    }

    /**
     * @param $data
     *
     * @param ContextInterface $context
     */
    protected function normalizeIterable($data, ContextInterface $context)
    {
        foreach ($data as $item) {
            $context->add($item);
            $this->normalize($context);
        }
    }

    /**
     * @param ContextInterface $context
     */
    protected function normalize(ContextInterface $context)
    {
        $current = $context->pop();

        if (!$current) {
            return;
        }

        $data = $current->getElement();

        if ($normalizer = $this->getNormalizer($data)) {
            $attributes = $context->getMandatoryAttributes();
            $computeds = $context->getComputeds($data);

            $attributes = array_merge($attributes, $normalizer->getSupportedAttributes($data, $context), $computeds->getAttributes());

            foreach ($attributes as $attribute) {
                if ($computeds->isComputedAttribute($attribute)) {
                    $value = $context->buildNormalizedValueFromComputed($computeds->getByAttribute($attribute));
                } else {
                    $value = $normalizer->getAttribute($data, $attribute, $context, $current->getConfig());
                }

                if ($value->isRelation()) {
                    $current->setRelation($attribute, $value->getTransformed());
                } else {
                    $current->set($attribute, $value->getTransformed());
                }

                if (!$value->stopsNormalization()) {
                    if ($this->dataType->isIterable($value->getValue())) {
                        $this->normalizeIterable($value->getValue(), $context);
                    } else if ($this->needsNormalization($value->getValue())) {
                        $context->add($value->getValue(), $value->getConfig());
                        $this->normalize($context);
                    }
                }
            }
        } else if ($this->dataType->isIterable($data)) {
            $this->normalizeIterable($data, $context);
        }
    }

    /**
     * @param $data
     * @param string $view
     * @param string $format
     * @param Computed[] $computeds
     *
     * @return mixed
     */
    public function serialize($data, string $view = "default", $format = "json", array $computeds = [])
    {
        $context = $this->factory->buildContext($format, $view, $computeds);

        if ($data) {
            // add first object to the stack
            $context->add($data);

            // start normalization
            $this->normalize($context);
        }

        // encode the internal representation into the given format (json, ...)
        return $context->encode();
    }

    /**
     * @param $data
     * @param $view
     *
     * @return JsonResponse
     */
    public function json($data, $view = "default")
    {
        return (new JsonResponse())->setJson($this->serialize($data, $view));
    }

    protected function denormalize($fieldDefinition, $value)
    {
        foreach ($this->factory->getDenormalizers() as $denormalizer) {
            if ($denormalizer->supports($fieldDefinition)) {
                return $denormalizer->denormalize($value);
            }
        }

        return $value;
    }

    /**
     * @param array $data
     * @param Concrete|Fieldcollection\Data\AbstractData $object
     */
    public function deserialize(array $data, $object)
    {
        switch (true) {
            case $object instanceof AbstractObject:
                $definition = $object->getClass();
                break;

            case $object instanceof Fieldcollection\Data\AbstractData:
                $definition = $object->getDefinition();
                break;
        }

        foreach ($data as $attribute => $value) {
            $fieldDefinition = $definition->getFieldDefinition($attribute);

            if (!$fieldDefinition) {
                continue;
            }

            $setter = "set" . ucfirst($attribute);

            if (method_exists($object, $setter)) {
                $value = $this->denormalize($fieldDefinition, $value);

                $object->{$setter}($value);
            }
        }
    }
}