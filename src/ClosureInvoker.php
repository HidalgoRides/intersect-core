<?php

namespace Intersect\Core;

class ClosureInvoker {

    /** @var ParameterResolver */
    private $parameterResolver;

    public function __construct(ParameterResolver $parameterResolver)
    {
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * @param $closure
     * @param array $namedParameters
     * @return mixed
     * @throws \Exception
     */
    public function invoke($closure, $namedParameters = array())
    {
        $closure = new \ReflectionFunction($closure);

        $parameters = $this->parameterResolver->resolve($closure->getParameters(), $namedParameters);

        return $closure->invokeArgs($parameters);
    }

}