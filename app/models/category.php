<?php class Category extends Base {

  private $name;

  public function setName($name) {
      $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function validates() {
    Validations::greaterThen($this->name, 5, 'name', $this->errors);
    Validations::notEmpty($this->name, 'name', $this->errors);
    Validations::uniqueField($this->name, 'name' , 'categories', $this->errors);
  }

  public function save() {
    if(!$this->isValid()) return false;

    $sql = "INSERT INTO categories (name) VALUES (:name)";

    $params = array('name' => $this->name);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);


    if(!$resp) {
      Logger::getInstance()->log("Falha para salvar categorias: " . print_r($this, TRUE), Logger::ERROR);
      Logger::getInstance()->log("Error " . print_r(error_get_last(), true ), Logger::ERROR);
      return false;
    }

    $this->setId($db->lastInsertId());
    $this->setCreatedAt(date());
    return true;
  }

  public function update($data = array()) {
    $this->setData($data);
    if (!$this->isvalid()) return false;

    $db = Database::getConnection();
    $params = array('name' => $this->name,
      'id' => $this->id);

    $sql = "UPDATE categories SET name=:name WHERE id = :id";

    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public function delete() {
    $db = Database::getConnection();
    $params = array($this->id);
    $sql = "DELETE FROM categories WHERE id = ?";
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public static function all() {
    $sql = "SELECT * FROM categories ORDER BY created_at DESC";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute();

    $categories = [];

    if(!$resp) return $categories;

    while($category = $statement->fetch(PDO::FETCH_ASSOC)) {
      $categories[] = new Category($category);
    }

    return $categories;
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT * FROM categories WHERE id = ?";
    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $category = new Category($row);
      return $category;
    }

    return null;
  }

  public static function count() {
    $sql = "SELECT COUNT(*) FROM categories";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute();

    return $statement->fetch()[0];
  }

} ?>
