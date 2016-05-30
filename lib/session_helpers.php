<?php
class SessionHelpers {

  public static function shouldBeAutenticated(){
    if (!(isset($_SESSION['employee']))) {
      Flash::message('negative', 'Você deve estar logado para acessar está página!');
      ViewHelpers::redirect_to('/pages/sessions/new');
    }
  }

  public static function shouldNotBeAutenticated(){
    if (isset($_SESSION['employee'])) {
      Flash::message('warning', 'Você deve estar deslogado para acessar está página!');
      ViewHelpers::redirectTo('/');
    }
  }

  public static function currentEmployee() {
    if (isset($_SESSION['employee']['id']))
      return Employee::findById($_SESSION['employee']['id']);

    return null;
  }

  public static function logIn($employee) {
    $_SESSION['employee']['id'] = $employee->getId();
  }

  public static function isLoggedIn() {
    return isset($_SESSION['employee']);
  }

  public static function logOut() {
    unset($_SESSION['employee']);
  }
} ?>
