<?php

declare(strict_types=1);

namespace Vipinbose\HashidsBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Yaml\Yaml;
use Vipinbose\HashidsBundle\EventListener\KernelListener;
use Vipinbose\HashidsBundle\Interfaces\HashidsServiceInterface;
use Vipinbose\HashidsBundle\Service\HashidsService;
use Vipinbose\HashidsBundle\ValueResolver\HashIdValueResolver;

class VipinboseHashidsBundle extends AbstractBundle
{

    public function configure(DefinitionConfigurator $definition): void
    {
        $rootNode = $definition->rootNode();
        $rootNode
            ->children()
                ->scalarNode('salt')
                    ->defaultValue('')
                    ->info('if set, the hashids will differ from everyone else\'s.')
                ->end()
                ->integerNode('min_hash_length')
                    ->info('if set, will generate minimum length for the id.')
                    ->defaultValue(4)
                    ->min(4)
                ->end()
                ->scalarNode('alphabet')
                    ->info('if set, will use only characters of alphabet string.')
                    ->defaultValue('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890')
                ->end()
                ->booleanNode('enable_value_resolver')
                    ->info('if true, try to convert all arguments in controller. Except use "_hash_" in specific routing variables.')
                    ->defaultFalse()
                ->end()
            ->end()
        ;
        
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
        $container->import('../config/twig.yaml');

        $this->createParameters($builder, $config);
        $container->services()
            ->set(HashidsService::class, HashidsService::class)
            ->arg(0, $config['salt'])
            ->arg(1, $config['min_hash_length'])
            ->arg(2, $config['alphabet'])
            ->autowire()
            ->public()
            ->alias(HashidsServiceInterface::class, HashidsService::class);

        $container->services()
            ->set(HashIdValueResolver::class, HashIdValueResolver::class)
            ->arg('$enable', $config['enable_value_resolver'])
            ->autowire()
            ->public()
            ->tag('controller.argument_value_resolver', ['priority' => 150]);

        $container->services()
            ->set(KernelListener::class, KernelListener::class)
            ->arg('$enable', $config['enable_value_resolver'])
            ->autowire()
            ->autoconfigure()
            ->public();
    }

    private function createParameters(ContainerBuilder $container, array $config): void
    {
        /** @var array $yamlParameter */
        $yamlParameter =  Yaml::parseFile(__DIR__ . '../config/parameters.yaml');
        $parameters = $yamlParameter['hashids']['parameters'] ?? [];

        foreach ($parameters as $parameter) {
            $container->setParameter("vipinbose.hashids.{$parameter}", $config[$parameter]);
        }
    }
}
