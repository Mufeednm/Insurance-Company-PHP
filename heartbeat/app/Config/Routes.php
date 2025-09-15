<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setAutoRoute(true);
$routes->get('install', 'Install::index');
$routes->post('install', 'Install::run');
$routes->get('/', 'Home::index');
