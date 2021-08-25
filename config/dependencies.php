<?php

use DI\Container;
use Slim\Views\Twig;
use Psr\Container\ContainerInterface;
use pw2_grupo_21\Repository\PDOSingleton;
use pw2_grupo_21\Repository\MySQLRepository;
use Slim\Flash\Messages;

$container = new Container();

$container->set(
    'view',
    function() {
        return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
    }
);

$container->set('db', function () {
    return PDOSingleton::getInstance(
        $_ENV['MYSQL_USER'],
        $_ENV['MYSQL_ROOT_PASSWORD'],
        $_ENV['MYSQL_HOST'],
        $_ENV['MYSQL_PORT'],
        $_ENV['MYSQL_DATABASE']
    );
});

$container->set(
    'repository', 
    function (ContainerInterface $container) {
        return new MySQLRepository($container->get('db'));
    }
);

$container->set(
    'flash',
    function () {
        return new Messages();
    }
);

