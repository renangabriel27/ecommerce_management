<?php class SellOrdersItemsController extends ApplicationController {

   protected $beforeAction = array('authenticated' => 'all', 'authenticatedEmployee' => 'all', 'createObjects' => 'all');

   public function addProduct() {
     $this->validateProduct();

     if($this->product->removeProductOfStock()) {
       $this->sellOrderItem->save($this->params['product']['id'], $this->params['order']['id']);
     } else {
       Flash::message('negative', 'Acabou o produto no estoque!');
     }
     $this->redirectTo("/pedidos/{$this->order->getId()}");
   }

   public function addAmount() {
     if($this->product->removeProductOfStock()) {
       $this->sellOrderItem->addAmountProduct($this->product);
     } else {
       Flash::message('negative', 'Acabou o produto no estoque!');
     }
     $this->redirectTo("/pedidos/{$this->order->getId()}");
   }

   public function removeAmount() {
     if($this->sellOrderItem->removeProduct()) {
       $this->product->addProductOnStock();
     }
     $this->redirectTo("/pedidos/{$this->order->getId()}");
   }

   public function destroy() {
     $amount = $this->sellOrderItem->getAmountOfProduct($this->params[':product_id'], $this->params[':id']);
     $this->product->restoreProductOnStock($amount);

     $this->sellOrderItem->delete('sell_orders_items');
     Flash::message('success', 'Produto deletado com sucesso do pedido');

     $this->redirectTo("/pedidos/{$this->order->getId()}");
   }

} ?>
