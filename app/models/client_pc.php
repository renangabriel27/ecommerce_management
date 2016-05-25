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

  public function setClientId() {
    $this->clientId = $clientId;
  }

  public function getClientId() {
    return $this->clientId();
  }

  public function validates() {
    Validations::notEmpty($this->name, 'name', $this->errors);
    Validations::notEmpty($this->cnpj, 'cnpj', $this->errors);
    Validations::notEmpty($this->phone, 'phone', $this->errors);
    Validations::notEmpty($this->companyName, 'companyName', $this->errors);
    Validations::notEmpty($this->address, 'address', $this->errors);
    Validations::notEmpty($this->addressNumber, 'addressNumber', $this->errors);
    Validations::notEmpty($this->addressCep, 'addressCep', $this->errors);

    /* Como o campo é único é necessário atualizar caso não tenha mudado*/
    if ($this->newRecord() || $this->changedFieldValue('email', 'client')) {
      Validations::validEmail($this->email, 'email', $this->errors);
      Validations::uniqueField($this->email, 'email', 'clients', $this->errors);
    }
  }


} ?>
