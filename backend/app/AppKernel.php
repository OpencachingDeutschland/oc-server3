<?php

use Mirsch\Bundle\AdminBundle\Kernel\AdminKernel;

class AppKernel extends AdminKernel
{
    public function registerBundles()
    {
        $bundles = [
            new AppBundle\AppBundle(),
        ];

        return array_merge(parent::registerBundles(), $bundles);
    }

}
