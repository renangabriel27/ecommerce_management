<?php class ClientsController  extends ApplicationController {

  protected $beforeAction = array('authenticated' => array('edit', 'update'));

  public function index() {
     $this->title = 'Sistema';
     $this->clients = Client::all();
  }

  public function _new() {
    $this->type = $this->params[':type'];
    if($this->type != 1 && $this->type != 2){
			$this->redirectTo('/clientes');
		}
    $this->client = ($this->type == 1) ? new ClientPi() : new ClientPc();
    $this->cities = City::all();
    $this->action = ViewHelpers::urlFor('/clientes');
    $this->submit = 'Cadastrar';
  }

  public function create(){
    $client = $this->params['client'];
    $this->type = $this->params['client']['type'];

    $this->cities = City::all();
    $this->client = ($this->type == 1) ? new ClientPi($client) : new ClientPc($client);
    $this->action = ViewHelpers::urlFor("/clientes/novo");

    if ($this->client->save()) {
      Flash::message('success', 'Cliente realizado com sucesso!');
      $this->redirectTo('/clientes');
    } else {
      Flash::message('negative', 'Existe dados incorretos no seu formulário!');
      $this->submit = 'Cadastrar';
      $this->render('new');
    }
  }

  public function edit() {
    $this->client = Client::findById($this->params[':id']);
    $this->type = $this->client->getType();
    $this->cities = City::all();
    $this->submit = 'Salvar';
    $this->action = ViewHelpers::urlFor("/clientes/{$this->client->getId()}");
  }

  public function update() {
     $this->client = Client::findById($this->params[':id']);
     $this->type = $this->client->getType();
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

  public function autoCompleteSearch() {
    $this->clients = Client::whereNameLikeAsJson($this->params['query']);
    echo $this->clients;
    exit();
  }

  public function destroy() {
    $client = Client::findById($this->params[':id']);
    $client->delete();
    Flash::message('success', 'Cliente deletado com sucesso');
    $this->redirectTo("/clientes");
  }


} ?>
