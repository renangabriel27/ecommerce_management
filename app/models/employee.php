<?php class Employee extends Base {

  private $name;
  private $email;
  private $password;
  private $salary;
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

  public function setSalary($salary) {
    $this->salary = $salary;
  }

  public function getSalary() {
    return $this->salary;
  }

  public function setCityId($cityId) {
    $this->cityId = $cityId;
  }

  public function getCityId() {
    return $this->getCityId;
  }

  public function setCity($city) {
    $this->city = $city;
  }

  public function getCity() {
    return $this->city;
  }

  public function validates() {
    Validations::notEmpty($this->name, 'name', $this->errors);

    if ($this->newRecord() || $this->changedFieldValue('email', 'employees')) {
      Validations::validEmail($this->email, 'email', $this->errors);
      Validations::uniqueField($this->email, 'email', 'employees', $this->errors);
    }

    if ($this->newRecord()) /* Caso a senha seja vazia não deve ser atualizada */
      Validations::notEmpty($this->password, 'password', $this->errors);
  }

  public function save() {
    if (!$this->isvalid()) return false;

    $sql = "INSERT INTO
              employees (name, email, password)
            VALUES
              (:name, :email, :password);";

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
    $params = array('name' => $this->name, 'email' => $this->email, 'id' => $this->id);

    if (empty($this->password)) {
      $sql = "UPDATE employees SET name=:name, email=:email WHERE id = :id";
    } else {
      $params['password'] = $this->cryptographyPassword($this->password);
      $sql = "UPDATE employees SET name=:name, email=:email, password=:password WHERE id = :id";
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
    $sql = "SELECT
              e.id AS employee_id, e.name AS employee_name, e.email AS employee_email, e.password AS employee_password,
              e.city_id AS employee_city_id, e.salary AS employee_salary, e.created_at AS employee_created_at,
              c.id AS city_id, c.name AS city_name, c.state_id AS state_id, c.created_at AS city_created_at
              FROM
                employees e, cities c
              WHERE
                e.id = ? AND (e.city_id = c.id)";
    $params = array($id);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return self::createEmployee($row);
    }

    return null;
  }

  public static function findByEmail($email) {
    $sql = "SELECT
              e.id AS employee_id, e.name AS employee_name, e.email AS employee_email, e.password AS employee_password,
              e.city_id AS employee_city_id, e.salary AS employee_salary, e.created_at AS employee_created_at,
              c.id AS city_id, c.name AS city_name, c.state_id AS state_id, c.created_at AS city_created_at
              FROM
                employees e, cities c
              WHERE
                (e.email = ?) AND (e.city_id = c.id)";
    $params = array($email);

    $db = Database::getConnection();
    $statement = $db->prepare($sql);
    $resp = $statement->execute($params);

    if ($resp && $row = $statement->fetch(PDO::FETCH_ASSOC)) {
      return self::createEmployee($row);
    }
    return null;
  }

  private static function createEmployee($row) {
    $employee = new Employee();
    $employee->setId($row['employee_id']);
    $employee->setName($row['employee_name']);
    $employee->setEmail($row['employee_email']);
    $employee->setPassword($row['employee_password']);
    $employee->setSalary($row['employee_salary']);
    $employee->setCityId($row['employee_city_id']);
    $employee->setCreatedAt($row['employee_created_at']);

    $city = new City();
    $city->setId($row['city_id']);
    $city->setName($row['city_name']);
    $city->setStateId($row['state_id']);
    $city->setCreatedAt($row['city_created_at']);

    $employee->setCity($city);

    return $employee;
  }

} ?>
