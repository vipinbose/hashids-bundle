<?php

declare(strict_types=1);

namespace Vipinbose\HashidsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vipinbose\HashidsBundle\DependencyInjection\VipinboseHashidsExtension;

class VipinboseHashidsBundle extends Bundle
{
    public function getContainerExtension(): VipinboseHashidsExtension
    {
        return new VipinboseHashidsExtension;
    }
}
