<?php class Order extends Base {

  private $employeeId; /* user id */
  private $client; /* user */
  private $amount;
  private $total;


  public function setEmployeeId($employeeId) {
      $this->employeeId = $employeeId;
  }

  public function getEmployeeId() {
    return $this->employeeId;
  }

  public function setClientId($client) {
      $this->client = $client;
  }

  public function getClientId() {
    return $this->client;
  }

  public function setAmount($amount) {
      $this->amount = $amount;
  }

  public function getAmount() {
    return $this->amount;
  }

  public function setTotal($total) {
      $this->total = $total;
  }

  public function getTotal() {
    return $this->total;
  }

  public function validates() {
    Validations::notEmpty($this->client, 'client_id', $this->errors);
  }

  public function save() {
    if(!$this->isValid()) return false;

    $sql = "INSERT INTO orders (client_id)
    VALUES (:client_id);";

    $params = array('client_id' => $this->client);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);


    if(!$resp) {
      Logger::getInstance()->log("Falha para salvar pedidos: " . print_r($this, TRUE), Logger::ERROR);
      Logger::getInstance()->log("Error " . print_r(error_get_last(), true ), Logger::ERROR);
      return false;
    }

    $this->setId($db->lastInsertId());
    return true;
  }

  public function getProducts() {
    $sql = "SELECT products.id, products.name, products.price, products.created_at
    FROM sell_orders_items, products WHERE ((sell_orders_items.order_id = ?) AND (products.id = sell_orders_items.product_id))";
    $params = array($this->id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $products = [];

    if(!$resp) return false;

    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $products[] = new Product($row);
    }
    return $products;
  }

  public function addProduct($id) {
    $sql = "INSERT INTO sell_orders_items (price, order_id, product_id)
    VALUES ((SELECT price FROM products WHERE products.id = :product_id) , :order_id, :product_id)";
    $params = array('product_id' => $id, 'order_id' => $this->id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if(!$resp) {
      Logger::getInstance()->log("Falha para salvar: " . print_r($this, TRUE), Logger::ERROR);
      Logger::getInstance()->log("Error " . print_r(error_get_last(), true ), Logger::ERROR);
      return false;
    }
    return true;
  }

  public function delete() {
    $db = Database::getConnection();
    $params = array($this->id);
    $sql = "DELETE FROM orders WHERE id = ?";
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public static function all() {
    $sql = "SELECT orders.id AS id, clients.id AS client_id, clients.name AS client_name,
    clients.email AS client_email FROM clients, orders WHERE (orders.client_id = clients.id)
    ORDER BY id";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute();

    $orders = [];

    if(!$resp) return $orders;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $order = new Order();
      $order->setId($row['id']);

      $client = new Client();
      $client->setId($row['client_id']);
      $client->setName($row['client_name']);
      $client->setEmail($row['client_email']);

      $order->setClientId($client);

      $orders[] = $order;
    }
    return $orders;
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT orders.id AS id, clients.id AS client_id, clients.name AS client_name,
    clients.email AS client_email FROM clients, orders WHERE (orders.client_id = clients.id)
    AND (orders.id = ? )";
    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $order = new Order();
      $order->setId($row['id']);

      $client = new Client();
      $client->setId($row['client_id']);
      $client->setName($row['client_name']);
      $client->setEmail($row['client_email']);

      $order->setClientId($client);
      return $order;
    }

    return null;
  }




} ?>
