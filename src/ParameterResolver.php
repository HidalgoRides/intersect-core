<?php

namespace Intersect\Core;

class ParameterResolver {

    /** @var ClassResolver */
    private $classResolver;

    public function __construct(ClassResolver $classResolver)
    {
        $this->classResolver = $classResolver;
    }

    /**
     * @param array $parameters
     * @param array $namedParameters
     * @return array
     * @throws \Exception
     */
    public function resolve($parameters = [], $namedParameters =[])
    {
        $resolvedParameters = [];

        /** @var \ReflectionParameter $parameter */
        foreach ($parameters as $parameter)
        {
            $parameterClass = $parameter->getClass();

            if (is_null($parameterClass))
            {
                $parameterName = $parameter->getName();

                if (array_key_exists($parameterName, $namedParameters))
                {
                    $resolvedParameters[] = $namedParameters[$parameterName];
                }
                else
                {
                    $defaultValueAvailable = $parameter->isDefaultValueAvailable();

                    if (!$defaultValueAvailable)
                    {
                        throw new \Exception('cannot resolve parameter: "' . $parameterName . '". either set a default value or add a named type for this parameter in the method OR add a named parameter to the Application::get method invocation');
                    }

                    $resolvedParameters[] = $parameter->getDefaultValue();
                }
            }
            else
            {
                $parameterClassName = $parameterClass->getName();
                $resolvedParameters[] = $this->classResolver->resolve($parameterClassName, $namedParameters);
            }
        }

        return $resolvedParameters;
    }

}