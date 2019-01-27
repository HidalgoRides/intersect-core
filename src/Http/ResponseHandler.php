<?php

namespace Intersect\Core\Http;

use Intersect\Core\Http\Response;

interface ResponseHandler {

    public function canHandle(Response $response);

    public function handle(Response $response);

}