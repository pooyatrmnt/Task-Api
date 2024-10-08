<?php

namespace Ninja;

interface Website {

    public function getDefaultRoute() : string;

    public function getLayoutVariables() : array;

    public function getController(string $controllerName) : ?object;

    public function checkLogin(string $uri) : ?string;
}