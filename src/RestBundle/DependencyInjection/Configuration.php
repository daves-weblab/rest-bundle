<?php

namespace DavesWeblab\RestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('daves_weblab_rest');

        $this->addDataType($rootNode);
        $this->addContext($rootNode);
        $this->addNormalization($rootNode);
        $this->addDenormalization($rootNode);
        $this->addComputeds($rootNode);

        $this->addObjects($rootNode);
        $this->addFieldcollections($rootNode);
        $this->addObjectbricks($rootNode);
        $this->addAssets($rootNode);
        // todo $this->addDocuments($rootNode);

        return $treeBuilder;
    }

    protected function addDataType(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode("dataTypes")
                    ->children()
                        ->arrayNode("relationTypes")
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    protected function addContext(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode("context")
                    ->scalarPrototype()->end()
                ->end()
            ->end();
    }

    protected function addNormalization(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode("normalization")
                    ->children()
                        // list of normalizers
                        ->arrayNode("normalizer")
                            ->scalarPrototype()->end()
                        ->end()

                        // list of normalization transformers
                        ->arrayNode("transformer")
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    protected function addComputeds(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->variableNode("computeds")->end()
            ->end();
    }

    protected function addDenormalization(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode("denormalization")
                    ->children()
                        ->arrayNode("denormalizer")
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    protected function addObjects(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->booleanNode("embedRelations")->defaultFalse()->end()
                ->variableNode("objects")->end()
            ->end();
    }

    protected function addFieldcollections(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->booleanNode("embedFieldcollections")->defaultFalse()->end()
            ->variableNode("fieldcollections")->end()
            ->end();
    }

    protected function addObjectbricks(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->booleanNode("embedObjectbricks")->defaultFalse()->end()
            ->variableNode("objectbricks")->end()
            ->end();
    }

    protected function addAssets(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode("assets")
                    ->children()
                        ->variableNode("fields")->end()
                    ->end()
                ->end()
            ->end();
    }
}
