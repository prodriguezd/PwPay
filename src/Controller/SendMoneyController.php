<?php

namespace pw2_grupo_21\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use pw2_grupo_21\Validator\Validator;
use pw2_grupo_21\Model\Transaction;
use DateTime;

final class SendMoneyController {
    private ContainerInterface $container;
    private Validator $validator;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->validator = new Validator();
    }

    public function showSendMoney(Request $request, Response $response): Response {
        if (empty($_SESSION['id'])) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        
        return $this->container->get('view')->render(
            $response,
            'sendMoney.twig',
            []
        );
        
    }

    public function sendMoney(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $errors = $this->checkFields($data);

        if (count($errors) == 0) {
            $errors = $this->checkData($data);

            if (count($errors) == 0) {
                $user = $this->container->get('repository')->getUserById($_SESSION['id']);
                $who = $this->container->get('repository')->getUserByEmail($data['who']);

                if ($who == null) {
                    $errors['who'] = 'This user is not registered!';
                }
                else {

                    if ($data['amount'] > $user->balance()) {
                        $errors['amount'] = 'You do not have enough money!';
                    }
                    else {

                        $transaction = Transaction::withParams(
                                        $user->Id(), 
                                        $who->Id(), 
                                        $data['amount'], 
                                        new DateTime(),
                                        $user->name(),
                                        $who->name(),
                                        (bool)1
                        );

                        $newBalance = ((float)$user->balance() - (float)$data['amount']);
                        $this->container->get('repository')->updateBalance($newBalance, $transaction, $user->Id());
                        $newBalance = ((float)$data['amount'] + (float)$user->balance());
                        $this->container->get('repository')->addAmount($newBalance, $who->Id());
                        
                        echo "<script>
                            alert('Money sended correctly.');
                            window.location.href='/account/summary'
                            </script>";
                        return $response->withHeader('Location', '/account/summary')->withStatus(302);
                    }
                }
            }
        }

        return $this->container->get('view')->render(
            $response, 
            'sendMoney.twig', 
            [
                'formErrors' => $errors,
                'formData' => $data, 
            ]
        ); 
    }


    private function checkData($data) {
        $errors = [];

        $amountOK = preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $data['amount']);

        if (!$amountOK) {
            $errors['amount'] = 'Invalid value for amount [xx.xx]';
        }

        if (!$this->validator->validateEmail($data['who'])) {
            $errors['who'] = 'Invalid domain';
            return $errors;
        }

        return $errors;
    }


    private function checkFields($data) {
        $errors = [];

        if(empty($data['who'])) {
            $errors['who'] = 'This fied cannot be empty';
        }

        if (empty($data['amount'])) {
            $errors['amount'] = 'This field cannot be empty';
            return $errors;
        }
        
        return $errors;
    }
}