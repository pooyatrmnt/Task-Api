<?php 

namespace Tapi\Controllers;

use Ninja\DatabaseTable;

class Tasks {

    public function __construct (private DatabaseTable $tasksTable) {}

    public function GET ($id = null) {

        if (isset($id)){
            
            return $this->fixBool( $this->tasksTable->find('id', $id) );

        }

        return $this->fixBool( $this->tasksTable->findAll() );

    }

    public function POST () {

        $data = json_decode(file_get_contents('php://input'), true);

        $task = [];

        if (!isset($data)) {

            return [

                'errors' => ['invalid request, please send the required parameters in the body']

            ];

        }

        $errors = $this->validateInput($data);

        if (! empty($errors)) {

            http_response_code(422);

            return [

                'errors' => $errors

            ];

        } else {

            $task['name'] = $data['name'];
            $task['priority'] = $data['priority'];

            if (empty($data['is_completed'])) {

                $task['is_completed'] = 0;
    
            } else {
    
                if ($data['is_completed'] === true) {
    
                    $task['is_completed'] = 1;
    
                } elseif ($data['is_completed'] === false) {
    
                    $task['is_completed'] = 0;
    
                }

            }

            http_response_code(201);
            return $this->tasksTable->save($task);

        }

    }

    public function PATCH ($id = null) {

        if (isset($id)){
            
            $task = $this->getTaskById($id);

            if (isset($task)) {
    
                $data = json_decode(file_get_contents('php://input'), true);

                $errors = $this->validateInput($data, false);

                if (! empty($errors)) {

                    http_response_code(422);

                    return [

                        'errors' => $errors

                    ];

                } else {

                    $newTask = [

                        'id'           => $id,
                        'name'         => $task->name,
                        'priority'     => $task->priority,
                        'is_completed' => $task->is_completed
        
                    ];

                    $commonKeys = array_intersect_key($newTask, $data);

                    foreach ($commonKeys as $key => $value) {

                        if (! empty($data[$key])) {

                            $newTask[$key] = $data[$key];

                        }

                    }

                    if ($newTask['is_completed'] === true) {
    
                        $newTask['is_completed'] = 1;
        
                    } elseif ($newTask['is_completed'] === false) {
        
                        $newTask['is_completed'] = 0;
        
                    }

                    $this->tasksTable->save($newTask);

                    return [

                        'message' => 'Task updated successfully.'

                    ];

                }

            } else {

                http_response_code(404);

                return [

                    'errors' => ['Task with this id does not exist']

                ];

            }

        }

        return [

            'errors' => ['when updating a record, you must provide an id']

        ];

    }

    public function DELETE ($id = null) {

        if (isset($id)){
            
            $task = $this->getTaskById($id);

            if (isset($task)) {

                $this->tasksTable->deleteRecord('id', $id);

                http_response_code(204);

                return [
                    
                    'message' =>  'Task deleted successfully.'
                
                ];

            } else {

                http_response_code(404);

                return [

                    'errors' => ['Task with this id does not exist']

                ];

            }

        }

        return [

            'errors' => ['when deleting a record, you must provide an id']

        ];

    }

    private function getTaskById ($id) : ?object {

        if (isset($id)) {

            $task  = $this->fixBool($this->tasksTable->find('id', $id));

        }

        return $task[0] ?? null;

    }

    private function fixBool ($data) {

        foreach  ($data as $key => $value) {

            if ($value->is_completed == 0) {

                $data[$key]->is_completed = false;

            } else {

                $data[$key]->is_completed = true;

            }

        }

        return $data;

    }

    private function validateInput (array $data, $is_new = true) {

        $errors = [];

        if (empty($data['name']) && $is_new) {


            $errors[] = 'name is required';

        }

        if (! empty($data['priority']) && ! filter_var($data['priority'], FILTER_VALIDATE_INT)){

            $errors[] = 'priority must be an integer';

        }

        if (! empty($data['is_completed']) && ! filter_var($data['is_completed'],  FILTER_VALIDATE_BOOLEAN)) {

            $errors[] = 'is_completed must be a boolean';

        }

        return $errors;

    }

}