<?php

namespace Tiup\LaravelTool\Routing;

use Illuminate\Routing\ResourceRegistrar as OriginalRegistrar;

class ResourceRegistrar extends OriginalRegistrar
{
    // add data to the array
    /**
     * The default actions for a resourceful controller.
     *
     * @var array
     */
    protected $resourceDefaults = ['index', 'create', 'store', 'edit', 'update', 'destroy', 'columns','show','delete','search'];


    /**
     * Add the Column method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @param  array   $options
     * @return \Illuminate\Routing\Route
     */
    protected function addResourceColumns($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name).'/columns';
        $action = $this->getResourceAction($name, $controller, 'columns', $options);

        return $this->router->get($uri, $action);
    }

    protected function addResourceDelete($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name).'/delete';
        $action = $this->getResourceAction($name, $controller, 'delete', $options);
        return $this->router->post($uri, $action);
    }

    protected function addResourceSearch($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name).'/search';
        $action = $this->getResourceAction($name, $controller, 'index', $options);
        return $this->router->post($uri, $action);
    }
}
