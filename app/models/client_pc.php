<?php class ClientPc extends Client {

  private $cnpj;
  private $companyName;
  private $clientId;

  public function setcnpj($cnpj) {
    $this->cnpj = $cnpj;
  }

  public function getcnpj() {
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
    Validations::notEmpty($this->cnpj, 'cnpj', $this->errors);
    Validations::notEmpty($this->companyName, 'companyName', $this->errors);
    parent::validates();
  }

  public function save() {
    if (!$this->isvalid()) return false;
    $this->type = 2;

    $sql = "INSERT INTO clients (name, email, phone, address , address_number, address_cep, city_id, type)
            VALUES (:name, :email, :phone, :address, :address_number, :address_cep, :city_id, :type);";

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

    $sql = "UPDATE clients SET name = ?, address = ? , address_number = ?, address_cep = ?,
    phone = ?, email = ?, city_id = ? WHERE id = ?";

    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    $params = array($this->cnpj, $this->companyName, $this->clientId);

    $sql = "UPDATE clients_pc SET cnpj= ? , company_name = ? WHERE client_id = ?";

    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if(!$resp) {
      Loger::getInstance()->log("Falha ao atualizar o cliente: " . print_r($this, TRUE), Logger::ERROR);
      Logger::getInstance()->log("Error " . print_r(error_get_last(), true ), Logger::ERROR);
      return false;
    }
    return true;
  }

  public static function findById($id) {
    $db = Database::getConnection();
    $sql = "SELECT * FROM clients, clients_pc WHERE clients.id = ? AND clients_pc.client_id = ?";
    $params = array($id, $id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return new ClientPc($row);
    }
    return null;
  }

  public function deleteClient() {
    $db = Database::getConnection();
    $params = array($this->clientId);
    $sql = "DELETE FROM clients WHERE id = ?";
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);
    if(!$resp) return false;

    $sql = "DELETE FROM clients_pc WHERE client_id = ?";
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }

} ?>
