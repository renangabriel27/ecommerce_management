<?php class Product extends Base {

  private $name;
  private $amount;
  private $description;
  private $price;
  private $categoryId;


  public function setName($name) {
      $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function setAmount($amount) {
    $this->amount = $amount;
  }

  public function getAmount() {
    return $this->amount;
  }

  public function setDescription($description) {
      $this->description = $description;
  }

  public function getDescription() {
    return $this->description;
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

  public function setCategoryId($categoryId) {
      $this->categoryId = $categoryId;
  }

  public function getCategoryId() {
    return $this->categoryId;
  }

  public function getCategory() {
    $db = Database::getConnection();
    $sql = "SELECT name FROM categories WHERE id = ?";
    $params = array($this->categoryId);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return new Category($row);
    }
    return null;
  }

  public function validates() {
    if ($this->newRecord() || $this->changedFieldValue('name', 'products')) {
      Validations::uniqueField($this->name, 'name',  'products', $this->errors);
      Validations::notEmpty($this->name, 'name', $this->errors);
    }
    Validations::notEmpty($this->amount, 'amount', $this->errors);
    Validations::isNumeric($this->price, 'price', $this->errors);
    Validations::notEmpty($this->categoryId, 'category_id', $this->errors);
    Validations::notEmpty($this->description, 'description', $this->errors);
  }

  public function save() {
    if(!$this->isValid()) return false;

    $sql = "INSERT INTO products (name, amount, description, price, category_id)
    VALUES (:name, :amount, :description, :price, :category_id);";

    $params = array('name' => $this->name, 'amount' => $this->amount, 'description' => $this->description,
                    'price' => $this->price, 'category_id' => $this->categoryId);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if(!$resp) {
      Logger::getInstance()->log("Falha ao salvar o produto: " . print_r($this, TRUE), Logger::ERROR);
      Logger::getInstance()->log("Error " . print_r(error_get_last(), true ), Logger::ERROR);
      return false;
    }
    return true;
  }

  public function update($data = array()) {
    $this->setData($data);
    if (!$this->isValid()) return false;

    $params = array($this->name, $this->amount, $this->description, $this->price, $this->categoryId, $this->id);
    $sql = "UPDATE products set name = ?, amount = ?, description = ?, price = ?, category_id = ?
    WHERE id = ?";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if(!$resp) {
      Logger::getInstance()->log("Falha ao atualizar o produto: " . print_r($this, TRUE), Logger::ERROR);
      Logger::getInstance()->log("Error " . print_r(error_get_last(), true ), Logger::ERROR);
      return false;
    }
    return true;
  }

  public static function all() {
    $sql = "SELECT products.id AS id, products.name AS product_name, products.amount AS product_amount,
            products.price AS product_price, products.created_at AS product_created_at, categories.id
            AS category_id, categories.name AS category_name FROM products, categories
            WHERE(products.category_id = categories.id) ORDER BY product_created_at DESC";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute();

    $products = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $product = new Product();
      $product->setId($row['id']);
      $product->setName($row['product_name']);
      $product->setAmount($row['product_amount']);
      $product->setPrice($row['product_price']);
      $product->setCreatedAt($row['product_created_at']);

      $category = new Category();
      $category->setId($row['category_id']);
      $category->setName($row['category_name']);

      $product->setCategoryId($category);

      $products[] = $product;
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

  public static function count() {
    $sql = "SELECT COUNT(*) FROM products";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $statement->execute();

    return $statement->fetch()[0];
  }

} ?>
