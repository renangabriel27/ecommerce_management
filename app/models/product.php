<?php class Product extends Base {

  private $name;
  private $amount;
  private $description;
  private $price;
  private $categoryId;
  private $category;


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

  public function setCategory($category) {
    $this->category = $category;
  }

  public function getCategory() {
    return $this->category;
  }

  public function validates() {
    if ($this->newRecord() || $this->changedFieldValue('name', 'products')) {
      Validations::uniqueField($this->name, 'name',  'products', $this->errors);
      Validations::notEmpty($this->name, 'name', $this->errors);
    }

    Validations::notEmpty($this->amount, 'amount', $this->errors);
    Validations::isNumeric($this->amount, 'amount', $this->errors);
    Validations::notEmpty($this->price, 'price', $this->errors);
    Validations::isNumeric($this->price, 'price', $this->errors);
    Validations::notEmpty($this->categoryId, 'category_id', $this->errors);
    Validations::notEmpty($this->description, 'description', $this->errors);
  }

  public function save() {
    if(!$this->isValid()) return false;
    $this->createdAt = date('Y-m-d H:i:s', time());

    $sql = "INSERT INTO
              products (name, amount, description, price, category_id, created_at)
            VALUES
              (:name, :amount, :description, :price, :category_id, :created_at)";

    $params = array('name' => $this->name, 'amount' => $this->amount,
                    'description' => $this->description, 'price' => $this->price,
                    'category_id' => $this->categoryId, 'created_at' => $this->createdAt);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());
    if(!$resp)  return false;

    return true;
  }

  public function selectPrice($id) {
    $sql = "SELECT price FROM sell_orders_items, orders
      WHERE(sell_orders_items.order_id = orders.id) AND product_id = ? AND status = ?";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $statement->execute(array($id, 'Fechado'));

    return $statement->fetch()[0];
  }

  public function update($data = array()) {
    // if($this->hasNotChange($data)) return true;

    $this->setData($data);
    if (!$this->isValid()) return false;
    $params = array($this->name, $this->amount, $this->description, $this->price, $this->categoryId, $this->id);
    $sql = "UPDATE
              products
           SET
              name = ?, amount = ?, description = ?, price = ?, category_id = ?
           WHERE
              id = ?";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if(!$resp) false;

    return true;
  }

  public function updateProductOnStock() {
    $sql = "UPDATE products set amount = ? WHERE id = ?";
    $params = array($this->amount, $this->id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if(!$resp) return false;

    return true;
  }

  public function removeProductOfStock() {
    if($this->amount != 0) {
      $this->amount--;
      if($this->updateProductOnStock()) return true;
    }
    return false;
  }

  public function addProductOnStock() {
    $this->amount++;
    if($this->updateProductOnStock()) return true;
    return false;
  }

  public function restoreProductOnStock($amount) {
    $this->amount += $amount;
    if($this->updateProductOnStock()) return true;

    return false;
  }

  public static function productSearch($options = []) {
    $sql = "SELECT
              p.id AS id, p.name AS product_name, p.amount AS product_amount,
              p.price AS product_price, p.description AS product_description,
              p.created_at AS product_created_at, c.id AS category_id,
              c.name AS category_name, c.created_at AS category_created_at
            FROM
              products p JOIN categories c ON(p.category_id = c.id)
            WHERE
              p.name LIKE :param ORDER BY p.name";

    $param = $options['param'];
    $params ="%{$param}%";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);

    if(sizeof($options) != 0) {
      $sql .= " LIMIT :limit OFFSET :offset ";;
      $statement = $db->prepare($sql);
      $statement->bindParam(':limit', $options['limit'], PDO::PARAM_INT);
      $statement->bindParam(':offset', $options['offset'], PDO::PARAM_INT);
    }
    $statement->bindParam(':param', $params, PDO::PARAM_STR, 12);

    $resp = $statement->execute();

    $products = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $products[] =  self::createProduct($row);
    }
    return $products;
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT
              p.id AS id, p.name AS product_name, p.amount AS product_amount,
              p.price AS product_price, p.description AS product_description,
              p.created_at AS product_created_at, c.id AS category_id,
              c.name AS category_name, c.created_at AS category_created_at
            FROM
              products p, categories c
            WHERE
              (p.category_id = c.id) AND (p.id = ?)";

    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return self::createProduct($row);
    }
    return null;
  }

  public static function all($options = []) {
      $sql = "SELECT
                p.id AS id, p.name AS product_name, p.amount AS product_amount,
                p.price AS product_price, p.description AS product_description,
                p.created_at AS product_created_at, c.id AS category_id,
                c.name AS category_name, c.created_at AS category_created_at
              FROM
                products p, categories c
              WHERE
                (p.category_id = c.id) ORDER BY product_created_at DESC";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);

    if(sizeof($options) != 0) {
      $sql .= " LIMIT :limit OFFSET :offset ";;
      $statement = $db->prepare($sql);
      $statement->bindParam(':limit', $options['limit'], PDO::PARAM_INT);
      $statement->bindParam(':offset', $options['offset'], PDO::PARAM_INT);
    }
    $resp = $statement->execute();

    $products = [];

    if(!$resp) return $products;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $products [] =  self::createProduct($row);
    }
    return $products;
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

  private static function createProduct($row) {
    $product = new Product();
    $product->setId($row['id']);
    $product->setName($row['product_name']);
    $product->setAmount($row['product_amount']);
    $product->setPrice($row['product_price']);
    $product->setDescription($row['product_description']);
    $product->setCategoryId($row['category_id']);
    $product->setCreatedAt($row['product_created_at']);

    $category = new Category();
    $category->setId($row['category_id']);
    $category->setName($row['category_name']);
    $category->setCreatedAt($row['category_created_at']);

    $product->setCategory($category);

    return $product;
  }

} ?>
