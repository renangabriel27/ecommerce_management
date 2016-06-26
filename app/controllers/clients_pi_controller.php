<?php class ClientsPiController  extends ApplicationController {

  protected $beforeAction = array('authenticated' => 'all');

  public function index() {
    $this->title = "Clientes (Pessoa física)";
    $this->clients = ClientPi::all();
  }

  public function clients() {
    $this->title = "Clientes";
    $this->clients = Client::all();
  }

  public function show() {
    $this->title = "Visualizar cliente";
    $this->client = ClientPi::findById($this->params[':id']);
  }

  public function _new() {
    $this->title ="Cadastro de cliente";
    $this->cities = City::all();
    $this->client  = new ClientPi();
    $this->action = ViewHelpers::urlFor("/clientes/nova-pessoa-fisica");
    $this->submit = 'Cadastrar';
  }

  public function create(){
    $this->client = new ClientPi($this->params['client']);

    if ($this->client->save()) {
      Flash::message('success', 'Cliente realizado com sucesso!');
      $this->redirectTo('/clientes/pessoa-fisica');
    } else {
      Flash::message('negative', 'Existe dados incorretos no seu formulário!');
      $this->title ="Cadastro de cliente";
      $this->submit = 'Cadastrar';
      $this->cities = City::all();
      $this->action = ViewHelpers::urlFor("/clientes/nova-pessoa-fisica");
      $this->render('new');
    }
  }

  public function edit() {
    $this->title = "Editar cliente";
    $this->client = ClientPi::findById($this->params[':id']);
    $this->cities = City::all();

    $this->submit = 'Salvar';
    $this->action = ViewHelpers::urlFor("/clientes/{$this->client->getId()}/pessoa-fisica");
  }

  public function update() {
     $this->client = ClientPi::findById($this->params[':id']);

    if($this->client->update($this->params['client'])) {
      Flash::message('success', 'Registro atualizado com sucesso!');
      $this->redirectTo('/clientes/pessoa-fisica');
    } else {
      Flash::message('negative', 'Existe dados incorretos no seu formulário!');
      $this->title = "Editar cliente";
      $this->submit = 'Salvar';
      $this->cities = City::all();
      $this->action = ViewHelpers::urlFor("/clientes/{$this->client->getId()}/pessoa-fisica");
      $this->render('edit');
    }
  }

  public function destroy() {
    $this->client = ClientPi::findById($this->params[':id']);
    if(!$this->client->isTableRelations('orders','client_id')) {
      $this->client->deleteClient($this->params[':id']);
      Flash::message('success', 'Cliente deletado com sucesso');
    } else {
      Flash::message('negative', 'Cliente não pode ser deletado, pois está relacionado com outras tabelas');
    }
    $this->redirectTo("/clientes/pessoa-fisica");
  }

  public function autoCompleteSearch() {
    $this->clients = Client::whereNameLikeAsJson($this->params['query']);
    echo $this->clients;
    exit();
  }

} ?>
