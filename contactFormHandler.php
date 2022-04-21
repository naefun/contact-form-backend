<?php

function checkRecaptcha(){
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array('secret' => $_SERVER['HTTP_GRECAPTCHA_SECRET'], 'response' => $_POST['grecaptcha']);
    
    // verify captcha
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === false) {
        throw new InvalidArgumentException("grecaptcha cannot be empty");
    }

    // parse response. $result should have "true" if the captcha was valid.
    if(strpos($result, "false") == true){
        return false;
    }else{
        return true;
    }
}

function sendMail(){
    $to      = $_SERVER['HTTP_TO_EMAIL'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $headers = 'From: ' . $_SERVER['HTTP_FROM_EMAIL'] . "\r\n" .
        'Reply-To: ' . $_POST['email'] . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    
    mail($to, $subject, $message, $headers);
}

function main(){
    if(checkRecaptcha() == false){
        echo("Nah, not happening.");
        throw new InvalidArgumentException("Cannot verify user");
    }else{
        sendMail();
    }
}

main();
?>
