<?php

function flashSession($message, $error )
{
    $_SESSION['pending'] = true;
    $_SESSION['message'] = $message;
    $_SESSION['success']=true ;
    $_SESSION['error']=$error ;

}
