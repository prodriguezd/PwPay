<?php

namespace pw2_grupo_21\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Iban\Validation\Validator;
use Iban\Validation\Iban;
use pw2_grupo_21\Model\Transaction;
use pw2_grupo_21\Model\User;
use DateTime;

final class LoadMoneyController {
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function showLoadMoney(Request $request, Response $response): Response {
       
        if (empty($_SESSION['id'])) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $user = $this->container->get('repository')->getUserById($_SESSION['id']);

        if (strcmp($user->IBAN(), "") == 0) {
            return $this->container->get('view')->render(
                $response,
                'loadMoney.twig',
                []
            );
        }

        
        $aux = substr_replace($user->IBAN(), '******************', 6, -4);

        $info = array(
                "IBAN" => $aux,
                "IBANOK" => true);

        return $this->container->get('view')->render(
            $response, 
            'loadMoney.twig', 
            $info
        );   

    }

    public function submitIban(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $errors = $this->checkFields($data);

        if (count($errors) == 0) {
            $ibanOk = $this->validateIBAN($data['iban']);

            if($ibanOk) {
                $user = $this->container->get('repository')->saveIban($data['iban'], $_SESSION['id']);
                return $response->withHeader('Location', '/account/bank-account')->withStatus(302);
            }
            else {
                $errors['iban'] = 'Incorrect IBAN';   
            }
        }

        return $this->container->get('view')->render(
            $response, 
            'loadMoney.twig', 
            [
                'formErrors' => $errors,
                'formData' => $data, 
            ]
        ); 

    }

    public function loadMoney(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $errors = $this->checkAmount($data);

        if (count($errors) == 0) {
            $user = $this->container->get('repository')->getUserById($_SESSION['id']);

            $transaction = Transaction::withParams(
                            $user->Id(), 
                            $user->Id(), 
                            $data['amount'], 
                            new DateTime(),
                            $user->name(),
                            $user->name(),
                            (bool)1
            );

            $newBalance = ((float)$data['amount'] + (float)$user->balance());

            $this->container->get('repository')->updateBalance($newBalance, $transaction, $user->Id());
            return $response->withHeader('Location', '/account/summary')->withStatus(302);

        }

        return $this->container->get('view')->render(
            $response, 
            'loadMoney.twig', 
            [
                'formErrors' => $errors,
                'formData' => $data, 
            ]
        ); 
    }



    private function checkAmount($data) {
        $errors = [];

        if (empty($data['amount'])) {
            $errors['amount'] = 'This field cannot be empty';
            return $errors;
        }

        $amountOK = preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $data['amount']);

        if (!$amountOK) {
            $errors['amount'] = 'Invalid value for amount [xx.xx]';
        }

        return $errors;
    }


    private function checkFields($data) {
        $errors = [];

        if(empty($data['ownerName'])) {
            $errors['ownerName'] = 'This fied cannot be empty';
        }
        if(empty($data['iban'])) {
            $errors['iban'] = 'This field cannot be empty';
        }
        return $errors;
    }

    private function validateIBAN($iban) {
        $validator = new Validator();

        if (!$validator->validate($iban)) {
            foreach ($validator->getViolations() as $violation) {
                $this->errorIBAN = $violation;
                return false;
            }
        }
        return true;
    }
}