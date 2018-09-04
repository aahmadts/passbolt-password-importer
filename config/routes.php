<?php
use Cake\Routing\Router;

Router::plugin(
    'PassboltPasswordImporter',
    ['path' => '/password-importer'],
    function ($routes) {
        $routes->get('/', ['controller' => 'App', 'action' => 'index']);
        $routes->post('/import', ['controller' => 'Import', 'action' => 'index']);
    }
);
