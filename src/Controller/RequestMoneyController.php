<?php

namespace pw2_grupo_21\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use pw2_grupo_21\Validator\Validator;
use pw2_grupo_21\Model\Transaction;
use Slim\Routing\RouteContext;
use DateTime;

final class RequestMoneyController {
    private ContainerInterface $container;
    private Validator $validator;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->validator = new Validator();
    }

    public function showRequestMoney(Request $request, Response $response): Response {
        if (empty($_SESSION['id'])) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        
        return $this->container->get('view')->render(
            $response,
            'requestMoney.twig',
            []
        );
        
    }

    public function showPendingRequests(Request $request, Response $response): Response {

        if (empty($_SESSION['id'])) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $user_id = $_SESSION['id'];
        //$requests = [];
        $transactions = $this->container->get('repository')->pendingTransactions($user_id);

        return $this->container->get('view')->render(
            $response, 
            'pendingRequests.twig', 
            [
                'transactions' => $transactions,
            ]
        ); 

    }

    public function requestMoney(Request $request, Response $response): Response {
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
                    $transaction = Transaction::withParams(
                                    $who->Id(), 
                                    $user->Id(), 
                                    $data['amount'], 
                                    new DateTime(),
                                    $who->name(),
                                    $user->name(),
                                    (bool)0
                        );

                        $this->container->get('repository')->saveTransaction($transaction);
                        echo "<script>
                            alert('Money requested correctly.');
                            window.location.href='/account/summary'
                            </script>";
                        return $response->withHeader('Location', '/account/summary')->withStatus(302);
                }
            }
        }

        return $this->container->get('view')->render(
            $response, 
            'requestMoney.twig', 
            [
                'formErrors' => $errors,
                'formData' => $data, 
            ]
        ); 
    }

    public function acceptPendingRequest(Request $request, Response $response): Response {
        if (empty($_SESSION['id'])) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $transactionId = $request->getQueryParams()['id'];
        $userId = $_SESSION['id'];

        $transaction = $this->container->get('repository')->getTransactionById($transactionId);

        if ($transaction == null) {
            return $response->withStatus(404);
        }

        $user = $this->container->get('repository')->getUserById($userId);

        if ($transaction->senderId() == $user->Id()) {
            if ($user->balance() >= $transaction->amount()) {

                $this->container->get('repository')->payTransaction($transaction);

                echo "<script>
                alert('Accepted.');
                window.location.href='/account/summary'
                </script>";
                return $response->withHeader('Location', '/account/money/requests/pending')->withStatus(302);
            }
            else {
                echo "<script>
                alert('You do not have enough money.');
                window.location.href='/account/money/requests/pending'
                </script>";
                return $response->withHeader('Location', '/account/money/requests/pending')->withStatus(302);
            }
        }
        else {
            return $response->withStatus(404);
        }

        echo "<script>
                alert('Accepted.');
                window.location.href='/account/summary'
                </script>";
        return $response->withHeader('Location', '/account/summary')->withStatus(302);

        
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