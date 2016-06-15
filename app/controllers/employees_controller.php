<?php class EmployeesController  extends ApplicationController {

  protected $beforeAction = array('authenticated' => array('edit', 'update'));

  public function index() {
     $this->title = 'Sistema';
  }

  public function _new() {
    $this->employee = new Employee();
    $this->action = '/registre-se';
    $this->submit = 'Cadastre-se';
  }

  public function create(){
    $this->employee = new Employee($this->params['employee']);

    if ($this->employee->save()) {
      Flash::message('success', 'Registro realizado com sucesso!');
      $this->redirectTo('/login');
    } else {
      Flash::message('negative', 'Existe dados incorretos no seu formulário!');
      $this->action = '/registre-se';
      $this->submit = 'Cadastre-se';
      $this->render('new');
    }
  }

  public function edit() {
    $this->employee = $this->currentEmployee();
    $this->action = '/perfil';
    $this->submit = 'Atualizar';
  }

  public function update() {
    $this->employee = $this->currentEmployee();

    if ($this->employee->update($this->params['employee'])) {
      Flash::message('success', 'Registro atualizado com sucesso!');
      $this->redirectTo('/');
    } else {
      Flash::message('negative', 'Existe dados incorretos no seu formulário!');
      $this->action = '/perfil';
      $this->submit = 'Atualizar';
      $this->render('edit');
    }
  }

} ?>
