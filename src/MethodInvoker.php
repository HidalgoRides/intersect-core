<?php

namespace Intersect\Core;

class MethodInvoker {

    /** @var ParameterResolver */
    private $parameterResolver;

    public function __construct(ParameterResolver $parameterResolver)
    {
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * @param $class
     * @param $methodName
     * @param array $namedParameters
     * @return mixed
     * @throws \Exception
     */
    public function invoke($class, $methodName, $namedParameters = array())
    {
        $method = new \ReflectionMethod($class, $methodName);
        $parameters = $this->parameterResolver->resolve($method->getParameters(), $namedParameters);

        return call_user_func_array(array($class, $methodName), $parameters);
    }

}