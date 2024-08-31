<?php

namespace Tapi;

use Ninja\Website;
use Ninja\DatabaseTable;

use Tapi\Controllers\Tasks;

use \PDO;

class TaskApiWebsite implements Website {

    private DatabaseTable $tasksTable;

    public function __construct() {

        $pdo = new PDO('mysql:host=localhost;dbname=XXXX;charset=utf8mb4;port=XXXX', 'username', 'password', [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // REMOVE IN PRODUCTION
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_STRINGIFY_FETCHES  => false
        ]);
        
        $this->tasksTable = new DatabaseTable($pdo, 'tasks', 'id');

    }

    public function getDefaultRoute(): string {

        return 'tasks';

    }

    public function getLayoutVariables(): array {

        return [];

    }

    public function getController(string $controllerName): ?object {

        $controllers = [

            'tasks' => new Tasks($this->tasksTable)

        ];

        return $controllers[$controllerName] ?? null;

    }

    public function checkLogin(string $uri): ?string {

        return $uri;

    }

}