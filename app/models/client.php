<?php class Client extends Base {

  protected $name;
  protected $email;
  protected $address;
  protected $addressNumber;
  protected $addressCep;
  protected $phone;
  protected $city;
  protected $type;


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

  public function setPhone($phone) {
    $this->phone = $phone;
  }

  public function getPhone() {
    return $this->phone;
  }

  public function setCity($city) {
    $this->city = $city;
  }

  public function getCity() {
    return $this->city;
  }

  public function setType($type) {
    $this->type = $type;
  }

  public function getType() {
    return $this->type;
  }

  public function validates() {
    Validations::notEmpty($this->name, 'name', $this->errors);
    Validations::notEmpty($this->phone, 'phone', $this->errors);
    Validations::notEmpty($this->dateOfBirth, 'dateOfBirth', $this->errors);
    Validations::notEmpty($this->address, 'address', $this->errors);
    Validations::notEmpty($this->addressNumber, 'addressNumber', $this->errors);
    Validations::notEmpty($this->addressCep, 'addressCep', $this->errors);

    /* Como o campo é único é necessário atualizar caso não tenha mudado*/
    if ($this->newRecord() || $this->changedFieldValue('email', 'client')) {
      Validations::validEmail($this->email, 'email', $this->errors);
      Validations::uniqueField($this->email, 'email', 'clients', $this->errors);
    }

  }

  public function save() {
    if (!$this->isvalid()) return false;

    $sql = "INSERT INTO clients (name, email, phone, date_of_birth, address , address_number, address_cep, city_id )
            VALUES (:name, :email, :phone, :date_of_birth, :address, :address_number, :address_cep, :city);";

    $params = array('name' => $this->name, 'email' => $this->email, 'phone' => $this->phone, 'date_of_birth' => $this->dateOfBirth,
                    'city' => $this->city, 'address' => $this->address, 'address_number' => $this->addressNumber, 'address_cep' => $this->addressCep);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());
    $this->setCreatedAt(date());
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


  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT name, email FROM clients WHERE id = ?";
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
    clients.address_cep, clients.phone, clients.city_id AS city, cities.name AS city
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
