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

  public function addProduct($id) {
    $amount = (int) self::getAmountOfProduct($id);
    $amount++;
    $db = Database::getConnection();
    $params = array('id' => $id, 'amount' => $amount);
    $sql = "UPDATE sell_orders_items SET amount= :amount WHERE product_id = :id ";
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public function removeProduct($id) {
    $amount = (int) self::getAmountOfProduct($id);
    if($amount == 0) return null;
    $amount--;
    $db = Database::getConnection();
    $params = array('id' => $id, 'amount' => $amount);
    $sql = "UPDATE sell_orders_items SET amount= :amount WHERE product_id = :id ";
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public static function getAmountOfProduct($id) {
      $db = Database::getConnection();
      $params = array($id);
      $sql = "SELECT amount FROM sell_orders_items WHERE product_id = ?";
      $statement = $db->prepare($sql);
      $statement->execute($params);
      $resp = $statement->fetch(PDO::FETCH_ASSOC);
      $amount = $resp['amount'];
      return $amount;
  }

  public static function findById($productId, $orderId) {
    $db = Database::getConnection();
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

}

?>