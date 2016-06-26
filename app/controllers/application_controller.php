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
    if($this->params[':id']) {
      $this->order = Order::findById($this->params[':id']);
      $this->employeeId = $this->currentEmployee()->getId();

      if(!$this->order->getEmployeeOrder($this->employeeId)) {
        Flash::message('negative', 'Você não pode acessar esta página');
        $this->redirectTo('/pedidos');
      }
    }
  }

  public function createObjects() {
    if(isset($this->params[':id']) && isset($this->params[':product_id'])) {
      $this->findByParams($this->params[':id'], $this->params[':product_id']);
    } else {
      $this->findByParams($this->params['order']['id'], $this->params['product']['id']);
    }
  }

  public function validateProduct() {
    if($this->order->emptyProduct($this->params['product']['name'])) {
      Flash::message('negative', 'Insira algum produto!');
      $this->redirectTo("/pedidos/{$this->order->getId()}");
    }
    if($this->order->uniqueProduct($this->params['product']['id'])) {
      Flash::message('negative', 'Esse produto já está cadastrado no pedido!');
      $this->redirectTo("/pedidos/{$this->order->getId()}");
    }
  }

  public function findByParams($orderId, $productId) {
    $this->order = Order::findById($orderId);
    $this->product = Product::findById($productId);
    $this->sellOrderItem = SellOrderItem::findById($productId, $orderId);
    if(!$this->sellOrderItem) $this->sellOrderItem = new SellOrderItem();
  }

  public function pagination($class, $method, $options) {
    $this->url = $options['url'];
    $this->limit = $options['limit'];

    $this->page = isset($this->params[':page']) ? $this->params[':page'] : 1;
    $offset = ($this->page-1)*$this->limit;
    $this->totalOfRegisters = $class::count();
    $this->totalOfPages = ceil($this->totalOfRegisters/$this->limit);

    if(sizeof($options) > 2) {
      $this->param = $options['param'];
      $options = array('limit' => $this->limit,'offset' => $offset, 'param' => $this->param);
    }
    else {
      $options = array('limit' => $this->limit,'offset' => $offset);
    }
    $registers = $class::$method($options);

    return $registers;
  }

}
