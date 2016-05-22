<?php class Product extends Base {

  private $name;
  private $description;
  private $price;
  private $categoryId;


  public function setName($name) {
      $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function setDescription($description) {
      $this->description = $description;
  }

  public function getDescription() {
    return $this->description;
  }

  public function setCategoryId($categoryId) {
      $this->categoryId = $categoryId;
  }

  public function getCategoryId() {
    return $this->categoryId;
  }

  public function getCategory() {
    $db = Database::getConnection();
    $sql = "SELECT * FROM categories WHERE id = ?";
    $params = array($this->categoryId);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return new Category($row);
    }
    return null;
  }

  public function setPrice($price) {
    if (preg_match('/R\$/', $price)) {
      $price = str_replace('.', '', $price);
      $price = str_replace(',', '.', $price);
      $price = str_replace('R$', '', $price);
    }
    $this->price = $price;
  }

  public function getPrice() {
    return $this->price;
  }

  public function validates() {
    Validations::notEmpty($this->name, 'name', $this->errors);
    Validations::notEmpty($this->description, 'description', $this->errors);
    Validations::isNumeric($this->price, 'price', $this->errors);
    Validations::notEmpty($this->categoryId, 'category_id', $this->errors);
  }

  public function save() {
    if(!$this->isValid()) return false;

    $sql = "INSERT INTO products (name, description, price, category_id)
    VALUES (?,?,?,?);";

    $params = array($this->name, $this->description, $this->price, $this->categoryId);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);


    if(!$resp) {
      Logger::getInstance()->log("Falha ao salvar o produto: " . print_r($this, TRUE),
        Logger::ERROR);
      Logger::getInstance()->log("Error " . print_r(error_get_last(), true ),
        Logger::ERROR);
      return false;
    }
    return true;
  }

  public function update() {
    $this->setData($data);
    if (!$this->isValid()) return false;

    $params = array($this->name, $this->description, $this->price, $this->categoryId);
    $sql = "UPDATE products set name = ?, description = ?, price = ?, category_id = ?
    WHERE id = ?";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if(!$resp) {
      Logger::getInstance()->log("Falha ao atualizar o produto: " . print_r($this, TRUE),
        Logger::ERROR);
      Logger::getInstance()->log("Error " . print_r(error_get_last(), true ),
        Logger::ERROR);
      return false;
    }
    return true;
  }

  public function delete() {
    $db = Database::getConnection();
    $params = array($this->id);
    $sql = "DELETE FROM products WHERE id = ?";
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }


  public static function whereNameLikeAsJson($param) {
    $sql = "SELECT id, name FROM products WHERE name LIKE :param ORDER BY name";
    $params = array('param' => "%{$param}%");

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $suggestions = array('suggestions' => '');

    if(!$resp) return $suggestions;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $product = array('value' => $row['name'], 'data' => $row['id']);
      $suggestions['suggestions'][] = $product;
    }

    return json_encode($suggestions);
  }

  public static function all($options) {
    $limit = $options['limit'];
    $offset = ($options['page'] - 1) * $limit;

    $sql = "SELECT * FROM products ORDER BY created_at DESC
    LIMIT :limit OFFSET :offset";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
    $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
    $resp = $statement->execute();

    $products = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $products[] = new Procuct($row);
    }
    return $products;
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT * FROM products WHERE id = ?";
    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return new Product($row);
    }
    return null;
  }

  public static function count() {
    $sql = "SELECT COUNT(*) FROM products";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $statement->execute();

    return $statement->fetch()[0];
  }

} ?>
