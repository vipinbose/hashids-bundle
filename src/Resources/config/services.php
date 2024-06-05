<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Vipinbose\HashidsBundle\EventListener\KernelListener;
use Vipinbose\HashidsBundle\Interfaces\HashidsServiceInterface;
use Vipinbose\HashidsBundle\Service\HashidsService;
use Vipinbose\HashidsBundle\ValueResolver\HashIdValueResolver;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(HashidsService::class, HashidsService::class)
        ->args([
            param('vipinbose.hashids.salt'),
            param('vipinbose.hashids.min_hash_length'),
            param('vipinbose.hashids.alphabet')
        ])
        ->autowire()
        ->public()
        ->alias(HashidsServiceInterface::class, HashidsService::class);

    $container->services()
        ->set(HashIdValueResolver::class, HashIdValueResolver::class)
        ->arg('$enable', param('vipinbose.hashids.enable_value_resolver'))
        ->autowire()
        ->public()
        ->tag('controller.argument_value_resolver', ['priority' => 111]);

    $container->services()
        ->set(KernelListener::class, KernelListener::class)
        ->arg('$enable', param('vipinbose.hashids.enable_value_resolver'))
        ->autowire()
        ->autoconfigure()
        ->public();
};
