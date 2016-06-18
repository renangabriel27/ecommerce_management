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

  public function authenticatedEmployee() {
    $this->employeeId = $this->currentEmployee()->getId();

    if(!$this->order->getEmployeeOrder($this->employeeId)) {
      Flash::message('negative', 'Você não pode acessar esta página');
      $this->redirectTo('/pedidos');
    }
  }

  public function orderIsClosed() {
    if($this->order->getStatus() == 'Fechado') {
      $this->redirectTo("/pedidos");
    }
  }

  public function findByParams($orderId, $productId) {
    $this->order = Order::findById($orderId);
    $this->product = Product::findById($productId);
    $this->sellOrderItem = SellOrderItem::findById($productId, $orderId);
  }

}
