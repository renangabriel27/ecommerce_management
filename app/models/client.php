<?php class Client extends Base {

  private $name;
  private $email;
  private $password;
  private $address;
  private $addressNumber;
  private $addressCep;
  private $dateOfBirth;
  private $phone;
  private $cityId;
  private $city;


  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }

  public function setEmail($email) {
    $this->email = $email;
  }
  public function getEmail() {
    return $this->email;
  }

  public function setPassword($password) {
    $this->password= $password;
  }

  public function setAddress($address) {
    $this->address = $address;
  }

  public function getAddress() {
    return $this->address;
  }

  public function setAddressNumber($addressNumber) {
    $this->addressNumber = $addressNumber;
  }

  public function getAddressNumber() {
    return $this->addressNumber;
  }

  public function setAddressCep($addressCep) {
    $this->addressCep = $addressCep;
  }

  public function getAddressCep() {
    return $this->addressCep;
  }

  public function setDateOfBirth($dateOfBirth) {
    $this->dateOfBirth = $dateOfBirth;
  }

  public function getDateOfBirth() {
    return $this->dateOfBirth;
  }

  public function setPhone($phone) {
    $this->phone = $phone;
  }

  public function getPhone() {
    return $this->phone;
  }

  public function setCityId($cityId) {
    $this->cityId = $cityId;
  }

  public function getCityId() {
    return $this->cityId;
  }

  public function setCity($city) {
    $this->city = $city;
  }

  public function getCity() {
    return $this->city;
  }

  public function validates() {
    Validations::notEmpty($this->name, 'name', $this->errors);

    /* Como o campo é único é necessário atualizar caso não tenha mudado*/
    if ($this->newRecord() || $this->changedFieldValue('email', 'client')) {
      Validations::validEmail($this->email, 'email', $this->errors);
      Validations::uniqueField($this->email, 'email', 'client', $this->errors);
    }

    if ($this->newRecord()) /* Caso a senha seja vazia não deve ser atualizada */
      Validations::notEmpty($this->password, 'password', $this->errors);
  }

  public function save() {
    if (!$this->isvalid()) return false;

    $sql = "INSERT INTO clients (name, email, password)
            VALUES (:name, :email, :password);";

    $params = array('name' => $this->name, 'email' => $this->email,
                    'password' => $this->cryptographyPassword($this->password));

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());
    return true;
  }

  public function update($data = array()) {
    $this->setData($data);
    if (!$this->isvalid()) return false;

    $db = Database::getConnection();
    $params = array('name' => $this->name,
      'email' => $this->email,
      'id' => $this->id);

    if (empty($this->password)) {
      $sql = "UPDATE clients SET name=:name, email=:email WHERE id = :id";
    } else {
      $params['password'] = $this->cryptographyPassword($this->password);
      $sql = "UPDATE clients SET name=:name, email=:email, password=:password WHERE id = :id";
    }

    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public function authenticate($password) {
    if ($this->password === $this->cryptographyPassword($password)) {
      SessionHelpers::logIn($this);
      return true;
    }
    return false;
  }

  private function cryptographyPassword($password) {
    return sha1(sha1('dw3'.$password));
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT id, name, email, password FROM clients WHERE id = ?";
    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $client = new Client($row);
      return $client;
    }

    return null;
  }

  public static function all() {
    $sql = "SELECT clients.id AS id, clients.name, clients.email,
    clients.address_cep, clients.phone, clients.city_id, cities.name AS city
    FROM clients, cities WHERE (clients.city_id = cities.id) ORDER BY clients.created_at DESC";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute();

    $clients = [];

    if(!$resp) return $clients;

    while($client = $statement->fetch(PDO::FETCH_ASSOC)) {
      $clients[] = new Client($client);
    }

    return $clients;
  }

  public static function findByEmail($email) {
    $db = Database::getConnection();
    $sql = "SELECT id, name, email, password FROM employees WHERE email = ?";
    $params = array($email);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $user = new User($row);
      return $user;
    }

    return null;
  }

  public static function whereNameLikeAsJson($query) {
    $sql = "SELECT * FROM clients WHERE name LIKE :query ORDER BY name";
    $params = ['query' => '%' . $query . '%'];

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $suggestions = ['suggestions' => ''];

    if(!$resp) return $suggestions;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $suggestions['suggestions'][] = array('value' => $row['name'], 'data' => $row['id']);
    }

    return json_encode($suggestions);
  }

} ?>
