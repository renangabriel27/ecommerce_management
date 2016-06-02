<?php class ApplicationController extends BaseController {
  private $currentEmployee;

  /* Mudar layout da rota */
  // public function __construct() {
  //   parent::__construct();
  //   $this->layout = 'layout/application.phtml';
  // }

  public function currentEmployee() {
    if ($this->currentEmployee === null) {
      $this->currentEmployee = SessionHelpers::currentEmployee();
    }
    return $this->currentEmployee;
  }

  public function authenticated() {
    if (!SessionHelpers::isLoggedIn()) {
      Flash::message('negative', 'Você deve estar logado para acessar esta página');
      $this->redirectTo('/login');
    }
  }

  public function notBeAuthenticated() {
    if (SessionHelpers::isLoggedIn()) {
      Flash::message('negative', 'Você deve estar deslogado para acessar esta página');
      $this->redirectTo('/');
    }
  }

  public function newClientType($type, $client = '') {
    if (!$client) return ($type == 1) ? new ClientPi() : new ClientPc();

    return ($type == 1) ? new ClientPi($client) : new ClientPc($client);
  }

  public function clientForm($type) {
    return ($type == 1) ? '_form_client_pi.phtml' : '_form_client_pc.phtml';
  }

}
