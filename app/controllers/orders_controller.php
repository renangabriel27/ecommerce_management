<?php class OrdersController extends ApplicationController {

   protected $beforeAction = array('authenticated' => 'all', 'authenticatedOrder' => array('addItemsProduct'));

   public function index() {
      $this->title = 'Todos os pedidos';
      $this->orders = Order::all();
   }

   public function open() {
     $this->title = 'Pedidos em aberto';
     $this->orders = Order::all('Aberto');
   }

   public function close() {
     $this->title = 'Pedidos fechados';
     $this->orders = Order::all('Fechado');
   }

   public function edit() {
     $this->order = Order::findById($this->params[':id']);
     $this->authenticatedEmployee();

     $this->sellOrderItem = new SellOrderItem();
     $this->title = 'Pedido';
     $this->submit = "Adicionar";
     $this->action =  ViewHelpers::urlFor('/pedidos/produtos');
   }

   public function _new() {
      $this->order = new Order();
      $this->submit = 'Cadastrar';
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
       Flash::message('negative', 'Existem dados inválidos!');
       $this->orders = Order::all();
       $this->action = ViewHelpers::urlFor('/pedidos');
       $this->submit = 'Novo pedido';
       $this->render('new');
     }
   }

   public function destroy() {
     $order = Order::findById($this->params[':id']);
     $order->delete('orders');
     Flash::message('success', 'Pedido deletado com sucesso');
     $this->redirectTo("/pedidos");
   }


   public function closeOrder() {
     $this->order = Order::findById($this->params[':id']);

     if($this->order->changeStatusOrder()) {
       Flash::message('positive', 'Pedido fechado com sucesso!');
       $this->redirectTo("/pedidos");
     } else {
       Flash::message('negative', 'Erro no fechamento do pedido!');
     }
   }


} ?>
