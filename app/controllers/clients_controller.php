<?php class ClientsController  extends ApplicationController {

  protected $beforeAction = array('authenticated' => 'all');

  public function index() {
     $this->title = 'Sistema';
     $this->clients = Client::all();
  }

  public function show() {
    $this->client = ClientPi::findById(':id');
  }

  public function _new() {
    $this->type = $this->params[':type'];
    $this->client = $this->type == 1 ? new ClientPi(): new ClientPc() ;
    $this->cities = City::all();
    $this->action = ViewHelpers::urlFor("/clientes");
    $this->submit = 'Cadastrar';
  }

  public function create(){
    $client = $this->params['client'];
    $this->type = $this->params['client']['type'];
    $this->client = $this->type == 1 ? new ClientPi($client): new ClientPc($client);

    if ($this->client->save()) {
      Flash::message('success', 'Cliente realizado com sucesso!');
      $this->redirectTo('/clientes');
    } else {
      Flash::message('negative', 'Existe dados incorretos no seu formulário!');
      $this->cities = City::all();
      $this->action = ViewHelpers::urlFor("/clientes");
      $this->submit = 'Cadastrar';
      $this->render('new');
    }
  }

  public function edit() {
    $client = $this->params[':id'];
    $this->type = $this->params[':type'];
    $this->client = $this->type == 1 ? ClientPi::findById($client) : ClientPc::findById($client);
    $this->cities = City::all();
    $this->submit = 'Salvar';
    $this->action = ViewHelpers::urlFor("/clientes/{$this->client->getId()}");
  }

  public function update() {
     $client = $this->params[':id'];
     $this->client = $this->type == 1 ? ClientPi::findById($client) : ClientPc::findById($client);
     $this->cities = City::all();
     $city = City::findById($this->client->getCityId());
     $this->submit = 'Salvar';
     $this->action = ViewHelpers::urlFor("/clientes/{$this->client->getId()}");

    if($this->client->update($this->params['client'])) {
      Flash::message('success', 'Registro atualizado com sucesso!');
      $this->redirectTo('/clientes');
    } else {
      Flash::message('negative', 'Existe dados incorretos no seu formulário!');
      $this->render('edit');
    }
  }

  public function destroy() {
    $client = ClientPi::findById($this->params[':id']);
    $client->deleteClient();
    Flash::message('success', 'Cliente deletado com sucesso');
    $this->redirectTo("/clientes");
  }

  public function autoCompleteSearch() {
    $this->clients = Client::whereNameLikeAsJson($this->params['query']);
    echo $this->clients;
    exit();
  }

} ?>
