<?php

namespace OcTest\Utils;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Intl\Exception\NotImplementedException;

class ConfigLoaderDummy implements LoaderInterface
{
    private $resource;

    public function load($resource, $type = null): void
    {
        $this->resource = $resource;
    }

    public function getLoadedResource()
    {
        return $this->resource;
    }

    public function supports($resource, $type = null): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getResolver(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setResolver(LoaderResolverInterface $resolver): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }
}
