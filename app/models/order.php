<?php class Order extends Base {

  private $employeeId;
  private $clientId;
  private $client;
  private $amount;
  private $total;
  private $status;

  public function setEmployeeId($employeeId) {
      $this->employeeId = $employeeId;
  }

  public function getEmployeeId() {
    return $this->employeeId;
  }

  public function setClientId($clientId) {
      $this->clientId = $clientId;
  }

  public function getClientId() {
    return $this->clientId;
  }

  public function setClient($client) {
    $this->client = $client;
  }

  public function getClient() {
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

  public function setStatus($status) {
      $this->status = $status;
  }

  public function getStatus() {
    return $this->status;
  }

  public function validates() {
    Validations::notEmpty($this->clientId, 'client_id', $this->errors);
    Validations::uniqueField($this->clientId, 'client_id', 'orders', $this->errors);
  }

  public function save() {
    if(!$this->isValid()) return false;

    $sql = "INSERT INTO
              orders (client_id, employee_id)
            VALUES
              (:client_id, :employee_id)";

    $this->employeeId = SessionHelpers::currentEmployee()->getId();
    $params = array('client_id' => $this->clientId, 'employee_id' => $this->employeeId);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if(!$resp) {
      Logger::getInstance()->log("Falha para salvar pedidos: " . print_r($this, TRUE), Logger::ERROR);
      Logger::getInstance()->log("Error " . print_r(error_get_last(), true ), Logger::ERROR);
      return false;
    }

    $this->setId($db->lastInsertId());
    $this->setCreatedAt(date());
    return true;
  }

  public function getProducts() {
    $sql = "SELECT
              products.id, products.name, products.price, products.created_at
            FROM
              sell_orders_items, products
            WHERE
              ((sell_orders_items.order_id = ?) AND (products.id = sell_orders_items.product_id))";

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
    $sql = "INSERT INTO
              sell_orders_items (price, order_id, product_id)
            VALUES
              ((SELECT price FROM products WHERE products.id = :product_id) , :order_id, :product_id)";
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

  public function uniqueItem($id) {
    $sql = "SELECT order_id, product_id FROM sell_orders_items WHERE order_id = ? AND product_id = ?";
    $params = array($this->id, $id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $statement->execute($params);

    if (!$row = $statement->fetch()) {
      return false;
    }
    return true;
  }

  public function getSellOrderItem($id) {
    $sql = "SELECT * FROM sell_orders_items where product_id = ?";
    $db = Database::getConnection();
    $params = array($id);
    $statement = $db->prepare($sql);
    $statement->execute($params);

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $sell_order_item = new SellOrderItem();
      $sell_order_item->setId($row['id']);
      $sell_order_item->setAmount($row['amount']);
      $sell_order_item->setPrice($row['price']);
      $sell_order_item->setCreatedAt($row['created_at']);
      $sell_order_item->setOrderId($row['order_id']);
      $sell_order_item->setProductId($row['product_id']);

    }
    return $sell_order_item;
  }

  public function changeStatusOrder($id) {
    $db = Database::getConnection();
    $this->status = "Fechado";
    $params = array('id' => $id, 'status' => $this->status);
    $sql = "UPDATE orders SET status= :status WHERE id = :id";
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);
    if($resp) return true;

    return false;
  }

  public static function all() {
    $sql = "SELECT
              orders.id AS id, orders.created_at AS created_at, orders.status AS status,
              clients.id AS client_id, clients.name AS client_name, clients.email AS client_email
            FROM
              clients
            JOIN
              orders
            ON
              (orders.client_id = clients.id)
            ORDER BY
              id";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute();

    $orders = [];

    if(!$resp) return $orders;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $orders[] = self::createOrder($row);
    }
    return $orders;
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT
              o.id AS id, o.created_at AS created_at, o.status AS status,
              c.id AS client_id, c.name AS client_name, c.email AS client_email
            FROM
              clients c, orders o
            WHERE
              (o.client_id = c.id)
            AND
              (o.id = ? )";

    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return self::createOrder($row);
    }

    return null;
  }

  private static function createOrder($row) {
    $order = new Order();
    $order->setId($row['id']);
    $order->setStatus($row['status']);
    $order->setCreatedAt($row['created_at']);

    $client = new Client();
    $client->setId($row['client_id']);
    $client->setName($row['client_name']);
    $client->setEmail($row['client_email']);

    $order->setClient($client);

    return $order;
  }

} ?>
