<?php

namespace Intersect\Core;

interface Event {

    public function handle($data = []);

}