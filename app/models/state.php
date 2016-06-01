<?php class State extends Base {

  private $uf;
  private $name;

  public function setUf($Uf) {
      $this->uf = $uf;
  }

  public function getUf() {
    return $this->uf;
  }

  public function setName($name) {
      $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public static function all() {
    $sql = "SELECT * FROM states ORDER BY id";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute();

    $states = [];

    if(!$resp) return $states;

    while($state = $statement->fetch(PDO::FETCH_ASSOC)) {
      $states[] = new State($state);
    }
    return $states;
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT * FROM states WHERE id = ?";
    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $state = new State($row);
      return $state;
    }

    return null;
  }

} ?>
