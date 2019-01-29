<?php

namespace Intersect\Core;

use Intersect\Core\Registry\ClassRegistry;

class ClassResolver {

    /** @var ClassRegistry */
    private $classRegistry;

    /** @var ParameterResolver */
    private $parameterResolver;

    /** @var array */
    private $resolvedClasses = [];

    public function __construct(ClassRegistry $classRegistry)
    {
        $this->classRegistry = $classRegistry;
        $this->parameterResolver = new ParameterResolver($this);
    }

    /**
     * @param $key
     * @param array $namedParameters
     * @return mixed|object
     * @throws \Exception
     */
    public function resolve($key, $namedParameters = [])
    {
        $registeredClass = $this->classRegistry->get($key);

        if (!is_null($registeredClass))
        {
            $class = $registeredClass->getClass();
            $isSingleton = $registeredClass->isSingleton();

            if ($class instanceof \Closure)
            {
                return $class();
            }

            if ($isSingleton && array_key_exists($key, $this->resolvedClasses))
            {
                return $this->resolvedClasses[$key];
            }

            if (is_object($class))
            {
                if ($class instanceof \Closure)
                {
                    $class = $class();
                }

                $this->resolvedClasses[$key] = $class;

                return $class;
            }
        }

        return $this->resolveClass((is_null($registeredClass) ? $key : $registeredClass->getClass()), $namedParameters);
    }

    /**
     * @param $class
     * @param $namedParameters
     * @return object|null
     * @throws \Exception
     */
    private function resolveClass($class, $namedParameters)
    {
        $reflectionClass = null;
        
        try {
            $reflectionClass = new \ReflectionClass($class);
            $constructor = $reflectionClass->getConstructor();
    
            if (is_null($constructor))
            {
                if (!$reflectionClass->isInstantiable())
                {
                    throw new \Exception('cannot resolve class: ' . $reflectionClass->getName());
                }
    
                $resolvedClass = $reflectionClass->newInstance();
            }
            else
            {
                $parameters = $this->parameterResolver->resolve($constructor->getParameters(), $namedParameters);
    
                $resolvedClass = null;
    
                if ($constructor->isPublic())
                {
                    $resolvedClass = $reflectionClass->newInstanceArgs($parameters);
                }
                else
                {
                    throw new \Exception('class "' . $reflectionClass->getName() . '" does not have a public constructor so it is not accessible through autoloading');
                }
            }
        } catch (\Exception $e) {
            // 
        }

        $this->resolvedClasses[$class] = $resolvedClass;

        return $resolvedClass;
    }

}