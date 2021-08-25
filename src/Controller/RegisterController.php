<?php

namespace pw2_grupo_21\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use pw2_grupo_21\Validator\Validator;
use pw2_grupo_21\Model\User;
use DateTime;

final class RegisterController {
    private ContainerInterface $container;
    private Validator $validator;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->validator = new Validator();
    }

    public function showRegisterPage(Request $request, Response $response): Response {
        
        return $this->container->get('view')->render(
            $response,
            'register.twig',
            []
        );  
    }

    public function handleSubmit(Request $request, Response $response): Response {
        
        $data = $request->getParsedBody();
        $errors = $this->checkFields($data);

        if (count($errors) == 0) {
            $errors = $this->validateData($data);

            if (count($errors) == 0) {
                $exists = $this->container->get('repository')->checkIfExists($data['email']);

                if ($exists == 0) {
                    //$birthdate = date('Y-m-d H:i:s', strtotime($data['birthdate']));

                    $user = User::withParams($data['name'], 
                            $data['lastname'], 
                            $data['email'], 
                            $data['password'], 
                            $data['birthdate'], 
                            $data['phone'], 
                            0.0, 
                            new DateTime(), 
                            new DateTime()
                        );
                   
                    $this->container->get('repository')->save($user);

                    $id = $this->container->get('repository')->getIdByEmail($user->email());
                    $token = bin2hex(random_bytes(10));
                    $this->container->get('repository')->saveToken($id, $token);
                    $this->sendVerification($user->email(), $user->name(), $user->lastName(), $token);

                    return $this->container->get('view')->render(
                        $response,
                        'checkMail.twig',
                        []
                    );  
                }
                else {
                    $errors['email'] = 'This email is registered to another account!';
                }
            }
        }

        return $this->container->get('view')->render(
            $response,
            'register.twig',
            [
                'formErrors' => $errors,
                'formData' => $data,
            ]
        );  
    }

    private function checkFields($data) {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'This field cannot be empty';
        }
        if (empty($data['lastname'])) {
            $errors['name'] = 'This field cannot be empty';
        }
        if (empty($data['email'])) {
            $errors['email'] = 'This field cannot be empty';
        }
        if (empty($data['password'])) {
            $errors['password'] = 'This field cannot be empty';
        }
        if (empty($data['birthdate'])) {
            $errors['birthdate'] = 'This field cannot be empty';
        }

        return $errors;
    }

    private function validateData($data) {
        $errors = [];

        if (!$this->validator->validateEmail($data['email'])) {
            $errors['email'] = 'Invalid domain';
            return $errors;
        }

        if (!$this->validator->validateBirthdate(date('Y-m-d H:i:s', strtotime($data['birthdate'])))) {
            $errors['birthdate'] = 'You have to be +18';
            return $errors;
        }

        if (!$this->validator->validPassword($data['password'])) {
            $errors['password'] = '[+5 caracters, 1 number, 1 upper case, 1 lower case]';
            return $errors;
        }

        if (!$this->validator->validatePhone($data['phone']) && !empty($data['phone'])) {
            $errors['phone'] = 'Invalid phone number';
            return $errors;
        }
        
        return $errors;
        
    }

    private function sendVerification($email, $name, $lastName, $token) {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host = 'mail.smtpbucket.com';                        // Set the SMTP server to send through
            $mail->Port = 8025;                                         // TCP port to connect to
            
            $aux = ' ';

            //Recipients
            $mail->setFrom('pwpay@pwpay.com', 'Mailer');
            $mail->addAddress($email, $name.$aux.$lastName);
            $mail->addReplyTo('info@pwpay.com', 'Information');

            // Content
            $mail->isHTML(true);                                        // Set email format to HTML
            $mail->Subject = 'Please verify your profile';
            $mail->Body = "Please click <b><a href='http://localhost:8030/activate?token={$token}'>on this link</a></b> to verify your profile. ";
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
        } catch (Exception $e) {
        }
    }    
}