<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Vipinbose\HashidsBundle\Twig\HashidsExtension;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(HashidsExtension::class, HashidsExtension::class)
        ->autowire()
        ->private()
        ->tag('twig.extension');
};
