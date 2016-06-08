<?php class OrdersController extends ApplicationController {

   protected $beforeAction = array('authenticated' => 'all');

   public function index() {
      $this->title = 'Listagem de pedidos';
      $this->orders = Order::all();
   }

   public function show() {
     $this->order = Order::findById($this->params[':id']);
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

   public function destroyProduct() {
     $this->order = Order::findById($this->params[':id']);
     $sellOrderItem = SellOrderItem::findById($this->params[':product_id'], $this->params[':id']);
     $sellOrderItem->delete('sell_orders_items');
     Flash::message('success', 'Produto deletado com sucesso do pedido');
     $this->redirectTo("/pedidos/{$this->order->getId()}");
   }

   public function addOrderProduct() {
     $this->order = Order::findById($this->params['order']['id']);
     $this->sellOrderItem = SellOrderItem::findById($this->params['product']['id'], $this->params['order']['id']);

     if($this->order->uniqueItem($this->params['product']['id'])) {
      Flash::message('negative', 'Esse produto já está cadastrado no pedido!');
      $this->redirectTo("/pedidos/{$this->order->getId()}");
    } else {
     $this->order->addProduct($this->params['product']['id']);
     $this->redirectTo("/pedidos/{$this->order->getId()}");
    }
   }

   public function addAmountProduct() {
     $orderId = $this->params[':id'];
     $this->order = Order::findById($orderId);
     $this->product = $this->params[':product_id'];
     SellOrderItem::addProduct($this->product, $orderId);
     $this->redirectTo("/pedidos/{$this->order->getId()}");
   }

   public function removeAmountProduct() {
     $orderId = $this->params[':id'];
     $this->order = Order::findById($orderId);
     $this->product = $this->params[':product_id'];
     SellOrderItem::removeProduct($this->product, $orderId);
     $this->redirectTo("/pedidos/{$this->order->getId()}");
   }

   public function closeOrder() {
     $this->order = Order::findById($this->params[':id']);
     if($this->order->changeStatusOrder($this->params[':id'])) {
       Flash::message('positive', 'Pedido fechado com sucesso!');
       $this->redirectTo("/pedidos");
     } else {
       Flash::message('negative', 'Erro no fechamento do pedido!');
     }

   }

} ?>
