<?php class ProductsController extends ApplicationController {

   protected $beforeAction = array('authenticated' => 'all');

   public function index() {
      $this->title = "Produtos";
      $this->existParams();
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
     $product = Product::findById($this->params[':id']);
     $product->delete('products');
     Flash::message('success', 'Produto deletado com sucesso');
     $this->redirectTo("/produtos");
   }

   public function search() {
     $this->product = new Product();
     $this->products = $this->product->productSearch($this->params['product']['name']);

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

   public function autoCompleteSearchId() {
     $products = Product::whereIdLikeAsJson($this->params['query']);
     echo $products;
     exit();
   }

   public function existParams() {
     if(!$this->params) {
       $this->products = Product::all();
       $this->linkToNew();
     } else {
       $this->search();
       $this->linkToBack();
     }
   }

   public function linkToNew() {
     $this->url = "/produtos/novo";
     $this->link = "Novo produto";
     $this->icon = 'class="add circle icon"';
   }

   public function linkToBack() {
     $this->url = "/produtos";
     $this->link = "Voltar";
     $this->icon = 'class="reply icon"';
   }

} ?>
