ecash mobile api
================
A composer package for ecashmobile api
originally from https://github.com/ecashmobile/ecashmobileapi-sdk-php

-------------
documentation
-------------

    <?php
    
    use Mukete\ECashMobileAPI as ECashMobileAPI;
    
    // create ecashmobile api instance
    $eCashInstance = ECashMobileAPI::getInstance("clientID", "clientSecret", "username", "password");
    
    // get access token
    $accessToken = $ecashAPI->oauthAuthenticate();
    
    //make a payment request
    $paymentRequest = $eCashInstance->requestPayment(amount, phoneNumber);




------
todo
------
write unit tests

