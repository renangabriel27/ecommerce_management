<?php class CategoriesController extends ApplicationController {

   protected $beforeAction = array('authenticated' => 'all');

   public function index() {
      $this->title = 'Listagem de categorias';
      $this->categories = Category::all();
   }

   public function show() {
     $this->category = Category::findById($this->params[':id']);
   }

   public function _new() {
      $this->category = new Category();
      $this->action = ViewHelpers::urlFor('/categorias');
      $this->submit = 'Nova Categoria';
   }

   public function create() {
      $this->category = new Category($this->params['category']);

      if($this->category->save()) {
        Flash::message('success', 'Categoria cadastrada com sucesso!');
        $this->redirectTo('/categorias');
      }
      else {
        Flash::message('negative', 'Existem dados inválidos!');
        $this->categories = Category::all();
        $this->action = ViewHelpers::urlFor('/categorias');
        $this->submit = 'Nova Categoria';
        $this->render('new');
      }
   }

   public function edit() {
     $this->categories = Category::all();
     $this->category = Category::findById($this->params[':id']);
     $this->submit = 'Salvar';
     $this->action = ViewHelpers::urlFor("/categorias/{$this->category->getId()}");
   }

   public function destroy() {
     $category = Category::findById($this->params[':id']);
     $category->delete();
     Flash::message('success', 'Categoria deletada com sucesso');
     $this->redirectTo("/categorias");
   }

   public function update() {
     $this->category = Category::findById($this->params[':id']);

     if ($this->category->update($this->params['category'])) {
       Flash::message('success', 'Registro atualizado com sucesso!');
       $this->redirectTo('/categorias');
     } else {
       Flash::message('negative', 'Existe dados incorretos no seu formulário!');
       $this->categories = Category::all();
       $this->action = ViewHelpers::urlFor("/categorias/{$this->category->getId()}");
       $this->submit = 'Atualizar';
       $this->render('edit');
     }
   }

   public function delete() {
      $db = Database::getConnection();
      $params = array('id' => $this->id);
      $sql = "DELETE FROM categories WHERE id = :id";
      $statement = $db->prepare($sql);
      return $statement->execute($params);
   }



} ?>
