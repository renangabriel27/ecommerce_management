<?php class ProductsController extends ApplicationController {

   protected $beforeAction = array('authenticated' => 'all');

   public function index() {
      $this->title = "Produtos";

      if(!isset($this->params['product'])) {
        $this->products = $this->pagination('Product', 'all', array('url' => '/produtos', 'limit' => 10));
        $this->linkToNew();
      } else {
        $this->search();
        $this->linkToBack();
      }
      $this->action = "/produtos/search";
   }

   public function show() {
     $this->title = "Visualizar produto";
     $this->product = Product::findById($this->params[':id']);
   }

   public function _new() {
      $this->product = new Product();
      $this->categories = Category::all();
      $this->submit = 'Novo produto';
      $this->action = ViewHelpers::urlFor("/produtos");
   }

   public function create() {
     $this->product = new Product($this->params['product']);

     if($this->product->save()) {
       Flash::message('success', 'Produto cadastrado com sucesso!');
       $this->redirectTo('/produtos');
     } else {
       Flash::message('negative', 'Existem dados inválidos!');
       $this->categories = Category::all();
       $this->submit = 'Novo produto';
       $this->action = ViewHelpers::urlFor('/produtos');
       $this->render('new');
    }
   }

   public function edit() {
     $this->product = Product::findById($this->params[':id']);
     $this->categories = Category::all();
     $this->submit = 'Salvar';
     $this->action = ViewHelpers::urlFor("/produtos/{$this->product->getId()}");
   }

   public function update() {
     $this->product = Product::findById($this->params[':id']);

     if ($this->product->update($this->params['product'])) {
       Flash::message('success', 'Registro atualizado com sucesso!');
       $this->redirectTo('/produtos');
     } else {
       Flash::message('negative', 'Existe dados incorretos no seu formulário!');
       $this->categories = Category::all();
       $this->action = ViewHelpers::urlFor("/produtos/{$this->product->getId()}");
       $this->submit = 'Atualizar';
       $this->render('edit');
     }
   }

   public function destroy() {
     $this->product = Product::findById($this->params[':id']);
     if(!$this->product->isTableRelations('sell_orders_items','product_id')) {
       $this->product->delete('products');
       Flash::message('success', 'Produto deletado com sucesso');
     } else {
       Flash::message('negative', 'Produto não pode ser deletado, pois está relacionada com outras tabelas');
     }
     $this->redirectTo("/produtos");
   }

   public function search() {
     $this->product = new Product();
     $this->products = $this->pagination('Product', 'productSearch', array('url' => '/produtos', 'limit' => 10, 'param' => $this->params['product']['name']));

     if($this->products) {
       Flash::message("success", "Produto(s) encontrado(s)!");
     } else {
       Flash::message("negative", "Nenhum produto encontrado!");
       ViewHelpers::redirectTo("/produtos");
     }
   }

   public function autoCompleteSearch() {
     $products = Product::whereNameLikeAsJson($this->params['query']);
     echo $products;
     exit();
   }

   private function linkToNew() {
     $this->urlButton = "/produtos/novo";
     $this->link = "Novo produto";
     $this->icon = 'class="add circle icon"';
   }

   private function linkToBack() {
     $this->urlButton = "/produtos";
     $this->link = "Voltar";
     $this->icon = 'class="reply icon"';
   }

} ?>
