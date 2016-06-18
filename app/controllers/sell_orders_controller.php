<?php class SellOrdersController extends ApplicationController {

   protected $beforeAction = array('authenticated' => 'all');

   public function createObjects() {
     $this->findByParams($this->params[':id'], $this->params[':product_id']);
     $this->orderIsClosed();
   }

   public function addAmountProduct() {
     $this->createObjects();

     if($this->product->removeProductOfStock()) {
       $this->sellOrderItem->addProduct();
     } else {
       Flash::message('negative', 'Acabou o produto no estoque!');
     }
     $this->redirectTo("/pedidos/{$this->order->getId()}");
   }

   public function removeAmountProduct() {
     $this->createObjects();

     if($this->sellOrderItem->removeProduct()) {
       $this->product->addProductOnStock();
     }
     $this->redirectTo("/pedidos/{$this->order->getId()}");
   }

   public function destroyProduct() {
     $this->createObjects();

     $amount = $this->sellOrderItem->getAmountOfProduct($this->params[':product_id'], $this->params[':id']);
     $this->product->restoreProductOnStock($amount);

     $this->sellOrderItem->delete('sell_orders_items');
     Flash::message('success', 'Produto deletado com sucesso do pedido');

     $this->redirectTo("/pedidos/{$this->order->getId()}");
   }

} ?>
