<?php 

namespace Ninja;

use \PDO, \PDOException, \DateTime;

class DatabaseTable {

    public function __construct(private PDO $database, private string $table, private string $primaryKey, 
        private string $className = '\stdClass', private array $constructorArgs = []){

    }
        
    public function total($field = null, $value = null){

        $query = 'SELECT COUNT(*) FROM `' . $this->table . '`';

        $values = [];

        if (!empty($field) && !empty($value)) {

            $query .=  ' WHERE `' . $field . '` = :value';
            $values = ['value' => $value];

        }

        $stmt = $this->database->prepare($query);
        $stmt->execute($values);
        
        $row = $stmt->fetch();

        return $row[0];

    }

    public function find($field, $value, $orderBy = null, $order = null, $limit = 0, $offset = 0){

        $values = [
            ':value' => $value
        ];

        $query = 'SELECT * FROM `' . $this->table . '` WHERE `' . $field . '` = :value';

        if ($orderBy != null &&  $order != null) {


            $query .= ' ORDER BY `' .  $orderBy . '` ' . $order;

        }

        if ($limit > 0) {

            $query .=  ' LIMIT ' . $limit;

        }

        if ($offset > 0) {

            $query .= ' OFFSET ' . $offset;

        }

        $stmt = $this->database->prepare($query);
        $stmt->execute($values);

        return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->className, $this->constructorArgs);

    }

    public function findAll($orderBy = null, $order = null, $limit = 0, $offset = 0){

        $query = 'SELECT * FROM `' . $this->table . '`';

        if ($orderBy != null &&  $order != null) {

            $query .= ' ORDER BY `' . $orderBy . '` ' . $order;

        }

        if ($limit > 0) {

            $query .= ' LIMIT ' . $limit;

        }

        if ($offset > 0) {

            $query .=  ' OFFSET ' . $offset;

        }

        $stmt = $this->database->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->className, $this->constructorArgs);

    }

    public function deleteRecord($field, $value){

        $values = [
            ':value' => $value
        ];

        $query = 'DELETE FROM `' . $this->table . '` WHERE `' . $field . '` = :value';
        $stmt = $this->database->prepare($query);
        $stmt->execute($values);

    }

    private function insert($values){

        $query = 'INSERT INTO `' . $this->table . '` (';

        foreach ($values as $key => $value) {
            $query .= '`' . $key . '`,';
        }

        $query = rtrim($query, ',');

        $query .= ') VALUES (';

        foreach ($values as $key => $value) {

            $query .= ':' . $key . ',';
            
        }

        $query = rtrim($query, ',');

        $query .= ')';

        $values = $this->processDates($values);

        $stmt = $this->database->prepare($query);
        $stmt->execute($values);

        return $this->database->lastInsertId();

    }

    private function update($values){

        $query = 'UPDATE `' . $this->table . '` SET ';

        foreach ($values as $key => $value){

            $query .= '`' . $key . '` = :' . $key . ','; 

        }

        $query = rtrim($query, ',');

        $query .= ' WHERE `' . $this->primaryKey . '` = :primaryKey';

        $values['primaryKey'] = $values['id'];

        $values = $this->processDates($values);

        $stmt = $this->database->prepare($query);
        $stmt->execute($values);

    }

    public function save($record){

        $entity = new $this->className(...$this->constructorArgs);

        try {

            if (empty($record[$this->primaryKey])){

                unset($record[$this->primaryKey]);

            }

            $insertId = $this->insert($record);
            
            $entity->{$this->primaryKey} = $insertId;

        } catch (PDOException $e) {

            $this->update($record);

        }

        foreach ($record as $key => $value) {

            if (!empty($value)) {

                if ($value instanceof DateTime) {

                    $value = $value->format('Y-m-d H:i:s');

                }

                $entity->$key = $value;

            }

        }

        return $entity;

    }

    private function processDates($values){

        foreach ($values as $key => $value) {

            if($value instanceof DateTime) {

                $values[$key] = $value->format('Y-m-d H:i:s');

            }

        }

        return $values;

    }


}