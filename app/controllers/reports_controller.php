<?php class ReportsController  extends ApplicationController {

  protected $beforeAction = array('authenticated' => 'all');

  public function employees() {
     $this->title = "Relatório dos funcionários que mais venderam";
     $this->reports = Report::employeeWhoDidMoreSales();
     $this->action = ViewHelpers::urlFor("/relatorios/funcionarios");


     if($this->params) {
       $this->reports = Report::findByDateEmployee($this->params['report']['created_at'], $this->params['report']['closed_at']);
     }

     $this->submit = "Pesquisar";
  }

  public function bestSellingProducts() {
     $this->title = "Relatório dos produtos mais vendidos";
     $this->reports = Report::bestSellingProducts();
     $this->action = ViewHelpers::urlFor("/relatorios/produtos-mais-vendidos");

     if($this->params) {
       $this->reports = Report::findByDateBestSelling($this->params['report']['created_at'], $this->params['report']['closed_at']);
     }
     $this->submit = "Pesquisar";
  }

  public function leastSellingProducts() {
    $this->title = "Relatório dos produtos menos vendidos";
    $this->reports = Report::leastSellingProducts();
    $this->action = ViewHelpers::urlFor("/relatorios/produtos-menos-vendidos");

    if($this->params) {
      $this->reports = Report::findByDateLeastSelling($this->params['report']['created_at'], $this->params['report']['closed_at']);
    }

    $this->submit = "Pesquisar";
  }

} ?>
