<?php

namespace Roots\Sage\Template;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\ViewServiceProvider;

/**
 * Class BladeProvider
 */
class BladeProvider extends ViewServiceProvider
{
    /**
     * @param ContainerContract $container
     * @param array             $config
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct(?ContainerContract $container = null, $config = [])
    {
        /** @noinspection PhpParamsInspection */
        parent::__construct($container ?: Container::getInstance());

        $this->app->bindIf('config', function () use ($config) {
            return new \Roots\Sage\Config($config);
        }, true);
    }

    /**
     * Bind required instances for the service provider.
     */
    public function register()
    {
        $this->registerFilesystem();
        $this->registerEvents();
        $this->registerEngineResolver();
        $this->registerViewFinder();
        $this->registerFactory();
        $this->registerBladeCompiler();
        return $this;
    }

    /**
     * Register the view factory without Laravel application lifecycle hooks.
     */
    public function registerFactory()
    {
        $this->app->singleton('view', function ($app) {
            $factory = $this->createFactory(
                $app['view.engine.resolver'],
                $app['view.finder'],
                $app['events']
            );
            $factory->setContainer($app);
            $factory->share('app', $app);
            return $factory;
        });
    }

    /**
     * WordPress handles the request lifecycle; there is no Laravel terminating hook.
     */
    public function registerBladeEngine($resolver)
    {
        $resolver->register('blade', function () {
            return new \Illuminate\View\Engines\CompilerEngine(
                $this->app->make('blade.compiler'),
                $this->app->make('files')
            );
        });
    }

    /**
     * Register Filesystem
     */
    public function registerFilesystem()
    {
        $this->app->bindIf('files', Filesystem::class, true);
        return $this;
    }

    /**
     * Register the events dispatcher
     */
    public function registerEvents()
    {
        $this->app->bindIf('events', Dispatcher::class, true);
        return $this;
    }

    /**
     * Register the view finder implementation.
     */
    public function registerViewFinder()
    {
        $this->app->bindIf('view.finder', function ($app) {
            $config = $this->app['config'];
            $paths = $config['view.paths'];
            $namespaces = $config['view.namespaces'];
            $finder = new FileViewFinder($app['files'], $paths);
            array_map([$finder, 'addNamespace'], array_keys($namespaces), $namespaces);
            return $finder;
        }, true);
        return $this;
    }
}
