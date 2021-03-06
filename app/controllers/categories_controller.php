<?php class CategoriesController extends ApplicationController {

  protected $beforeAction = array('authenticated' => 'all');

  public function index() {
    $this->title = 'Categorias';
    $this->categories = Category::all();
  }

  public function show() {
    $this->title = "Visualizar categoria";
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
      $this->action = ViewHelpers::urlFor('/categorias');
      $this->submit = 'Nova Categoria';
      $this->render('new');
    }
  }

  public function edit() {
    $this->submit = 'Salvar';
    $this->category = Category::findById($this->params[':id']);
    $this->action = ViewHelpers::urlFor("/categorias/{$this->category->getId()}");
  }

  public function update() {
    $this->category = Category::findById($this->params[':id']);

    if ($this->category->update($this->params['category'])) {
      Flash::message('success', 'Registro atualizado com sucesso!');
      $this->redirectTo('/categorias');
    } else {
      Flash::message('negative', 'Existe dados incorretos no seu formulário!');
      $this->action = ViewHelpers::urlFor("/categorias/{$this->category->getId()}");
      $this->submit = 'Atualizar';
      $this->render('edit');
    }
  }

  public function destroy() {
    $this->category = Category::findById($this->params[':id']);
    if(!$this->category->isTableRelations('products','category_id')) {
      $this->category->delete('categories');
      Flash::message('success', 'Categoria deletada com sucesso');
    } else {
      Flash::message('negative', 'Categoria não pode ser deletada, pois está relacionada com outras tabelas');
    }
    $this->redirectTo("/categorias");
  }

} ?>
