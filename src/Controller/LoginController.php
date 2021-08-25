<?php

namespace pw2_grupo_21\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class LoginController {
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function showLoginPage(Request $request, Response $response): Response {
        
        if (empty($_SESSION['id'])) {
            return $this->container->get('view')->render(
                $response,
                'login.twig',
                []
            );
        }

        /*$this->container->get('flash')->addMessage(
            'error',
            'First you need to logout!'
        );

        $messages = $this->container->get('flash')->getMessages();
        $notification = $messages['error'] ?? [];*/

        return $response->withHeader('Location', '/')->withStatus(302);

    }

    public function handleSubmit(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $errors = $this->checkFields($data);

        if (count($errors) == 0) {
            $user_id = $this->container->get('repository')->checkCredentials($data['email'], $data['password']);

            if ($user_id == NULL) {
                $errors['email'] = 'Incorrect credentials';
                $errors['password'] = ' ';
            }
            else {
                $_SESSION['id'] = $user_id;
                return $response->withHeader('Location', '/account/summary')->withStatus(302);
            }
        }
        return $this->container->get('view')->render(
            $response,
            'login.twig',
            [
                'formErrors' => $errors,
                'formData' => $data,
            ]
        );  
    }

    private function checkFields($data) {
        $errors = [];
        
        if (empty($data['email'])) {
            $errors['email'] = 'This field cannot be empty';
        }
        if (empty($data['password'])) {
            $errors['password'] = 'This field cannot be empty';
        }

        return $errors;
    }
}