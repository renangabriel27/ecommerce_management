<?php class ProductsController extends ApplicationController {

   protected $beforeAction = array('authenticated' => 'all');

   public function index() {
      $this->products = $this->pagination('Product', array('url' => '/produtos', 'limit' => 10));
   }

   public function show() {
     $this->product = Product::findById($this->params[':id']);
   }

   public function _new() {
      $this->categories =  Category::all();
      $this->product = new Product();
      $this->submit = 'Novo produto';
      $this->action = ViewHelpers::urlFor("/produtos");
   }

   public function create() {
     $this->product = new Product($this->params['product']);

     if($this->product->save()) {
       Flash::message('success', 'Produto cadastrado com sucesso!');
       $this->redirectTo('/produtos');
     }
     else {
       Flash::message('danger', 'Existem dados inválidos!');
       $this->categories = Category::all();
       $this->submit = 'Novo pedido';
       $this->action = ViewHelpers::urlFor('/produtos');
       $this->render('new');
     }
   }

   public function edit() {
     $this->categories = Category::all();
     $this->product = Product::findById($this->params[':id']);
     $this->submit = 'Salvar';
     $this->action = ViewHelpers::urlFor("/produtos/{$this->product->getId()}");
   }

   public function update() {
     $this->product = Product::findById($this->params[':id']);

     if ($this->product->update($this->params['product'])) {
       Flash::message('success', 'Registro atualizado com sucesso!');
       $this->redirectTo('/produtos');
     } else {
       Flash::message('danger', 'Existe dados incorretos no seu formulário!');
       $this->categories = Category::all();
       $this->action = ViewHelpers::urlFor("/produtos/{$this->product->getId()}");
       $this->submit = 'Atualizar';
       $this->render('edit');
     }
   }

   public function destroy() {
     $product = Product::findById($this->params[':id']);
     $product->delete();
     Flash::message('success', 'Produto deletado com sucesso');
     $this->redirectTo("/produtos");
   }

   public function autoCompleteSearch() {
     $products = Product::whereNameLikeAsJson($this->params['query']);
     echo $products;
     exit();
   }



} ?>
