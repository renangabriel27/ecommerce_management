<?php class ClientsController  extends ApplicationController {

  protected $beforeAction = array('authenticated' => 'all');

  public function index() {
     $this->title = 'Sistema';
     $this->clients = Client::all();
  }

  public function show() {
    $this->title = "Visualizar cliente";
    $this->client = Client::findById($this->params[':id']);
    $this->type = $this->client->getType();
    $this->cities = City::all();
  }

  public function _new() {
    $this->title ="Cadastro de cliente";
    $this->type = $this->params[':type'];
    $this->client = $this->newClientType($this->type);
    $this->cities = City::all();
    $this->action = ViewHelpers::urlFor("/clientes");
    $this->submit = 'Cadastrar';
  }

  public function create(){
    $client = $this->params['client'];
    $this->type = $this->params['client']['type'];
    $this->client = $this->newClientType($this->type, $client);

    if ($this->client->save()) {
      Flash::message('success', 'Cliente realizado com sucesso!');
      $this->redirectTo('/clientes');
    } else {
      Flash::message('negative', 'Existe dados incorretos no seu formulário!');
      $this->title ="Cadastro de cliente";
      $this->submit = 'Cadastrar';
      $this->cities = City::all();
      $this->action = ViewHelpers::urlFor("/clientes");
      $this->render('new');
    }
  }

  public function edit() {
    $this->title = "Editar cliente";
    $client = $this->params[':id'];
    $this->client = Client::findById($client);
    $this->type = $this->client->getType();
    $this->cities = City::all();

    $this->submit = 'Salvar';
    $this->action = ViewHelpers::urlFor("/clientes/{$this->client->getClientId()}");
  }

  public function update() {
     $clientId = $this->params[':id'];
     $this->client = Client::findById($clientId);
     $this->type = $this->client->getType();

    if($this->client->update($this->params['client'])) {
      Flash::message('success', 'Registro atualizado com sucesso!');
      $this->redirectTo('/clientes');
    } else {
      Flash::message('negative', 'Existe dados incorretos no seu formulário!');
      $this->title = "Editar cliente";
      $this->submit = 'Salvar';
      $this->cities = City::all();
      $this->action = ViewHelpers::urlFor("/clientes/{$this->client->getClientId()}");
      $this->render('edit');
    }
  }

  public function destroy() {
    $this->client = Client::findById($this->params[':id']);
    $this->type = $this->client->getType();
    $this->client->deleteClient($this->type, $this->client->getClientId());
    Flash::message('success', 'Cliente deletado com sucesso');
    $this->redirectTo("/clientes");
  }

  public function autoCompleteSearch() {
    $this->clients = Client::whereNameLikeAsJson($this->params['query']);
    echo $this->clients;
    exit();
  }

} ?>
