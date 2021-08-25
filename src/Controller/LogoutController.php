<?php

namespace pw2_grupo_21\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class LogoutController {
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function logout(Request $request, Response $response): Response {
        if (!empty($_SESSION['id'])) {
            unset($_SESSION['id']);
            session_destroy();
        }
        
        return $response->withHeader('Location', '/')->withStatus(302);
        
    }
}