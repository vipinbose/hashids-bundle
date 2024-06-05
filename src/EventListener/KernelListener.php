<?php declare(strict_types=1);

namespace VipinBose\HashidsBundle\EventListener;

use VipinBose\HashidsBundle\Attribute\HashidsRequestConverterAttribute;
use VipinBose\HashidsBundle\Interfaces\HashidsServiceInterface;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class KernelListener implements EventSubscriberInterface
{
    public function __construct(
        private bool $enable,
        private HashidsServiceInterface $hashidsService
    ) {}

    public function onKernelController(ControllerEvent $event): void
    {
        if (!$this->enable) {
            return;
        }

        if (!is_array($controllers = $event->getController())) {
            return;
        }

        [$controller, $method] = $controllers;

        $this->resolve($controller, $method, $event);
    }

    private function resolve(object $controller, string $method, ControllerEvent $event): void
    {
        $attributes = (new ReflectionClass($controller))
            ->getMethod($method)
            ->getAttributes(HashidsRequestConverterAttribute::class);

        foreach ($attributes as $attribute) {
            /** @var HashidsRequestConverterAttribute $hashidsParamConverterAttribute */
            $hashidsParamConverterAttribute = $attribute->newInstance();
            $this->handleRequest($event->getRequest(), $hashidsParamConverterAttribute);
        }
    }

    private function handleRequest(
        Request $request,
        HashidsRequestConverterAttribute $hashidsParamConverterAttribute
    ): void {
        foreach ($hashidsParamConverterAttribute->requestAttributesKeys as $requestAttributesHashKey) {
            /** @var string|null $hash */
            $hash = $request->attributes->get($requestAttributesHashKey);
            if ($hash === null) {
                continue;
            }

            $hash = (string) $hash;
            $hashids = $this->hashidsService->decode($hash);

            if ($this->hasHashidDecoded($hashids)) {
                $request->attributes->set($requestAttributesHashKey, current($hashids));
            }
        }
    }

    private function hasHashidDecoded(mixed $hashids): bool
    {
        return $hashids && is_iterable($hashids);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController'
        ];
    }
}
