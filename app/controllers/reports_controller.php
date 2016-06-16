<?php class ReportsController  extends ApplicationController {

  protected $beforeAction = array('authenticated' => 'all');

  public function indexEmployees() {
     $this->title = "Relatório dos funcionários que mais venderam";
     $this->reports = Report::employeeWhoDidMoreSales();
  }

  public function indexBestSellingProducts() {
     $this->title = "Relatório dos produtos mais vendidos";
     $this->reports = Report::bestSellingProducts();
  }

  public function indexLeastSellingProducts() {
    $this->title = "Relatório dos produtos menos vendidos";
    $this->reports = Report::leastSellingProducts();
  }

} ?>
