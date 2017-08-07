<?php

namespace Cethyworks\ContentInjectorBundle\Tests\Functional\Mock;

use Cethyworks\ContentInjectorBundle\CethyworksContentInjectorBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

class MockKernel extends Kernel
{
    /**
     * Returns an array of bundles to register.
     *
     * @return BundleInterface[] An array of bundle instances
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new CethyworksContentInjectorBundle()
        ];
    }
    public function getRootDir()
    {
        return __DIR__;
    }
    public function getCacheDir()
    {
        return dirname(__DIR__).'/../../var/cache/'. $this->getEnvironment();
    }
    public function getLogDir()
    {
        return dirname(__DIR__).'/../../var/logs';
    }
    /**
     * Loads the container configuration.
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__. '/config.yml');
    }
}
