<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;

// подключаем Composer
require '../vendor/autoload.php';

// создаём объект приложения
$app = AppFactory::create();

// указываем базовый путь для роутинга — поскольку это подкаталог
$app->setBasePath('/slim1');

//Создаем тикет

// http://demo/slim1/
$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    $response->getBody()->write('Home page');
    return $response;
});

// http://demo/slim1/hello
$app->get('/hello', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    $response->getBody()->write('Hello world!');
    return $response;
});

// запускаем приложение
$app->run();
