<?php

namespace DavesWeblab\RestBundle\Serializer;

use DavesWeblab\RestBundle\Data\DataType;
use DavesWeblab\RestBundle\Denormalizer\DataObjectDenormalizer;
use DavesWeblab\RestBundle\Denormalizer\DateDenormalizer;
use DavesWeblab\RestBundle\Denormalizer\DenormalizerInterface;
use DavesWeblab\RestBundle\Denormalizer\ImageDenormalizer;
use DavesWeblab\RestBundle\Normalizer\AssetNormalizer;
use DavesWeblab\RestBundle\Normalizer\FieldCollectionNormalizer;
use DavesWeblab\RestBundle\Normalizer\NormalizerInterface;
use DavesWeblab\RestBundle\Normalizer\ObjectNormalizer;
use DavesWeblab\RestBundle\Normalizer\Transformer\DateTransformer;
use DavesWeblab\RestBundle\Normalizer\Transformer\FieldCollectionAsIdTransformer;
use DavesWeblab\RestBundle\Normalizer\Transformer\MultiselectTransformer;
use DavesWeblab\RestBundle\Normalizer\Transformer\RelationAsIdTransformer;
use DavesWeblab\RestBundle\Normalizer\Transformer\Transformer;
use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use DavesWeblab\RestBundle\Serializer\Context\JsonApiContext;
use DavesWeblab\RestBundle\Serializer\Context\RestContext;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection;
use Symfony\Component\HttpFoundation\JsonResponse;

class Serializer
{
    /**
     * @var \DavesWeblab\RestBundle\Config\Config $config
     */
    private $config;

    /**
     * @var DataType $dataType
     */
    private $dataType;

    /**
     * @var NormalizerInterface[] $normalizers
     */
    private $normalizers = [];

    /**
     * @var Transformer[] $normalizeTransformers
     */
    private $normalizeTransformers = [];

    /**
     * @var string[] $contextDefinitions
     */
    private $contextDefinitions = [];

    /**
     * @var DenormalizerInterface[] $denormalizers
     */
    private $denormalizers = [];

    public function __construct(\DavesWeblab\RestBundle\Config\Config $config, DataType $dataType)
    {
        $this->config = $config;
        $this->dataType = $dataType;

        $normalization = $config->getNormalizationConfig();

        // merge normalizers from normalization config if available
        $this->normalizers = array_merge([
            new ObjectNormalizer(),
            new FieldCollectionNormalizer(),
            new AssetNormalizer()
        ], $normalization->get("normalizer", []));

        // merge transformers from normalization config if available
        $this->normalizeTransformers = array_merge([
            new RelationAsIdTransformer(),
            new FieldCollectionAsIdTransformer(),
            new DateTransformer(),
            new MultiselectTransformer()
        ], $normalization->get("transformer", []));

        // merge contexts from normalization config if available
        $this->contextDefinitions = array_merge([
            JsonApiContext::class
        ], $normalization->get("context", []));

        $this->denormalizers = [
            new DataObjectDenormalizer($dataType),
            new ImageDenormalizer($dataType),
            new DateDenormalizer($dataType)
        ];
    }

    /**
     * @param $data
     *
     * @return NormalizerInterface|null
     */
    protected function getNormalizer($data)
    {
        foreach ($this->normalizers as $normalizer) {
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
            $attributes = array_merge($attributes, $normalizer->getSupportedAttributes($data, $context));

            foreach ($attributes as $attribute) {
                $value = $normalizer->getAttribute($data, $attribute, $context, $current->getConfig());

                if($value->isRelation()) {
                    $current->setRelation($attribute, $value->getTransformed());
                } else {
                    $current->set($attribute, $value->getTransformed());
                }

                if(!$value->stopsNormalization()) {
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
     * @param string $format
     *
     * @return mixed
     */
    public function serialize($data, string $view = "default", $format = "json")
    {
        $context = null;

        // ContextInterface classes have a static function supports
        // which defines if the context is able to handle the given format
        foreach ($this->contextDefinitions as $contextDefintion) {
            try {
                // call the static supports method
                if (call_user_func("{$contextDefintion}::supports", $format)) {
                    $context = new $contextDefintion();
                }
            } catch (\Throwable $e) {
                throw new \InvalidArgumentException("Invalid Context Class given. {$contextDefintion}");
            }
        }

        // sanity check
        if (!$context || !$context instanceof ContextInterface) {
            throw new \InvalidArgumentException("Invalid format given. {$format}");
        }

        // data type dependency
        $context->setDataType($this->dataType);

        // config for normalizers/denormalizers/...
        $context->setConfig($this->config);

        // set view based on configuration
        $context->setView($view);

        // enable transformers for the context
        $context->setTransformers($this->normalizeTransformers);

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
        foreach ($this->denormalizers as $denormalizer) {
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