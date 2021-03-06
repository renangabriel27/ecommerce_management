<?php class SellOrderItem extends Base {

  private $price;
  private $amount;
  private $orderId;
  private $productId;

  public function setPrice($price) {
    $this->price = $price;
  }

  public function getPrice() {
    return $this->price;
  }

  public function setAmount($amount) {
    return $this->amount;
  }

  public function getAmount() {
    return $this->amount;
  }

  public function setOrderId($orderId) {
    $this->orderId = $orderId;
  }

  public function getOrderId() {
    return $this->orderId;
  }

  public function setProductId($productId) {
    $this->productId = $productId;
  }

  public function getProductId() {
    return $this->productId;
  }

  public function save($productId, $orderId) {
    $sql = "INSERT INTO
              sell_orders_items (price, order_id, product_id)
            VALUES
              ((SELECT price FROM products WHERE products.id = :product_id), :order_id, :product_id)";

    $params = array('product_id' => $productId, 'order_id' => $orderId);
    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if(!$resp) return false;

    return true;
  }

  public function addAmountProduct() {
    $this->amount = self::getAmountOfProduct($this->productId, $this->orderId);
    $this->amount++;

    $params = array('id' => $this->productId, 'amount' => $this->amount, 'order_id' => $this->orderId);
    $sql = "UPDATE sell_orders_items SET amount= :amount WHERE product_id = :id AND order_id = :order_id";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if(!$resp) return false;

    return true;
  }

  public function removeProduct() {
    $this->amount = self::getAmountOfProduct($this->productId, $this->orderId);

    if(($this->amount-1) == 0) {
      $sellOrderItem = self::findById($this->productId, $this->orderId);
      return $sellOrderItem->delete('sell_orders_items');
    }

    $this->amount--;
    $params = array('id' => $this->productId, 'amount' => $this->amount, 'order_id' => $this->orderId);
    $sql = "UPDATE sell_orders_items SET amount= :amount WHERE product_id = :id AND order_id = :order_id";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public static function getAmountOfProduct($productId, $orderId) {
      $params = array($productId, $orderId);
      $sql = "SELECT amount FROM sell_orders_items WHERE product_id = ? AND order_id = ?";

      $db = Database::getConnection();
      $statement = $db->prepare($sql);
      $statement->execute($params);;

      return $statement->fetch()[0];
  }

  public static function findById($productId, $orderId) {
    $sql = "SELECT * FROM sell_orders_items WHERE product_id = ? AND order_id = ?";
    $params = array($productId, $orderId);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return new SellOrderItem($row);
    }
    return null;
  }

  public static function findByIdPrice($orderId, $productId) {
    $sql = "SELECT DISTINCT price FROM sell_orders_items WHERE product_id = ? AND order_id = ?";
    $params = array($productId, $orderId);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);
    return $statement->fetch()[0];
  }

}

?>
