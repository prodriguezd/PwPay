<?php

namespace pw2_grupo_21\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class LandingController {
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function showHome(Request $request, Response $response): Response {
        if (empty($_SESSION['id'])) {
            return $this->container->get('view')->render(
                $response,
                'landing.twig',
                []
            );
        }

        return $response->withHeader('Location', '/account/summary')->withStatus(302);

    }
    
}