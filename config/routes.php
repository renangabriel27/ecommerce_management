<?php
    require 'application.php';
    $router = new Router($_SERVER['REQUEST_URI']);

    $router->get('/', array('controller' => 'HomeController', 'action' => 'index'));

    /* Rotas para os funcionarios
    ------------------------- */
    $router->get('/registre-se', array('controller' => 'EmployeesController', 'action' => '_new'));
    $router->post('/registre-se', array('controller' => 'EmployeesController', 'action' => 'create'));
    $router->get('/perfil', array('controller' => 'EmployeesController', 'action' => 'edit'));
    $router->post('/perfil', array('controller' => 'EmployeesController', 'action' => 'update'));
    /* Fim das rotas para os funcionários
    --------------------------------- */

    /* Rotas para os sessões
    ------------------------- */
    $router->get('/login', array('controller' => 'SessionsController', 'action' => '_new'));
    $router->post('/login', array('controller' => 'SessionsController', 'action' => 'create'));
    $router->get('/logout', array('controller' => 'SessionsController', 'action' => 'destroy'));
    /* Fim das rotas para os sessões
      --------------------------------- */

    /* Rotas para as categorias
    ------------------------- */
    $router->get('/categorias', array('controller' => 'CategoriesController', 'action' => 'index'));
    $router->get('/categorias/page/:page', array('controller' => 'CategoriesController', 'action' => 'index'));
    $router->get('/categorias/novo', array('controller' => 'CategoriesController', 'action' => '_new'));
    $router->post('/categorias', array('controller' => 'CategoriesController', 'action' => 'create'));
    $router->get('/categorias/:id', array('controller' => 'CategoriesController', 'action' => 'show'));
    $router->get('/categorias/:id/editar', array('controller' => 'CategoriesController', 'action' => 'edit'));
    $router->post('/categorias/:id', array('controller' => 'CategoriesController', 'action' => 'update'));
    $router->get('/categorias/:id/deletar', array('controller' => 'CategoriesController', 'action' => 'destroy'));

    /* Fim das rotas para os categorias
    --------------------------------- */

    /* Rotas para os pedidos
    ------------------------- */
    $router->get('/pedidos', array('controller' => 'OrdersController', 'action' => 'index'));
    $router->post('/pedidos', array('controller' => 'OrdersController', 'action' => 'create'));
    $router->get('/pedidos/novo', array('controller' => 'OrdersController', 'action' => '_new'));
    $router->post('/pedidos/produtos', array('controller' => 'OrdersController', 'action' => 'addOrderProduct'));
    $router->get('/pedidos/:id', array('controller' => 'OrdersController', 'action' => 'show'));
    $router->get('/pedidos/:id/deletar', array('controller' => 'OrdersController', 'action' => 'destroy'));
    $router->get('/pedidos/:id/fechar', array('controller' => 'OrdersController', 'action' => 'closeOrder'));
    $router->get('/pedidos/:id/produtos/:product_id/deletar', array('controller' => 'OrdersController', 'action' => 'destroyProduct'));
    $router->get('/pedidos/:id/produtos/:product_id/adicionar', array('controller' => 'OrdersController', 'action' => 'addAmountProduct'));
    $router->get('/pedidos/:id/produtos/:product_id/remover', array('controller' => 'OrdersController', 'action' => 'removeAmountProduct'));

    /* Fim das rotas para os pedidos
    --------------------------------- */

    /* Rotas para os produtos
    ------------------------- */
    $router->get('/produtos/autocomplete-search', array('controller' => 'ProductsController', 'action' => 'autoCompleteSearch'));
    $router->get('/produtos/autocomplete-search-id', array('controller' => 'ProductsController', 'action' => 'autoCompleteSearchId'));

    $router->get('/produtos', array('controller' => 'ProductsController', 'action' => 'index'));
    $router->get('/produtos/page/:page', array('controller' => 'ProductsController', 'action' => 'index'));
    $router->get('/produtos/novo', array('controller' => 'ProductsController', 'action' => '_new'));
    $router->post('/produtos', array('controller' => 'ProductsController', 'action' => 'create'));
    $router->get('/produtos/:id', array('controller' => 'ProductsController', 'action' => 'show'));
    $router->get('/produtos/:id/editar', array('controller' => 'ProductsController', 'action' => 'edit'));
    $router->post('/produtos/:id', array('controller' => 'ProductsController', 'action' => 'update'));
    $router->get('/produtos/:id/deletar', array('controller' => 'ProductsController', 'action' => 'destroy'));

    /* Fim das rotas para os produtos
    --------------------------------- */

    /* Rotas para os clientes físicos
    ------------------------- */
    $router->get('/clientes/autocomplete-search', array('controller' => 'ClientsPiController', 'action' => 'autoCompleteSearch'));

    $router->get('/clientes/pessoa-fisica', array('controller' => 'ClientsPiController', 'action' => 'index'));
    $router->get('/clientes/nova-pessoa-fisica', array('controller' => 'ClientsPiController', 'action' => '_new'));
    $router->post('/clientes/pessoa-fisica', array('controller' => 'ClientsPiController', 'action' => 'create'));
    $router->get('/clientes/:id/pessoa-fisica', array('controller' => 'ClientsPiController', 'action' => 'show'));
    $router->get('/clientes/:id/editar-pessoa-fisica', array('controller' => 'ClientsPiController', 'action' => 'edit'));
    $router->post('/clientes/:id/pessoa-fisica', array('controller' => 'ClientsPiController', 'action' => 'update'));
    $router->get('/clientes/:id/deletar-pessoa-fisica', array('controller' => 'ClientsPiController', 'action' => 'destroy'));

    /* Fim das rotas para os clientes físicos
    --------------------------------- */

    /* Rotas para os clientes jurídicos
    ------------------------- */

    $router->get('/clientes/pessoa-juridica', array('controller' => 'ClientsPcController', 'action' => 'index'));
    $router->get('/clientes/nova-pessoa-juridica', array('controller' => 'ClientsPcController', 'action' => '_new'));
    $router->post('/clientes/pessoa-juridica', array('controller' => 'ClientsPcController', 'action' => 'create'));
    $router->get('/clientes/:id/pessoa-juridica', array('controller' => 'ClientsPcController', 'action' => 'show'));
    $router->get('/clientes/:id/editar-pessoa-juridica', array('controller' => 'ClientsPcController', 'action' => 'edit'));
    $router->post('/clientes/:id/pessoa-juridica', array('controller' => 'ClientsPcController', 'action' => 'update'));
    $router->get('/clientes/:id/deletar-pessoa-juridica', array('controller' => 'ClientsPcController', 'action' => 'destroy'));

    /* Fim das rotas para os clientes jurídicos
    --------------------------------- */


    $router->load();
?>
