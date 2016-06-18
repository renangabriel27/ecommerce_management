<?php class OrdersController extends ApplicationController {

   protected $beforeAction = array('authenticated' => 'all');

   public function index() {
      $this->title = 'Visualizar Pedidos';
      $this->orders = Order::all();
   }

   public function orderOpen() {
     $this->title = 'Pedidos em aberto';
     $this->orders = Order::allOpen();
   }

   public function orderClose() {
     $this->title = 'Pedidos fechados';
     $this->orders = Order::allClose();
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


   public function addOrderProduct() {
     $this->findByParams($this->params['order']['id'], $this->params['product']['id']);
     $this->orderIsClosed();
     $this->validateOrder();
   }

   public function validateOrder() {
     if($this->order->productIsValid($this->params['product']['name'])) {
       Flash::message('negative', 'Insira algum produto!');
       $this->redirectTo("/pedidos/{$this->order->getId()}");
     }
     if($this->order->uniqueItem($this->params['product']['id'])) {
       Flash::message('negative', 'Esse produto já está cadastrado no pedido!');
       $this->redirectTo("/pedidos/{$this->order->getId()}");
     } else {
       if($this->product->removeProductOfStock()) {
         $this->order->addProduct($this->params['product']['id']);
       } else {
         Flash::message('negative', 'Acabou o produto no estoque!');
       }
       $this->redirectTo("/pedidos/{$this->order->getId()}");
     }
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
