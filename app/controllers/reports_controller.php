<?php class ReportsController  extends ApplicationController {

  protected $beforeAction = array('authenticated' => 'all');

  public function employees() {
     $this->title = "Relatório dos funcionários que mais venderam";
     $this->reports = Report::employeeWhoDidMoreSales();
     $this->create('findByDateEmployee', 'funcionarios');

     $this->graphicEmployee();
     $this->submit = "Pesquisar";
     $this->action = ViewHelpers::urlFor("/relatorios/funcionarios");
  }

  public function bestSellingProducts() {
     $this->title = "Relatório dos produtos mais vendidos";
     $this->reports = Report::bestSellingProducts();
     $this->create('findByDateBestSelling', 'produtos-mais-vendidos');

     $this->graphicProduct();
     $this->submit = "Pesquisar";
     $this->action = ViewHelpers::urlFor("/relatorios/produtos-mais-vendidos");
  }

  public function leastSellingProducts() {
    $this->title = "Relatório dos produtos menos vendidos";
    $this->reports = Report::leastSellingProducts();
    $this->create('findByDateLeastSelling', 'produtos-menos-vendidos');

    $this->graphicProduct();
    $this->submit = "Pesquisar";
    $this->action = ViewHelpers::urlFor("/relatorios/produtos-menos-vendidos");
  }

  private function create($method, $url) {
    $this->report = new Report();
    if($this->params && Report::dateIsValid($this->params['report']['created_at'], $this->params['report']['closed_at'])) {
      $this->reports = Report::$method($this->params['report']['created_at'], $this->params['report']['closed_at']);

      if(sizeof($this->reports) == 0) {
        Flash::message('negative', 'Não foi encontrado nenhum relatório nessa data');
        $this->redirectTo("/relatorios/{$url}");
      }
      $this->report->setCreatedAt($this->params['report']['created_at']);
      $this->report->setClosedAt($this->params['report']['closed_at']);
    } 
  }

  private function graphicProduct() {
    $this->graphics = [['Produto', 'Quantidade']];

    foreach($this->reports as $report) {
      $this->graphics[] = [ $report->getProduct()->getName(),  $report->getAmount()];
    }
    $this->graphics = json_encode($this->graphics, JSON_NUMERIC_CHECK);

  }

  private function graphicEmployee() {
    $this->graphics = [['Funcionario', 'Quantidade']];

    foreach($this->reports as $report) {
      $this->graphics[] = [ $report->getEmployee()->getName(),  $report->getTotal()];
    }
    $this->graphics = json_encode($this->graphics, JSON_NUMERIC_CHECK);

  }

} ?>
