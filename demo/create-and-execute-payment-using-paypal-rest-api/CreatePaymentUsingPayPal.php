<?php
// Include required library files.
require_once('../../vendor/autoload.php');
require_once('../../includes/config.php');


/**
 * Setup configuration for the PayPal library using vars from the config file.
 * Then load the PayPal object into $PayPal
 */

$configArray = array(
    'ClientID' => $rest_client_id,
    'ClientSecret' => $rest_client_secret,
    'LogResults' => $log_results, 
    'LogPath' => $log_path,
    'LogLevel' => $log_level  
);

$PayPal = new angelleye\PayPal\rest\payments\PaymentAPI($configArray);

/**
 * @var $intent
 * In this demo we have intent of the payment is "sale".
 * There are more intent that available in the PayPal but purpose of use them are different.
 *  sale : Must be set to sale for immediate payment
 *  authorize : To authorize a payment for capture later
 *  order : To create an order
 */

$intent= $_SESSION['intent'];

/**
 * Here we are setting up the parameters for a basic Express Checkout flow.
 *
 * The template provided at ../../vendor/angelleye/paypal-php-library/templates/samples/rest/payment/CreatePaymentUsingPayPal.php
 * contains a lot more parameters that we aren't using here, so I've removed them to keep this clean.
 *
 * $domain used here is set in the config file.
 */

$urls= array(
    'ReturnUrl'   => 'ExecutePayment.php?success=true',                                    // Required when Pay using paypal. Example : ExecutePayment.php?success=true
    'CancelUrl'   => 'ExecutePayment.php?success=false',                                   // Required when Pay using paypal. Example : ExecutePayment.php?success=false
    'BaseUrl'     => $domain.'demo/create-and-execute-payment-using-paypal-rest-api/'          // Required. The base url that we pass for the return.
);

$invoiceNumber= $_SESSION['invoiceNumber'];
$NoteToPayer = $_SESSION['NoteToPayer'];
$orderItems = $_SESSION['items'];
$paymentDetails = $_SESSION['paymentDetails'];
$amount = $_SESSION['amount'];

/**
 * Now we gather all of the arrays above into a single array.
 */

$requestData = array(
    'intent'         => $intent,    
    'invoiceNumber'  => $invoiceNumber,
    'orderItems'     => $orderItems,
    'paymentDetails' => $paymentDetails,
    'amount'         => $amount,
    'urls'           => $urls,
    'NoteToPayer'    => $NoteToPayer
);

/**
 * Here we are making the call to the create_payment_with_paypal function in the library,
 * and we're passing in our $requestData that we just set above.
 */

$returnArray = $PayPal->create_payment_with_paypal($requestData);

/**
 * Now we'll check for any errors returned by PayPal, and if we get an error,
 * we'll save the error details to a session and redirect the user to an
 * error page to display it accordingly.
 *
 * If all goes well then redirect the user to PayPal
 * using the approvalUrl returned by the create_payment_with_paypal() function.
 */

if($returnArray['RESULT'] == 'Success'){
    $approvalUrl = $returnArray['PAYMENT']['approvalUrl'];
    header('Location: ' . $approvalUrl);
}
else{
    /**
     * Error page redirection
     */
    $_SESSION['rest_errors'] = true;
    $_SESSION['errors'] = $returnArray;
    header('Location: ../error.php');
}