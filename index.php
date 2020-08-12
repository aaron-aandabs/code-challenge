<?php

require './vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Elevator\StartupController as Startup;
use \Elevator\CartController as Cart;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$config['db']['host']   = $_ENV['DB_HOST'];
$config['db']['user']   = $_ENV['DB_USER'];
$config['db']['pass']   = $_ENV['DB_PASS'];
$config['db']['dbname'] = $_ENV['DB_NAME'];

$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$app->get('/call/{callFrom}/to/{callTo}', function (Request $request, Response $response, array $args) {

    $startup = new Startup;
    $startupCheck = $startup->checkConnections($this->db);
    if ($startupCheck === true) {
        $carts = new Cart;
        $cartMessage = $carts->getNearestCart($args['callFrom'], $args['callTo'], $this->db);
        $finalResponse = $cartMessage;
    } else {
        $finalResponse = ['success'=>false,'message'=>'There was an error starting up your environment. Please check your error logs.'];
    }
    return $response->withJson($finalResponse);
});
$app->run();
