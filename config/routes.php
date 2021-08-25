<?php

use \pw2_grupo_21\Middleware\StartSessionMiddleware;
use \pw2_grupo_21\Controller\LandingController;
use \pw2_grupo_21\Controller\RegisterController;
use \pw2_grupo_21\Controller\ActivateController;
use \pw2_grupo_21\Controller\LoginController;
use \pw2_grupo_21\Controller\LogoutController;
use \pw2_grupo_21\Controller\ProfileController;
use \pw2_grupo_21\Controller\DashboardController;
use \pw2_grupo_21\Controller\LoadMoneyController;
use \pw2_grupo_21\Controller\SendMoneyController;
use \pw2_grupo_21\Controller\RequestMoneyController;
use \pw2_grupo_21\Controller\TransactionsController;

$app->add(StartSessionMiddleware::class);

$app->get('/', LandingController::class . ':showHome')->setName('landing');

$app->get('/sign-up', RegisterController::class . ':showRegisterPage')->setName('sign-up');
$app->post('/sign-up', RegisterController::class . ':handleSubmit');

$app->get('/activate', ActivateController::class . ':activationPage')->setName('activation');

$app->get('/sign-in', LoginController::class . ':showLoginPage')->setName('sign-in');
$app->post('/sign-in', LoginController::class . ':handleSubmit');

$app->post('/logout', LogoutController::class . ':logout');

$app->get('/profile', ProfileController::class . ':showProfile')->setName('profile');
$app->post('/profile', ProfileController::class . ':editProfile')->setName('profile');
$app->get('/profile/security', ProfileController::class . ':showChangePassword');
$app->post('/profile/security', ProfileController::class . ':changePassword');

$app->get('/account/summary', DashboardController::class . ':showDashboard')->setName('dashboard');

$app->get('/account/bank-account', LoadMoneyController::class . ':showLoadMoney');
$app->post('/account/bank-account', LoadMoneyController::class . ':submitIban');
$app->post('/account/bank-account/load', LoadMoneyController::class . ':loadMoney');

$app->get('/account/money/send', SendMoneyController::class . ':showSendMoney');
$app->post('/account/money/send', SendMoneyController::class . ':sendMoney');

$app->get('/account/money/requests', RequestMoneyController::class . ':showRequestMoney');
$app->post('/account/money/requests', RequestMoneyController::class . ':requestMoney');
$app->get('/account/money/requests/pending', RequestMoneyController::class . ':showPendingRequests');
$app->get('/account/money/requests/{id}/accept', RequestMoneyController::class . ':acceptPendingRequest')->setName('acceptRequestMoney');


$app->get('/account/transactions', TransactionsController::class . ':showTransactions');