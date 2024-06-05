<?php

declare(strict_types=1);

namespace Vipinbose\HashidsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

class VipinboseHashidsExtension extends Extension
{
    private const string DIR_CONFIG = '/../Resources/config';
    private const string DIR_CONFIG_PARAM = self::DIR_CONFIG . '/parameters.yaml';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . self::DIR_CONFIG));
        $loader->load('services.yaml');
        $loader->load('twig.yaml');

        $this->createParameters($container, $config);
    }

    private function createParameters(ContainerBuilder $container, array $config): void
    {
        /** @var array $yamlParameter */
        $yamlParameter =  Yaml::parseFile(__DIR__ . self::DIR_CONFIG_PARAM);
        $parameters = $yamlParameter['hashids']['parameters'] ?? [];

        foreach ($parameters as $parameter) {
            $container->setParameter("vipinbose.hashids.{$parameter}", $config[$parameter]);
        }
    }

    public function getAlias(): string
    {
        return Configuration::ALIAS;
    }
}
