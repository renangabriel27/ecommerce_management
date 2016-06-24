<?php class EmployeesController  extends ApplicationController {

  protected $beforeAction = array('authenticated' => array('edit', 'update'));

  public function index() {
     $this->title = 'Sistema';
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
