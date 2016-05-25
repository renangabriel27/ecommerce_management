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

  public function setClientId() {
    $this->clientId = $clientId;
  }

  public function getClientId() {
    return $this->clientId();
  }
  public function validates() {
    Validations::notEmpty($this->name, 'name', $this->errors);
    Validations::notEmpty($this->cpf, 'cpf', $this->errors);
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


} ?>
