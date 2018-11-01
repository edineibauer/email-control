<?php

$read = new \ConnCrud\Read();
$read->exeRead("email_list", "WHERE email = :em", "em={$dados['email']}");
if($read->getResult()) {
    $del = new \ConnCrud\Delete();
    $del->exeDelete("email_list", "WHERE email = :em", "em={$dados['email']}");
}