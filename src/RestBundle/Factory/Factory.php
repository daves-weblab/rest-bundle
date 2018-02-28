<?php

namespace DavesWeblab\RestBundle\Factory;

use DavesWeblab\RestBundle\Config\Config;
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
use DavesWeblab\RestBundle\Property\Computed;
use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use DavesWeblab\RestBundle\Serializer\Context\RestContext;

class Factory
{
    /**
     * @var DataType $dataType
     */
    private $dataType;

    /**
     * @var Config $config
     */
    private $config;

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

    /**
     * @var Computed[] $computeds
     */
    private $computeds = [];

    public function __construct(DataType $dataType, Config $config)
    {
        $this->dataType = $dataType;
        $this->config = $config;

        $normalization = $config->getNormalizationConfig();

        /**
         * build normalizers
         */
        $normalizers = array_merge([
            ObjectNormalizer::class,
            FieldCollectionNormalizer::class,
            AssetNormalizer::class
        ], $normalization->get("normalizer", []));

        foreach ($normalizers as $normalizer) {
            $this->normalizers[] = $this->buildNormalizer($normalizer);
        }

        /**
         * build normalizer transformers
         */
        $normalizeTransformers = array_merge([
            RelationAsIdTransformer::class,
            FieldCollectionAsIdTransformer::class,
            DateTransformer::class,
            MultiselectTransformer::class
        ], $normalization->get("transformer", []));

        foreach ($normalizeTransformers as $transformer) {
            $this->normalizeTransformers[] = $this->buildNormalizerTransformer($transformer);
        }

        /**
         * build denormalizers
         */
        $denormalization = $config->getDenormalizationConfig();

        $denormalizers = array_merge([
            DataObjectDenormalizer::class,
            ImageDenormalizer::class,
            DateDenormalizer::class
        ], $denormalization->get("denormalizer"));

        foreach ($denormalizers as $denormalizer) {
            $this->denormalizers[] = $this->buildDenormalizer($denormalizer);
        }

        /**
         * context definitions
         */
        $this->contextDefinitions = $config->getContextConfig();

        /**
         * build computeds
         */
        $computeds = $config->getComputeds();

        foreach ($computeds as $computed) {
            $this->computeds[] = $this->buildComputed($computed);
        }
    }

    /**
     * @return NormalizerInterface[]
     */
    public function getNormalizers()
    {
        return $this->normalizers;
    }

    /**
     * @return DenormalizerInterface[]
     */
    public function getDenormalizers()
    {
        return $this->denormalizers;
    }

    /**
     * @return Transformer[]
     */
    public function getNormalizeTransformers()
    {
        return $this->normalizeTransformers;
    }

    public function getContextDefintions()
    {
        return $this->contextDefinitions;
    }

    /**
     * @param string $format
     * @param string $view
     * @param Computed[] $computeds
     *
     * @return mixed
     */
    public function buildContext(string $format, string $view, array $computeds = [])
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
                throw new \InvalidArgumentException("Invalid context class given. {$contextDefintion}");
            }
        }

        if (!$context || !$context instanceof ContextInterface) {
            throw new \InvalidArgumentException("Unsupported format given. {$format}");
        }

        $context->setDataType($this->dataType);
        $context->setConfig($this->config);
        $context->setView($view);
        $context->setTransformers($this->getNormalizeTransformers());
        $context->setComputeds($this->computeds);
        $context->addComputeds($computeds);

        return $context;
    }

    /**
     * @param string $class
     *
     * @return NormalizerInterface
     *
     * @throws \InvalidArgumentException
     */
    public function buildNormalizer(string $class)
    {
        return $this->build($class, NormalizerInterface::class);
    }

    /**
     * @param string $class
     *
     * @return Transformer
     *
     * @throws \InvalidArgumentException
     */
    public function buildNormalizerTransformer(string $class)
    {
        return $this->build($class, Transformer::class);
    }

    /**
     * @param string $class
     *
     * @return DenormalizerInterface
     *
     * @throws \InvalidArgumentException
     */
    public function buildDenormalizer(string $class)
    {
        return $this->build($class, DenormalizerInterface::class, [$this->dataType]);
    }

    /**
     * @param string $class
     *
     * @return Computed
     *
     * @throws \InvalidArgumentException
     */
    public function buildComputed(string $class)
    {
        return $this->build($class, Computed::class);
    }

    /**
     * @param string $class
     * @param string $instanceOf
     *
     * @return mixed
     */
    private function build(string $class, string $instanceOf, array $params = [])
    {
        $element = new $class(...$params);

        if (!is_subclass_of($element, $instanceOf)) {
            throw new \InvalidArgumentException("Element does not match given type. {$class} is not instance of {$instanceOf}");
        }

        return $element;
    }
}