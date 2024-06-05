<?php declare(strict_types=1);

namespace VipinBose\HashidsBundle\Attribute;

use Attribute;

#[Attribute]
class HashidsRequestConverterAttribute
{
    public function __construct(public array $requestAttributesKeys) {}
}
