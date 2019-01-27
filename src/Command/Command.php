<?php

namespace Intersect\Core\Command;

interface Command {

    public function execute($data = []);
    public function getDescription();
    public function getParameters();

}