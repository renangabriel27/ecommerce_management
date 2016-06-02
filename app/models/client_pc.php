<?php class ClientPc extends Client {

  private $cnpj;
  private $companyName;
  private $clientId;

  public function setCnpj($cnpj) {
    $cnpj = str_replace('.', '', $cnpj);
    $cnpj = str_replace('-', '', $cnpj);
    $cnpj = str_replace('/', '', $cnpj);
    $this->cnpj = $cnpj;
  }

  public function getCnpj() {
    return $this->cnpj;
  }

  public function setCompanyName($companyName) {
    $this->companyName = $companyName;
  }

  public function getCompanyName() {
    return $this->companyName;
  }

  public function setClientId($clientId) {
    $this->clientId = $clientId;
  }

  public function getClientId() {
    return $this->clientId;
  }

  public function validates() {
    parent::validates();
    Validations::notEmpty($this->cnpj, 'cnpj', $this->errors);
    Validations::notEmpty($this->companyName, 'companyName', $this->errors);
  }

  public function save() {
    if (!$this->isvalid()) return false;
    $this->type = 2;

    $sql = "INSERT INTO
              clients (name, email, phone, address , address_number, address_cep, city_id, type)
            VALUES
              (:name, :email, :phone, :address, :address_number, :address_cep, :city_id, :type);";

    $params = array('name' => $this->name, 'email' => $this->email, 'phone' => $this->phone, 'address' => $this->address,
                    'address_number' => $this->addressNumber, 'address_cep' => $this->addressCep, 'city_id' => $this->cityId,
                    'type' => $this->type);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());
    $this->setCreatedAt(date());

    $sql = "INSERT INTO clients_pc (company_name, cnpj , client_id) VALUES (:companyName, :cnpj, :client_id);";

    $params = array('companyName' => $this->companyName, 'cnpj' => $this->cnpj, 'client_id' => $this->id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);


    return true;
  }


  public function update($data = array()) {
    $this->setData($data);
    if (!$this->isvalid()) return false;

    $db = Database::getConnection();
    $params = array($this->name, $this->address, $this->addressNumber, $this->addressCep,
                    $this->phone, $this->email, $this->cityId, $this->clientId);

    $sql = "UPDATE clients SET name = ?, address = ? , address_number = ?, address_cep = ?, phone = ?, email = ?, city_id = ? WHERE id = ?";

    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $params = array($this->cnpj, $this->companyName, $this->clientId);

    $sql = "UPDATE clients_pc SET cnpj= ?, company_name = ? WHERE client_id = ?";

    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if(!$resp) return false;

    return true;
  }

  public static function findById($id) {
    $sql = "SELECT
              c.id, c.name, c.email, c.address, c.address_cep, c.address_number, c.city_id,
              c.phone, c.type, c.created_at, cities.id AS city_id, cities.name AS city_name,
              cities.state_id AS state_id, cities.created_at AS city_created_at, cp.id, cp.cnpj,
              cp.company_name, cp.client_id
            FROM
              clients c, clients_pc cp, cities WHERE (c.city_id = cities.id) AND (c.id = :id) AND (cp.client_id = :id)
            ORDER BY
              client_id";

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

  public static function all() {
    $sql = "SELECT
              c.name, c.email, c.address, c.address_cep, c.address_number, c.city_id,
              c.phone, c.type, c.created_at, cities.id AS city_id, cities.name AS city_name,
              cities.state_id AS state_id, cities.created_at AS city_created_at, cp.id, cp.cnpj,
              cp.company_name, cp.client_id
            FROM
              clients c, clients_pc cp, cities WHERE (c.city_id = cities.id) AND (c.id = cp.client_id)
            ORDER BY
              client_id";

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute();

    $clients = [];

    if(!$resp) return $clients;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $clients[] =  self::createClient($row);
    }
    return $clients;
  }

  public function deleteClient($id) {
    $sql = "DELETE FROM clients, clients_pc USING clients, clients_pc WHERE clients.id = ? AND clients_pc.client_id =?";

    $params = array($id, $id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

  private static function createClient($row) {
    $client = new ClientPc();
    $client->setId($row['id']);
    $client->setName($row['name']);
    $client->setEmail($row['email']);
    $client->setAddress($row['address']);
    $client->setAddressNumber($row['address_number']);
    $client->setAddressCep($row['address_cep']);
    $client->setPhone($row['phone']);
    $client->setType($row['type']);
    $client->setCnpj($row['cnpj']);
    $client->setCompanyName($row['company_name']);
    $client->setCityId($row['city_id']);
    $client->setClientId($row['client_id']);
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
