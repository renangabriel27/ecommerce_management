<?php class ApplicationController extends BaseController {
  private $currentUser;

  /* Mudar layout da rota */
  // public function __construct() {
  //   parent::__construct();
  //   $this->layout = 'layout/application.phtml';
  // }

  public function currentUser() {
    if ($this->currentUser === null) {
      $this->currentUser = SessionHelpers::currentUser();
    }
    return $this->currentUser;
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

}
