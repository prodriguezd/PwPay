<?php

namespace pw2_grupo_21\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class ProfileController {
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function showProfile(Request $request, Response $response): Response {
        if (empty($_SESSION['id'])) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $id = $_SESSION['id'];    
        $user = $this->container->get('repository')->getUserById($id);

        return $this->container->get('view')->render(
            $response,
            'profile.twig',
            [
                'user' => $user,
            ]
        );
    }

    public function showChangePassword(Request $request, Response $response): Response {
        if (empty($_SESSION['id'])) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        return $this->container->get('view')->render(
            $response,
            'changePassword.twig',
            []
        );



    }

    public function editProfile(Request $request, Response $response): Response {
        
    }

    public function changePassword(Request $request, Response $response): Response {

        $data = $request->getParsedBody();
        $errors = $this->checkFields($data);

        $id = $_SESSION['id'];

        $user = $this->container->get('repository')->getUserById($id);

        if (count($errors) == 0) {
            if (strcmp($data['newpassword'], $data['newpassword']) == 0) {
                $ok = $this->container->get('repository')->verifyOldPassword($id, $data['oldpassword']);

                if($ok == true) {
                    $ok = $this->container->get('repository')->changePassword($id, $data['newpassword']);
                    echo "<script>
                            alert('Password changed');
                            window.location.href='/profile'
                            </script>";
                        return $response->withHeader('Location', '/profile')->withStatus(302);
                }
                else {
                    $errors['oldpassword'] = 'Password incorrect';
                }
            }
            else {
                $errors['newpassword'] = 'Passwords do not match';
            }
        }

        return $this->container->get('view')->render(
            $response, 
            'changePassword.twig', 
            [
                'formErrors' => $errors,
                'formData' => $data, 
            ]
        ); 
    }

    private function checkFields($data) {

        $errors = [];

        if (empty($data['oldpassword'])) {
            $errors['oldpassword'] = 'This field cannot be empty';
        }
        if (empty($data['newpassword'])) {
            $errors['newpassword'] = 'This field cannot be empty';
        }
        if (empty($data['repnewpassword'])) {
            $errors['repnewpassword'] = 'This field cannot be empty';
        }

        return $errors;
    }
}