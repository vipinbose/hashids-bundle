<?php declare(strict_types=1);

namespace Vipinbose\HashidsBundle\Attribute;

use Attribute;

#[Attribute]
class HashidsRequestConverterAttribute
{
    public function __construct(public array $requestAttributesKeys) {}
}
