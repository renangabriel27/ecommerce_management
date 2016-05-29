<?php class ClientPi extends Client {

  private $cpf;
  private $dateOfBirth;
  private $clientId;

  public function setCpf($cpf) {
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

  public function setClientId($clientId) {
    $this->clientId = $clientId;
  }

  public function getClientId() {
    return $this->clientId;
  }

  public function validates() {
    Validations::notEmpty($this->name, 'name', $this->errors);
    Validations::notEmpty($this->cpf, 'cpf', $this->errors);
    Validations::notEmpty($this->cityId, 'city_id', $this->errors);
    Validations::notEmpty($this->phone, 'phone', $this->errors);
    Validations::notEmpty($this->dateOfBirth, 'dateOfBirth', $this->errors);
    Validations::notEmpty($this->address, 'address', $this->errors);
    Validations::notEmpty($this->addressNumber, 'addressNumber', $this->errors);
    Validations::notEmpty($this->addressCep, 'addressCep', $this->errors);

    /* Como o campo é único é necessário atualizar caso não tenha mudado*/
    if ($this->newRecord() || $this->changedFieldValue('email', 'clients')) {
      Validations::validEmail($this->email, 'email', $this->errors);
      Validations::uniqueField($this->email, 'email', 'clients', $this->errors);

    }
  }

  public function save() {
    if (!$this->isvalid()) return false;

    $sql = "INSERT INTO clients (name, email, phone, address , address_number, address_cep, city_id, type)
            VALUES (:name, :email, :phone, :address, :address_number, :address_cep, :city_id, :type)";

    $params = array('name' => $this->name, 'email' => $this->email, 'phone' => $this->phone, 'address' => $this->address,
                    'address_number' => $this->addressNumber, 'address_cep' => $this->addressCep, 'city_id' => $this->cityId,
                    'type' => $this->type);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $this->setId($db->lastInsertId());
    $this->setCreatedAt(date());

    $sql = "INSERT INTO clients_pi (date_of_birth, cpf , client_id) VALUES (:date_of_birth, :cpf, :client_id);";

    $params = array('date_of_birth' => $this->dateOfBirth, 'cpf' => $this->cpf, 'client_id' => $this->id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);


    return true;
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT * FROM clients, clients_pi WHERE clients.id = ?";
    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $client = new ClientPi($row);
      return $client;
    }

    return null;
  }

  public function update($data = array()) {
    $this->setData($data);
    if (!$this->isvalid()) return false;

    $db = Database::getConnection();
    $params = array($this->name, $this->address, $this->addressNumber, $this->addressCep,
                    $this->phone, $this->email, $this->cityId, $this->clientId);

    $sql = "UPDATE clients SET name = ?, address = ? , address_number = ?, address_cep = ?,
                    phone = ?, email = ?, city_id = ? WHERE id = ?";

    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $params = array($this->cpf, $this->dateOfBirth, $this->id);

    $sql = "UPDATE clients_pi SET cpf= ? , date_of_birth = ? WHERE client_id = ?";

    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if(!$resp) {
      Loger::getInstance()->log("Falha ao atualizar o cliente: " . print_r($this, TRUE), Logger::ERROR);
      Logger::getInstance()->log("Error " . print_r(error_get_last(), true ), Logger::ERROR);
      return false;
    }
    return true;
  }


} ?>
