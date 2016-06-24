<?php class ReportsController  extends ApplicationController {

  protected $beforeAction = array('authenticated' => 'all');

  public function employees() {
     $this->title = "Relatório dos funcionários que mais venderam";
     $this->action = ViewHelpers::urlFor("/relatorios/funcionarios");
     $this->reports = Report::employeeWhoDidMoreSales();
     $this->report = new Report();

     if($this->params) {
       $this->reports = Report::findByDateEmployee($this->params['report']['created_at'], $this->params['report']['closed_at']);

       if(sizeof($this->reports) == 0) {
         Flash::message('negative', 'Não foi encontrado nenhum relatório nessa data');
         $this->redirectTo("/relatorios/funcionarios");
       }
       $this->report->setCreatedAt($this->params['report']['created_at']);
       $this->report->setClosedAt($this->params['report']['closed_at']);
     }

     $this->graphicEmployee();
     $this->submit = "Pesquisar";
  }

  public function bestSellingProducts() {
     $this->title = "Relatório dos produtos mais vendidos";
     $this->reports = Report::bestSellingProducts();
     $this->report = new Report();

     $this->action = ViewHelpers::urlFor("/relatorios/produtos-mais-vendidos");

     if($this->params) {
       $this->reports = Report::findByDateBestSelling($this->params['report']['created_at'], $this->params['report']['closed_at']);
       $this->report->setCreatedAt($this->params['report']['created_at']);
       $this->report->setClosedAt($this->params['report']['closed_at']);
     }

     $this->graphicProduct();
     $this->submit = "Pesquisar";
  }

  public function leastSellingProducts() {
    $this->title = "Relatório dos produtos menos vendidos";
    $this->reports = Report::leastSellingProducts();
    $this->report = new Report();
    $this->action = ViewHelpers::urlFor("/relatorios/produtos-menos-vendidos");

    if($this->params) {
      $this->reports = Report::findByDateLeastSelling($this->params['report']['created_at'], $this->params['report']['closed_at']);
      $this->report->setCreatedAt($this->params['report']['created_at']);
      $this->report->setClosedAt($this->params['report']['closed_at']);
    }

    $this->graphicProduct();
    $this->submit = "Pesquisar";
  }

  public function graphicProduct() {
    $this->graphics = [['Produto', 'Quantidade']];

    foreach($this->reports as $report) {
      $this->graphics[] = [ $report->getProduct()->getName(),  $report->getAmount()];
    }
    $this->graphics = json_encode($this->graphics, JSON_NUMERIC_CHECK);

  }

  public function graphicEmployee() {
    $this->graphics = [['Funcionario', 'Quantidade']];

    foreach($this->reports as $report) {
      $this->graphics[] = [ $report->getEmployee()->getName(),  $report->getTotal()];
    }
    $this->graphics = json_encode($this->graphics, JSON_NUMERIC_CHECK);

  }

} ?>
