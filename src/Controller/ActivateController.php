<?php

namespace pw2_grupo_21\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

final class ActivateController {
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function activationPage(Request $request, Response $response): Response {
        $token = $request->getQueryParams()['token'];

        $user_id = $this->checkToken($token);

        if($user_id == NULL) {
            return $response->withStatus(404);
        }

        return $this->container->get('view')->render(
            $response,
            'activate.twig',
            []
        );
    }

    private function checkToken($token) {
        $user_id = $this->container->get('repository')->getUserIdFromToken($token);
        return $user_id;
    }
}