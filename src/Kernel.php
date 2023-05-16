<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getCacheDir(): string
    {
		if( ! file_exists(getenv("USERPROFILE").'/symfony/'))
			return dirname(__DIR__).'/var/'.$this->environment.'/cache';
        else 
			return getenv("USERPROFILE").'/symfony/var/'.$this->environment.'/cache';
    }
    public function getLogDir(): string
    {
		if( ! file_exists(getenv("USERPROFILE").'/symfony/'))
			return dirname(__DIR__).'/var/'.$this->environment.'/log';
        else
			return getenv("USERPROFILE").'/symfony/var/'.$this->environment.'/log';
    }
}
