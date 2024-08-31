<?php 

namespace Ninja;

use Ninja\Website;
use Ninja\ApiErrorHandler;

use PDOException;
use ReflectionClass;
use ReflectionMethod;

class ApiEntryPoint {

    public function __construct (private Website $website) {}

    public function run (string $uri, string $method) {

        try {

            header('Content-type: application/json; charset=UTF-8');

            $route = explode('/', $uri);

            array_shift($route); // to get rid of /api part of the uri

            $controllerName = array_shift($route);

            $controller = $this->website->getController($controllerName);

            if (is_callable([$controller, $method])){

                $response = $controller->$method(...$route);

                if (!empty($response)) {

                    echo json_encode($response);

                } else {

                    http_response_code(404);

                }

            } else {

                if (isset($controller)) {

                    http_response_code(405);

                    $reflector = new ReflectionClass($controller);

                    $reflectorMethods = $reflector->getMethods(ReflectionMethod::IS_PUBLIC);

                    $validMethods = array_map(function  ($method) {

                        return $method->name;

                    },  $reflectorMethods);

                    if (in_array('__construct', $validMethods)) { 

                        unset($validMethods[array_search('__construct', $validMethods)]);

                    } 

                    $valid = 'Allow: ';

                    foreach  ($validMethods as $vMethod) {

                        $valid .= $vMethod . ', ';

                    }

                    header(rtrim($valid, ', '));

                    echo json_encode([

                        'errors' => ['not a valid request method']

                    ]);
                    exit;

                }

                http_response_code(404);
                exit;

            }

            

        } catch (PDOException $error) {

            ApiErrorHandler::handleException($error);

        }

        

    }

}