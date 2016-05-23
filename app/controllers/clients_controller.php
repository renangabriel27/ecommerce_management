<?php class ClientsController  extends ApplicationController {

  protected $beforeAction = array('authenticated' => array('edit', 'update'));

  public function index() {
     $this->title = 'Sistema';
     $this->clients = Client::all();
  }

  public function _new() {
    $this->client = new Client();
  }

  public function create(){
    $this->client = new Client($this->params['user']);

    if ($this->client->save()) {
      Flash::message('success', 'Cliente realizado com sucesso!');
      $this->redirectTo('/login');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->action = '/registre-se';
      $this->submit = 'Cadastre-se';
      $this->render('new');
    }
  }

  public function edit() {
    $this->client = $this->currentUser();
    $this->action = '/perfil';
    $this->submit = 'Atualizar';
  }

  public function update() {
    $this->client = $this->currentUser();

    if ($this->client->update($this->params['user'])) {
      Flash::message('success', 'Registro atualizado com sucesso!');
      $this->redirectTo('/');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->action = '/perfil';
      $this->submit = 'Atualizar';
      $this->render('edit');
    }
  }

  public function autoCompleteSearch() {
    $this->clients = Client::whereNameLikeAsJson($this->params['query']);
    echo $this->clients;
    exit();
  }

} ?>
