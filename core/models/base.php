<?php
abstract class Base {
    protected $id;
    protected $createdAt;
    protected $errors = array();

    public function __construct($data = array()) {
      $this->setData($data);
    }

    public function validates() {}

    public function getId() {
      return $this->id;
    }

    public function setId($id) {
      $this->id = $id;
    }

    public function getCreatedAt(){
      return $this->createdAt;
    }

    public function setCreatedAt($createdAt) {
      $this->createdAt = $createdAt;
    }

    public function getErrors($index = null) {
      if ($index == null)
        return $this->errors;

      if (isset($this->errors[$index]))
        return $this->errors[$index];

      return false;
    }

    public function isValid() {
      $this->errors = array();
      $this->validates();
      return empty($this->errors);
    }

    public function newRecord() {
      return empty($this->id);
    }

    public function changedFieldValue($field, $table) {
      $db = Database::getConnection();
      $sql = "select {$field} from {$table} where id = :id";

      $statement = $db->prepare($sql);
      $params = array('id' => $this->id);
      $statement->execute($params);
      $result = $statement->fetch();

      $method = 'get' . $field;
      $field_from_db = $result[$field];

      Logger::getInstance()->log("Mudou: {$this->$method()}", Logger::NOTICE);

      return $field_from_db !== $this->$method();
    }

    public function isTableRelations($table, $field) {
      $sql = "SELECT * FROM {$table} WHERE {$field} = ?";
      $params = array($this->id);

      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $statement->execute($params);
      $resp = $statement->fetch(PDO::FETCH_ASSOC);

      if($resp) return true;

      return false;
    }

    public function setData($data = array()) {
      foreach($data as $key => $value){
         $method = "set{$key}";
         $method = ActiveSupport::snakToCamelCase($method);
         $this->$method(strip_tags(trim($value)));
      }
    }

    public function hasNotChange($data = array()) {
      foreach($data as $key => $value) {
         $method = "get_{$key}";
         $method = ActiveSupport::snakeToCamelCase($method);

         if($this->$method != $value) return false;
      }
      return true;
    }

    public function delete($table) {
      $db = Database::getConnection();

      $params = array('id' => $this->id);
      $sql = "DELETE FROM $table WHERE id = :id";

      $statement = $db->prepare($sql);
      return $statement->execute($params);
    }


} ?>
