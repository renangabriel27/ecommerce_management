<?php class OrdersController extends ApplicationController {

   protected $beforeAction = array('authenticated' => 'all');

   public function index() {
      $this->title = 'Listagem de pedidos';
      $this->orders = Order::all();
   }

   public function show() {
     $this->order = Order::findById($this->params[':id']);
   }

   public function _new() {
      $this->order = new Order();
      $this->submit = 'Novo pedido';
      $this->action = ViewHelpers::urlFor("/pedidos");
   }

   public function create() {
     unset($this->params['order']['client_name']);

     $this->order = new Order($this->params['order']);

     if($this->order->save()) {
       Flash::message('success', 'Pedido cadastrado com sucesso!');
       $this->redirectTo('/pedidos');
     }
     else {
       Flash::message('danger', 'Existem dados inválidos!');
       $this->orders = Order::all();
       $this->action = ViewHelpers::urlFor('/pedidos');
       $this->submit = 'Novo pedido';
       $this->render('new');
     }
   }

   public function destroy() {
     $order = Order::findById($this->params[':id']);
     $order->delete();
     Flash::message('success', 'Pedido deletado com sucesso');
     $this->redirectTo("/pedidos");
   }

   public function addProduct() {
     $this->order = Order::findById($this->params['order']['id']);
     $this->order->addProduct($this->params['product']['id']);
     $this->redirectTo("/pedidos/{$this->order->getId()}");
   }

} ?>
