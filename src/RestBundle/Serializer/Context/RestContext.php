<?php

namespace DavesWeblab\RestBundle\Serializer\Context;

use DavesWeblab\RestBundle\Serializer\Context\Embedded\EmbeddedIterableRestContext;
use DavesWeblab\RestBundle\Serializer\Context\Embedded\EmbeddedRestContext;
use DavesWeblab\RestBundle\Serializer\Json\JsonEntity;
use DavesWeblab\RestBundle\Serializer\Json\Namespaces\JsonNamespace;
use DavesWeblab\RestBundle\Serializer\Json\Namespaces\NoNamespaceAvailableException;
use DavesWeblab\RestBundle\Serializer\Json\Namespaces\Rest\AssetNamespace;
use DavesWeblab\RestBundle\Serializer\Json\Namespaces\Rest\FieldCollectionNamespace;
use DavesWeblab\RestBundle\Serializer\Json\Namespaces\Rest\ObjectbrickNamespace;
use DavesWeblab\RestBundle\Serializer\Json\Namespaces\Rest\ObjectListingNamespace;
use DavesWeblab\RestBundle\Serializer\Json\Namespaces\Rest\ObjectNamespace;
use ICanBoogie\Inflector;

class RestContext extends AbstractContext
{
    /**
     * @var array $json
     */
    protected $json = [];

    /**
     * @var JsonNamespace[]
     */
    protected $namespaces;

    /**
     * @var string $rootNamespace
     */
    protected $rootNamespace;

    public function __construct()
    {
        $this->namespaces = [
            new ObjectNamespace(),
            new ObjectListingNamespace(),
            new AssetNamespace(),
            new FieldCollectionNamespace(),
            new ObjectbrickNamespace()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function supports(string $format): bool
    {
        return $format === "json";
    }

    /**
     * @param $data
     *
     * @return string
     *
     * @throws NoNamespaceAvailableException
     */
    public function getNamespace($data)
    {
        foreach ($this->namespaces as $namespace) {
            if ($namespace->supports($data)) {
                return $namespace->getNamespace($data);
            }
        }

        throw new NoNamespaceAvailableException("No namespace available for " . get_class($data));
    }

    /**
     * {@inheritdoc}
     */
    public function add($data, array $config = null, bool $isEmbedded = false)
    {
        // ignore circular dependencies and duplicates
        if ($this->hasObject($data)) {
            return;
        }

        // track the object in the object store
        $this->trackObject($data);

        $namespace = $this->getNamespace($data);

        $root = empty($this->json);

        // ignore relations and don't add them to the json
        // each relation will be loaded with another ajax call
        if (!$root && !$this->areRelationsIncluded()) {
            return;
        }

        // root is singular, relations are pluralized
        // e.g. asset => assets

        $entity = new JsonEntity($data);

        if ($root || $namespace == $this->rootNamespace) {
            $entity->setRoot(true);
        }

        if ($data instanceof \Traversable) {
            if ($root) {
                $this->rootNamespace = $namespace;
            }

            $namespace = Inflector::get()->pluralize($namespace);

            if (!array_key_exists($namespace, $this->json)) {
                $this->json[$namespace] = [];
            }
        } else if ($root) {
            // only one root available
            // singular namespace
            $this->json[$namespace] = $entity;
        } else {
            // relation, pluralized namespace
            $namespace = Inflector::get()->pluralize($namespace);

            // ensure namespace
            if (!array_key_exists($namespace, $this->json)) {
                $this->json[$namespace] = [];
            }

            $this->json[$namespace][] = $entity;
        }

        $entity->setConfig($config);
        $this->push($entity);

        return $entity;
    }

    public function encode()
    {
        return json_encode($this->json);
    }

    /**
     * {@inheritdoc}
     */
    public function buildRelationData($id, $data)
    {
        return [
            "id" => $id,
            "type" => $this->getNamespace($data)
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getMandatoryAttributes()
    {
        return ["id"];
    }

    /**
     * {@inheritdoc}
     */
    public function buildEmbeddedContext($data)
    {
        return new EmbeddedRestContext();
    }
}