<?php
require_once('../../../autoload.php');
require_once('../../../includes/config.php');

use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

$_api_context = new \PayPal\Rest\ApiContext(
                new \PayPal\Auth\OAuthTokenCredential($rest_client_id,$rest_client_secret)
            );
echo "<pre>";


if (isset($_GET['success']) && $_GET['success'] == 'true') {        

    try {
        
        $paymentId = $_GET['paymentId'];
        $payment = Payment::get($paymentId, $_api_context);
        
        $execution = new PaymentExecution();
        $execution->setPayerId($_GET['PayerID']);

        $payment=$payment->execute($execution, $_api_context);

        $transactions = $payment->getTransactions();
        $transaction = $transactions[0];
        $relatedResources = $transaction->getRelatedResources();
        $relatedResource = $relatedResources[0];
        $order = $relatedResource->getOrder();
        $result = \PayPal\Api\Order::get($order->getId(), $_api_context);
        var_dump($result);
    } catch (\PayPal\Exception\PayPalConnectionException  $ex) {
        var_dump($ex->getData());
        exit;
    }    
} else {
   echo "User Cancelled the Approval";
   exit;
}
