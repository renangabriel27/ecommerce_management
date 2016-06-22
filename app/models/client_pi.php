<?php class ClientPi extends Client {

  private $cpf;
  private $dateOfBirth;

  public function setCpf($cpf) {
    $cpf = str_replace('.', '', $cpf);
    $cpf = str_replace('-', '', $cpf);
    $this->cpf = $cpf;
  }

  public function getCpf() {
    return $this->cpf;
  }

  public function setDateOfBirth($dateOfBirth) {
    $this->dateOfBirth = $dateOfBirth;
  }

  public function getDateOfBirth() {
    return $this->dateOfBirth;
  }

  public function validates() {
    parent::validates();
    Validations::notEmpty($this->cpf, 'cpf', $this->errors);
    Validations::notEmpty($this->dateOfBirth, 'dateOfBirth', $this->errors);
  }

  public function save() {
    if (!$this->isvalid()) return false;
    $this->type = 1;

    $sql = "INSERT INTO
              clients (name, email, phone, address , address_number, address_cep, city_id, type)
            VALUES
              (:name, :email, :phone, :address, :address_number, :address_cep, :city_id, :type)";

    $params = array('name' => $this->name, 'email' => $this->email, 'phone' => $this->phone, 'address' => $this->address,
                    'address_number' => $this->addressNumber, 'address_cep' => $this->addressCep,
                    'city_id' => $this->cityId, 'type' => $this->type);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());
    $this->setCreatedAt(date());

    $sql = "INSERT INTO clients_pi (id, date_of_birth, cpf) VALUES (:id, :date_of_birth, :cpf)";

    $params = array('id' => $this->id, 'date_of_birth' => $this->dateOfBirth, 'cpf' => $this->cpf);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    return true;
  }

  public static function findById($id) {
    $sql = "SELECT
              c.id, c.name, c.email, c.address, c.address_cep, c.address_number, c.city_id,
              c.phone, c.type, c.created_at, cities.id AS city_id, cities.name AS city_name,
              cities.state_id AS state_id, cities.created_at AS city_created_at, cp.cpf,
              cp.date_of_birth
            FROM
              clients c, clients_pi cp, cities WHERE (c.city_id = cities.id) AND (c.id = :id) AND (cp.id =:id)
            ORDER BY
              id";

    $params = array('id' => $id, 'id' => $id);
    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $clients = [];

    if(!$resp) return $clients;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return self::createClient($row);
    }
    return null;
  }

  public function update($data = array()) {
    $this->setData($data);
    if (!$this->isvalid()) return false;

    $db = Database::getConnection();
    $params = array($this->name, $this->address, $this->addressNumber, $this->addressCep,
                    $this->phone, $this->email, $this->cityId, $this->id);

    $sql = "UPDATE clients SET name = ?, address = ? , address_number = ?, address_cep = ?,
                    phone = ?, email = ?, city_id = ? WHERE id = ?";

    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $params = array($this->cpf, $this->dateOfBirth, $this->id);

    $sql = "UPDATE clients_pi SET cpf= ? , date_of_birth = ? WHERE id = ?";

    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if(!$resp) return false;

    return true;
  }

  public function deleteClient($id) {
    $sql = "DELETE FROM clients, clients_pi USING clients, clients_pi WHERE clients.id = ? AND clients_pi.id =?";

    $params = array($id, $id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  public static function all($options = []) {
    $sql = "SELECT
              c.id, c.name, c.email, c.address, c.address_cep, c.address_number, c.city_id,
              c.phone, c.type, c.created_at, cities.id AS city_id, cities.name AS city_name,
              cities.state_id AS state_id, cities.created_at AS city_created_at, cp.cpf,
              cp.date_of_birth
            FROM
              clients c, clients_pi cp, cities WHERE (c.city_id = cities.id) AND (c.id = cp.id)
            ORDER BY
              id";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);

    if(sizeof($options) != 0) {
      $sql .= " LIMIT :limit OFFSET :offset ";
      $statement = $db->prepare($sql);
      $statement->bindParam(':limit', $options['limit'], PDO::PARAM_INT);
      $statement->bindParam(':offset', $options['offset'], PDO::PARAM_INT);
    }

    $resp = $statement->execute();

    $clients = [];

    if(!$resp) return $clients;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $clients[] =  self::createClient($row);
    }
    return $clients;
  }

  public static function count() {
    $sql = "SELECT COUNT(*) FROM clients_pi";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $statement->execute();

    return $statement->fetch()[0];
  }

  private static function createClient($row) {
    $client = new ClientPi();
    $client->setId($row['id']);
    $client->setName($row['name']);
    $client->setEmail($row['email']);
    $client->setAddress($row['address']);
    $client->setAddressNumber($row['address_number']);
    $client->setAddressCep($row['address_cep']);
    $client->setPhone($row['phone']);
    $client->setType($row['type']);
    $client->setCpf($row['cpf']);
    $client->setCityId($row['city_id']);
    $client->setDateOfBirth($row['date_of_birth']);
    $client->setCreatedAt($row['created_at']);

    $city = new City();
    $city->setId($row['city_id']);
    $city->setName($row['city_name']);
    $city->setStateId($row['state_id']);
    $city->setCreatedAt($row['created_at']);

    $client->setCity($city);

    return $client;
  }

} ?>
