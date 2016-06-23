<?php class Report extends Base {

  private $employeeId;
  private $employee;
  private $amount;
  private $product;
  private $total;
  private $closedAt;

  public function setEmployeeId($employeeId) {
    $this->employeeId = $employeeId;
  }

  public function getEmployeeId() {
    return $this->employeeId;
  }

  public function setEmployee($employee) {
    $this->employee = $employee;
  }

  public function getEmployee() {
    return $this->employee;
  }

  public function setProduct($product) {
    $this->product = $product;
  }

  public function getProduct() {
    return $this->product;
  }

  public function setTotal($total) {
    $this->total = $total;
  }

  public function getTotal() {
    return $this->total;
  }

  public function setAmount($amount) {
    $this->amount = $amount;
  }

  public function getAmount() {
    return $this->amount;
  }

  public function setClosedAt($closedAt) {
    $this->closedAt = $closedAt;
  }

  public function getClosedAt() {
    return $this->closedAt;
  }

  public static function employeeWhoDidMoreSales() {
      $sql = "SELECT
                employee_id, name AS employee_name, COUNT(orders.employee_id) amount, SUM(total) total
              FROM
                orders, employees
              WHERE
                (orders.employee_id = employees.id) GROUP BY (employee_id)
              ORDER BY
                3 DESC;";

      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute();

      $reports = [];

      if(!$resp) return $reports;

      while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $reports[] = self::createEmployees($row);
      }
      return $reports;
  }

  public static function findByDateEmployee($createdAt, $closedAt) {
      $sql = "SELECT
                employee_id, name AS employee_name, COUNT(orders.employee_id) amount, SUM(total) total
              FROM
                orders, employees
              WHERE
                (orders.employee_id = employees.id) AND
                  (orders.created_at > ? AND orders.closed_at <= ?) AND (orders.status = ?) GROUP BY (employee_id)
              ORDER BY
                3 DESC;";

      $params = array($createdAt, $closedAt, 'Fechado');
      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute($params);

      $reports = [];

      if(!$resp) return $reports;

      while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $reports[] = self::createEmployees($row);
      }
      return $reports;
  }

  public static function bestSellingProducts() {
      $sql = "SELECT
                product_id, name AS product_name, products.price AS product_price,
                SUM(sell_orders_items.amount) AS amount, SUM(sell_orders_items.amount*products.price) total
              FROM
                sell_orders_items, products
              WHERE
                (sell_orders_items.product_id = products.id)
              GROUP BY
                product_id
              ORDER BY
                amount DESC";

      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute();

      $reports = [];

      if(!$resp) return $reports;

      while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $reports[] = self::createProducts($row);
      }
      return $reports;
  }

  public static function findByDateBestSelling($createdAt, $closedAt) {
      $sql = "SELECT
                product_id, name AS product_name, products.price AS product_price,
                SUM(sell_orders_items.amount) amount, SUM(sell_orders_items.amount*products.price) total
              FROM
                sell_orders_items, products, orders
              WHERE
                (sell_orders_items.product_id = products.id) AND (orders.id = sell_orders_items.order_id) AND
                  orders.created_at >  ? AND orders.closed_at  <= ? AND orders.status = ?
              GROUP BY
                product_id
              ORDER BY
                4 DESC";

      $params = array($createdAt, $closedAt, 'Fechado');
      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute($params);

      $reports = [];

      if(!$resp) return $reports;

      while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $reports[] = self::createProducts($row);
      }
      return $reports;
  }

  public static function findByDateLeastSelling($createdAt, $closedAt) {
      $sql = "SELECT
                product_id, name AS product_name, products.price AS product_price,
                SUM(sell_orders_items.amount) amount, SUM(sell_orders_items.amount*products.price) total
              FROM
                sell_orders_items, products, orders
              WHERE
                (sell_orders_items.product_id = products.id) AND (orders.id = sell_orders_items.order_id) AND
                  orders.created_at > ? AND orders.closed_at  <= ? AND orders.status = ?
              GROUP BY
                product_id
              ORDER BY
                4 ASC";

      $params = array($createdAt, $closedAt, 'Fechado');
      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute($params);

      $reports = [];

      if(!$resp) return $reports;

      while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $reports[] = self::createProducts($row);
      }
      return $reports;
  }

  public static function leastSellingProducts() {
      $sql = "SELECT
                product_id, name AS product_name, products.price AS product_price,
                SUM(sell_orders_items.amount) amount, SUM(sell_orders_items.amount*products.price) total
              FROM
                sell_orders_items, products
              WHERE
                (sell_orders_items.product_id = products.id)
              GROUP BY
                product_id
              ORDER BY
                4 ASC";

      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $resp = $statement->execute();

      $reports = [];

      if(!$resp) return $reports;

      while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $reports[] = self::createProducts($row);
      }
      return $reports;
  }

  private static function createEmployees($row) {
    $report = new Report();
    $report->setAmount($row['amount']);
    $report->setTotal($row['total']);

    $employee = new Employee();
    $employee->setId($row['employee_id']);
    $employee->setName($row['employee_name']);

    $report->setEmployee($employee);

    return $report;
  }

  private static function createProducts($row) {
    $report = new Report();
    $report->setAmount($row['amount']);
    $report->setTotal($row['total']);

    $product = new Product();
    $product->setId($row['product_id']);
    $product->setName($row['product_name']);
    $product->setPrice($row['product_price']);

    $report->setProduct($product);

    return $report;
  }




} ?>
