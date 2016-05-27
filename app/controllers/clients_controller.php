<?php class ClientsController  extends ApplicationController {

  protected $beforeAction = array('authenticated' => array('edit', 'update'));

  public function index() {
     $this->title = 'Sistema';
     $this->clients = Client::all();
  }

  public function _new() {
    $this->type = $this->params[':type'];
    if($this->type != 1 && $this->type != 2) $this->redirectTo('/clientes');

    $this->client = $this->type == 1 ? new ClientPi() : new ClientPc();
    $this->cities = City::all();
    $this->action = ViewHelpers::urlFor('/clientes/novo');
    $this->submit = 'Cadastrar';
  }

  public function create(){
    $client = $this->params['client'];
    $this->type = $this->params['client']['type'];
    $this->client = ($this->type == 1) ? new ClientPi($client) : new ClientPc($client);

    if ($this->client->save()) {
      Flash::message('success', 'Cliente realizado com sucesso!');
      $this->redirectTo('/clientes');
    } else {
      Flash::message('negative', 'Existe dados incorretos no seu formulário!');
      $this->cities = City::all();
      $this->action = ViewHelpers::urlFor("/clientes/novo");
      $this->submit = 'Cadastrar';
      $this->render('new');
    }
  }

  public function edit() {
    $this->cities = City::all();
    $client = Client::findById($this->params[':id']);
    $this->type = $client->getType();
    $this->client = ($this->type == 1) ? ClientPi::findById($this->params[':id']) : ClientPc::findById($this->params[':id']);
    $this->submit = 'Salvar';
    $this->action = ViewHelpers::urlFor("/clientes/{$this->client->getClientId()}");
  }

  public function update() {
     $this->type = $this->params['client']['type'];
     $this->client = Client::findById($this->params[':id']);

    if($this->client->update($this->params['client'])) {
      Flash::message('success', 'Registro atualizado com sucesso!');
      $this->redirectTo('/clientes');
    } else {
      Flash::message('negative', 'Existe dados incorretos no seu formulário!');
      $this->action = ViewHelpers::urlFor("/clientes/{$this->client->getId()}/editar");
      $city = City::findById($this->client->getCityId());
      $this->submit = 'Salvar';
      $this->render('edit');
    }
  }

  public function autoCompleteSearch() {
    $this->clients = Client::whereNameLikeAsJson($this->params['query']);
    echo $this->clients;
    exit();
  }

  public function destroy() {

  }

} ?>
