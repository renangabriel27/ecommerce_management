<?php class SessionsController extends ApplicationController {

  protected $beforeAction = array('notBeAuthenticated' => array('_new'));

  public function _new() {
    $this->employee = new Employee();
  }

  public function create() {
    $email = $this->params['employee']['email'];
    $password = $this->params['employee']['password'];

    $this->employee = Employee::findByEmail($email);
    if ($this->employee && $this->employee->authenticate($password)) {
      Flash::message('success', 'Usuário autenticado com sucesso!');
      $this->redirectTo('/');
    } else {
      $this->employee = new Employee();
      Flash::message('negative', 'Usuário ou senha inválidos!');
      $this->render('new');
    }
  }

  public function destroy() {
    SessionHelpers::logOut();
    Flash::message('success', 'Logout com sucesso!');
    $this->redirectTo('/login');
  }

} ?>
