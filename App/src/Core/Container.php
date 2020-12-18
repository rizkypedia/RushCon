<?php


namespace Rushcon\Core;


/**
 * Class Container
 */
class Container
{
    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @param mixed|object $abstract
     * @param null $concrete
     */
    public function set($abstract, $concrete = null)
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }
        $this->instances[$abstract] = $concrete;
    }

    /**
     * @param mixed|null|object $abstract
     * @param array $parameters
     *
     * @return mixed|null|object
     */
    public function get($abstract, $parameters = [])
    {
        // if we don't have it, just register it
        if (!isset($this->instances[$abstract])) {
            $this->set($abstract);
        }

        return $this->resolve($this->instances[$abstract], $parameters);
    }

    /**
     * resolve single
     *
     * @param mixed|object $concrete
     * @param mixed|object $parameters
     *
     * @return mixed|object
     */
    public function resolve($concrete, $parameters)
    {
        if ($concrete instanceof \Closure && is_callable($concrete)) {
            return $concrete($this, $parameters);
        }

        $reflector = new \ReflectionClass($concrete);
        // check if class is instantiable
        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable");
        }

        // get class constructor
        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            // get new instance from class
            return $reflector->newInstance();
        }

        // get constructor params
        $parameters = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters);

        // get new instance with dependencies resolved
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * get all dependencies resolved
     *
     * @param mixed|object $parameters
     *
     * @return array
     */
    public function getDependencies($parameters)
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            // get the type hinted class
            $dependency = $parameter->getClass();
            if ($dependency === null) {
                // check if default value for a parameter is available
                if ($parameter->isDefaultValueAvailable()) {
                    // get default value of parameter
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \Exception("Can not resolve class dependency {$parameter->name}");
                }
            } else {
                // get dependency resolved
                $dependencies[] = $this->get($dependency->name);
            }
        }

        return $dependencies;
    }
}
