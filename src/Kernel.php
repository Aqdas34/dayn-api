<?php

namespace App;

Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait; 
}
