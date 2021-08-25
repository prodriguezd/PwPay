<?php

namespace pw2_grupo_21\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class TransactionsController {
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function showTransactions(Request $request, Response $response): Response {
        if (empty($_SESSION['id'])) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $user_id = $_SESSION['id'];
        $transactions = [];
        $transactions = $this->container->get('repository')->getDashboardTransactions($user_id);
        $money = $this->container->get('repository')->getBalanceById($user_id);
        
        
        return $this->container->get('view')->render(
            $response,
            'transactions.twig',
            [
                "money" => $money,
                "transactions" => $transactions,
                "user_id" => $user_id,
            ]
        );
        
    }

}