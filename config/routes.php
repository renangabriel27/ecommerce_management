<?php
    require 'application.php';
    $router = new Router($_SERVER['REQUEST_URI']);

    $router->get('/', array('controller' => 'HomeController', 'action' => 'index'));

    /* Routes for employees
    ------------------------- */
    $router->get('/registre-se', array('controller' => 'EmployeesController', 'action' => '_new'));
    $router->post('/registre-se', array('controller' => 'EmployeesController', 'action' => 'create'));
    $router->get('/perfil', array('controller' => 'EmployeesController', 'action' => 'edit'));
    $router->post('/perfil', array('controller' => 'EmployeesController', 'action' => 'update'));
    /* End of routes for employees
    --------------------------------- */

    /* Routes for sessions
    ------------------------- */
    $router->get('/login', array('controller' => 'SessionsController', 'action' => '_new'));
    $router->post('/login', array('controller' => 'SessionsController', 'action' => 'create'));
    $router->get('/logout', array('controller' => 'SessionsController', 'action' => 'destroy'));
    /* End of routes for sessions
      --------------------------------- */

    /* Routes for categories
    ------------------------- */
    $router->get('/categorias', array('controller' => 'CategoriesController', 'action' => 'index'));
    $router->get('/categorias/page/:page', array('controller' => 'CategoriesController', 'action' => 'index'));
    $router->get('/categorias/novo', array('controller' => 'CategoriesController', 'action' => '_new'));
    $router->post('/categorias', array('controller' => 'CategoriesController', 'action' => 'create'));
    $router->get('/categorias/:id', array('controller' => 'CategoriesController', 'action' => 'show'));
    $router->get('/categorias/:id/editar', array('controller' => 'CategoriesController', 'action' => 'edit'));
    $router->post('/categorias/:id', array('controller' => 'CategoriesController', 'action' => 'update'));
    $router->get('/categorias/:id/deletar', array('controller' => 'CategoriesController', 'action' => 'destroy'));

    /* End of routes for categories
    --------------------------------- */

    /* Routes for orders
    ------------------------- */
    $router->get('/pedidos', array('controller' => 'OrdersController', 'action' => 'index'));
    $router->post('/pedidos', array('controller' => 'OrdersController', 'action' => 'create'));
    $router->get('/pedidos/novo', array('controller' => 'OrdersController', 'action' => '_new'));
    $router->post('/pedidos/produtos', array('controller' => 'OrdersController', 'action' => 'addOrderProduct'));
    $router->get('/pedidos/abertos', array('controller' => 'OrdersController', 'action' => 'orderOpen'));
    $router->get('/pedidos/fechados', array('controller' => 'OrdersController', 'action' => 'orderClose'));
    $router->get('/pedidos/:id', array('controller' => 'OrdersController', 'action' => 'edit'));
    $router->get('/pedidos/:id/deletar', array('controller' => 'OrdersController', 'action' => 'destroy'));
    $router->get('/pedidos/:id/fechar', array('controller' => 'OrdersController', 'action' => 'closeOrder'));
    /* End of routes for orders
    --------------------------------- */

    /* Routes for sell orders items
    ------------------------- */
    $router->get('/pedidos/:id/produtos/:product_id/adicionar', array('controller' => 'SellOrdersController', 'action' => 'addAmountProduct'));
    $router->get('/pedidos/:id/produtos/:product_id/deletar', array('controller' => 'SellOrdersController', 'action' => 'destroyProduct'));
    $router->get('/pedidos/:id/produtos/:product_id/remover', array('controller' => 'SellOrdersController', 'action' => 'removeAmountProduct'));
    /* End of routes for sell orders items
    --------------------------------- */


    /* Routes for products
    ------------------------- */
    $router->get('/produtos/autocomplete-search', array('controller' => 'ProductsController', 'action' => 'autoCompleteSearch'));
    $router->get('/produtos/autocomplete-search-id', array('controller' => 'ProductsController', 'action' => 'autoCompleteSearchId'));
    $router->get('/produtos/search', array('controller' => 'ProductsController', 'action' => 'index'));

    $router->get('/produtos', array('controller' => 'ProductsController', 'action' => 'index'));
    $router->get('/produtos/page/:page', array('controller' => 'ProductsController', 'action' => 'index'));
    $router->get('/produtos/novo', array('controller' => 'ProductsController', 'action' => '_new'));
    $router->post('/produtos', array('controller' => 'ProductsController', 'action' => 'create'));
    $router->get('/produtos/:id', array('controller' => 'ProductsController', 'action' => 'show'));
    $router->get('/produtos/:id/editar', array('controller' => 'ProductsController', 'action' => 'edit'));
    $router->post('/produtos/:id', array('controller' => 'ProductsController', 'action' => 'update'));
    $router->get('/produtos/:id/deletar', array('controller' => 'ProductsController', 'action' => 'destroy'));

    /* End of routes for products
    --------------------------------- */

    /* Routes for clients - person individual
    ------------------------- */
    $router->get('/clientes/autocomplete-search', array('controller' => 'ClientsPiController', 'action' => 'autoCompleteSearch'));
    $router->get('/clientes', array('controller' => 'ClientsPiController', 'action' => 'clients'));

    $router->get('/clientes/pessoa-fisica', array('controller' => 'ClientsPiController', 'action' => 'index'));
    $router->get('/clientes/nova-pessoa-fisica', array('controller' => 'ClientsPiController', 'action' => '_new'));
    $router->post('/clientes/pessoa-fisica', array('controller' => 'ClientsPiController', 'action' => 'create'));
    $router->get('/clientes/:id/pessoa-fisica', array('controller' => 'ClientsPiController', 'action' => 'show'));
    $router->get('/clientes/:id/editar-pessoa-fisica', array('controller' => 'ClientsPiController', 'action' => 'edit'));
    $router->post('/clientes/:id/pessoa-fisica', array('controller' => 'ClientsPiController', 'action' => 'update'));
    $router->get('/clientes/:id/deletar-pessoa-fisica', array('controller' => 'ClientsPiController', 'action' => 'destroy'));

    /* End of routes for clients - person individual
    --------------------------------- */

    /* Routes for clients - person corporate
    ------------------------- */

    $router->get('/clientes/pessoa-juridica', array('controller' => 'ClientsPcController', 'action' => 'index'));
    $router->get('/clientes/nova-pessoa-juridica', array('controller' => 'ClientsPcController', 'action' => '_new'));
    $router->post('/clientes/pessoa-juridica', array('controller' => 'ClientsPcController', 'action' => 'create'));
    $router->get('/clientes/:id/pessoa-juridica', array('controller' => 'ClientsPcController', 'action' => 'show'));
    $router->get('/clientes/:id/editar-pessoa-juridica', array('controller' => 'ClientsPcController', 'action' => 'edit'));
    $router->post('/clientes/:id/pessoa-juridica', array('controller' => 'ClientsPcController', 'action' => 'update'));
    $router->get('/clientes/:id/deletar-pessoa-juridica', array('controller' => 'ClientsPcController', 'action' => 'destroy'));

    /* End of routes for clients - person corporate
    --------------------------------- */

    /* Routes for reports
    ------------------------- */
    $router->get('/relatorios/funcionarios', array('controller' => 'ReportsController', 'action' => 'employees'));
    $router->get('/relatorios/produtos-mais-vendidos', array('controller' => 'ReportsController', 'action' => 'bestSellingProducts'));
    $router->get('/relatorios/produtos-menos-vendidos', array('controller' => 'ReportsController', 'action' => 'leastSellingProducts'));
    /* End of routes for reports
    --------------------------------- */

    $router->load();
?>
