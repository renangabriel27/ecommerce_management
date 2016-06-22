<?php class ClientsPcController  extends ApplicationController {

  protected $beforeAction = array('authenticated' => 'all');

  public function index() {
     $this->title = "Clientes (Pessoa jurídica)";
     $this->clients = $this->pagination('ClientPc', array('url' => '/clientes/pessoa-juridica', 'limit' => 10));
  }

  public function show() {
    $this->title = "Visualizar cliente";
    $this->client = ClientPc::findById($this->params[':id']);
  }

  public function _new() {
    $this->title ="Cadastro de cliente";
    $this->cities = City::all();
    $this->client  = new ClientPc();
    $this->action = ViewHelpers::urlFor("/clientes/pessoa-juridica");
    $this->submit = 'Cadastrar';
    $this->render('new');
  }

  public function create(){
    $this->client = new ClientPc($this->params['client']);

    if ($this->client->save()) {
      Flash::message('success', 'Cliente realizado com sucesso!');
      $this->redirectTo('/clientes/pessoa-juridica');
    } else {
      Flash::message('negative', 'Existe dados incorretos no seu formulário!');
      $this->title ="Cadastro de cliente";
      $this->submit = 'Cadastrar';
      $this->cities = City::all();
      $this->action = ViewHelpers::urlFor("/clientes/pessoa-juridica");
      $this->render('new');
    }
  }

  public function edit() {
    $this->title = "Editar cliente";
    $this->client = ClientPc::findById($this->params[':id']);
    $this->cities = City::all();

    $this->submit = 'Salvar';
    $this->action = ViewHelpers::urlFor("/clientes/{$this->client->getId()}/pessoa-juridica");
  }

  public function update() {
     $this->client = ClientPc::findById($this->params[':id']);

    if($this->client->update($this->params['client'])) {
      Flash::message('success', 'Registro atualizado com sucesso!');
      $this->redirectTo('/clientes/pessoa-juridica');
    } else {
      Flash::message('negative', 'Existe dados incorretos no seu formulário!');
      $this->title = "Editar cliente";
      $this->submit = 'Salvar';
      $this->cities = City::all();
      $this->action = ViewHelpers::urlFor("/clientes/{$this->client->getId()}/pessoa-juridica");
      $this->render('edit');
    }
  }

  public function destroy() {
    $this->client = ClientPc::findById($this->params[':id']);
    $this->client->deleteClient($this->params[':id']);
    Flash::message('success', 'Cliente deletado com sucesso');
    $this->redirectTo("/clientes/pessoa-juridica");
  }

} ?>
