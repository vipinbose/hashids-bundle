<?php

declare(strict_types=1);

namespace Vipinbose\HashidsBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Vipinbose\HashidsBundle\Interfaces\HashidsServiceInterface;

class HashidsExtension extends AbstractExtension
{

    public function __construct(private HashidsServiceInterface $hashids)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('hashids_encode', [$this, 'encode']),
            new TwigFilter('hashids_decode', [$this, 'decode']),
        ];
    }

    public function encode(int $number): string
    {
        return $this->hashids->encode($number);
    }

    /**
     * @return array<int, ?int>
     */
    public function decode(string $hash): array
    {
        return $this->hashids->decode($hash);
    }
}
