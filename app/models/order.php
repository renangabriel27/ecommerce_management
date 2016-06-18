<?php class Order extends Base {

  private $employeeId;
  private $clientId;
  private $employee;
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

  public function setEmployee($employee) {
    $this->employee = $employee;
  }

  public function getEmployee() {
    return $this->employee;
  }

  public function setAmount($amount) {
    $this->amount = $amount;
  }

  public function getAmount() {
    return $this->amount;
  }

  public function setTotal($total = '') {
    if (preg_match('/R\$/', $total)) {
      $total = str_replace('.', '', $total);
      $total = str_replace(',', '.', $total);
      $total = str_replace('R$', '', $total);
    }
    $this->total = $this->getItemsTotal();
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

  public function getItemsTotal() {
    $sql = "SELECT amount, price FROM sell_orders_items WHERE order_id = ?";
    $params = array($this->id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if(!$resp) return false;

    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $itemsTotal = $row['amount'] * $row['price'];
      $this->total += $itemsTotal;
    }
    return $this->total;
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

  public function updateTotal($orderId) {
    $sql = "UPDATE orders set total = ? WHERE id = ?";
    $params = array($this->total, $orderId);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if(!$resp) return false;

    return true;
  }

  public function addProduct($productId) {
    $sql = "INSERT INTO
              sell_orders_items (price, order_id, product_id)
            VALUES
              ((SELECT price FROM products WHERE products.id = :product_id), :order_id, :product_id)";
    $params = array('product_id' => $productId, 'order_id' => $this->id);

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

  public function productIsValid($params) {
    if($params == NULL) return true;

    return false;
  }

  public function sum() {
    $sql = "SELECT SUM(amount) FROM sell_orders_items WHERE order_id = ?";

    $params = array($this->id);
    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $statement->execute($params);

    return $statement->fetch()[0];
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

  public function changeStatusOrder() {
    $this->status = 'Fechado';
    $params = array('id' => $this->id, 'status' => $this->status);
    $sql = "UPDATE orders SET status= :status WHERE id = :id";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if($resp) return true;

    return false;
  }

  public function getEmployeeOrder($employeeId) {
    $params = array('employee_id' => $employeeId, 'order_id' => $this->id);
    $sql = "SELECT * FROM orders WHERE employee_id = :employee_id AND orders.id = :order_id";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $statement->execute($params);
    $resp = $statement->fetch(PDO::FETCH_ASSOC);
    if($resp) return true;

    return false;
  }

  public static function all() {
    $employeeId = SessionHelpers::currentEmployee()->getId();
    $sql = "SELECT
              o.id AS id, o.created_at AS created_at,  o.total AS total, o.status AS status,
              o.employee_id AS order_employee_id, o.client_id AS order_client_id,
              c.id AS client_id, c.name AS client_name, c.email AS client_email, c.address AS client_address,
              c.address_number AS client_address_number, c.address_cep AS client_cep, c.phone AS client_phone,
              c.created_at AS client_created_at, c.city_id AS client_city_id, c.type AS client_type,
              ct.id AS city_id, ct.name AS city_name, ct.created_at AS city_created_at, ct.state_id AS state_id,
              e.id AS employee_id, e.name AS employee_name, e.email AS employee_email, e.salary AS employee_salary,
              e.created_at AS employee_created_at
            FROM
              clients c, orders o, cities ct, employees e
            WHERE
              (o.client_id = c.id) AND (ct.id = c.city_id ) AND (e.id = o.employee_id) AND (o.employee_id = ?)
            ORDER BY
              o.created_at ASC";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute(array($employeeId));

    $orders = [];

    if(!$resp) return $orders;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $orders[] = self::createOrder($row);
    }
    return $orders;
  }

  public static function allOpen() {
    $employeeId = SessionHelpers::currentEmployee()->getId();
    $sql = "SELECT
              o.id AS id, o.created_at AS created_at,  o.total AS total, o.status AS status,
              o.employee_id AS order_employee_id, o.client_id AS order_client_id,
              c.id AS client_id, c.name AS client_name, c.email AS client_email, c.address AS client_address,
              c.address_number AS client_address_number, c.address_cep AS client_cep, c.phone AS client_phone,
              c.created_at AS client_created_at, c.city_id AS client_city_id, c.type AS client_type,
              ct.id AS city_id, ct.name AS city_name, ct.created_at AS city_created_at, ct.state_id AS state_id,
              e.id AS employee_id, e.name AS employee_name, e.email AS employee_email, e.salary AS employee_salary,
              e.created_at AS employee_created_at
            FROM
              clients c, orders o, cities ct, employees e
            WHERE
              (o.client_id = c.id) AND (ct.id = c.city_id ) AND (e.id = o.employee_id) AND (o.employee_id = ?) AND (o.status = ?)
            ORDER BY
              o.created_at ASC";

    $params = array($employeeId, 'Aberto');
    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $orders = [];

    if(!$resp) return $orders;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $orders[] = self::createOrder($row);
    }
    return $orders;
  }

  public static function allClose() {
    $employeeId = SessionHelpers::currentEmployee()->getId();
    $sql = "SELECT
              o.id AS id, o.created_at AS created_at,  o.total AS total, o.status AS status,
              o.employee_id AS order_employee_id, o.client_id AS order_client_id,
              c.id AS client_id, c.name AS client_name, c.email AS client_email, c.address AS client_address,
              c.address_number AS client_address_number, c.address_cep AS client_cep, c.phone AS client_phone,
              c.created_at AS client_created_at, c.city_id AS client_city_id, c.type AS client_type,
              ct.id AS city_id, ct.name AS city_name, ct.created_at AS city_created_at, ct.state_id AS state_id,
              e.id AS employee_id, e.name AS employee_name, e.email AS employee_email, e.salary AS employee_salary,
              e.created_at AS employee_created_at
            FROM
              clients c, orders o, cities ct, employees e
            WHERE
              (o.client_id = c.id) AND (ct.id = c.city_id ) AND (e.id = o.employee_id) AND (o.employee_id = ?) AND (o.status = ?)
            ORDER BY
              o.created_at ASC";

    $params = array($employeeId, 'Fechado');
    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

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
              o.id AS id, o.created_at AS created_at, o.status AS status, o.total AS total,
              o.employee_id AS order_employee_id, o.client_id AS order_client_id,
              c.id AS client_id, c.name AS client_name, c.email AS client_email, c.address AS client_address,
              c.address_number AS client_address_number, c.address_cep AS client_cep, c.phone AS client_phone,
              c.created_at AS client_created_at, c.city_id AS client_city_id, c.type AS client_type, ct.id AS city_id,
              ct.name AS city_name, ct.created_at AS city_created_at, ct.state_id AS state_id, e.id AS employee_id,
              e.name AS employee_name, e.email AS employee_email, e.salary AS employee_salary,
              e.created_at AS employee_created_at
            FROM
              clients c, orders o, cities ct, employees e
            WHERE
              (o.client_id = c.id) AND (o.id = ? ) AND (c.city_id = ct.id) AND (o.employee_id = e.id)";

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
    $order->setEmployeeId($row['order_employee_id']);
    $order->setClientId($row['order_client_id']);
    $order->setTotal($row['total']);
    $order->setCreatedAt($row['created_at']);

    $client = new Client();
    $client->setId($row['client_id']);
    $client->setName($row['client_name']);
    $client->setEmail($row['client_email']);
    $client->setCityId($row['client_city_id']);
    $client->setAddress($row['client_address']);
    $client->setAddressNumber($row['client_address_number']);
    $client->setAddressCep($row['client_cep']);
    $client->setPhone($row['client_phone']);
    $client->setType($row['client_type']);
    $client->setCreatedAt($row['client_created_at']);

    $city = new City();
    $city->setId($row['city_id']);
    $city->setName($row['city_name']);
    $city->setCreatedAt($row['city_created_at']);
    $city->setStateId($row['state_id']);

    $client->setCity($city);

    $employee = new Employee();
    $employee->setId($row['employee_id']);
    $employee->setName($row['employee_name']);
    $employee->setEmail($row['employee_email']);
    $employee->setSalary($row['employee_salary']);
    $employee->setCreatedAt($row['employee_created_at']);

    $order->setClient($client);
    $order->setEmployee($employee);

    return $order;
  }

} ?>
