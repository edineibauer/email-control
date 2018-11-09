<?php

$dados = [];
if(!empty($route->getVar())){
    $read = new \ConnCrud\Read();
    $read->exeRead("email_list", "WHERE email = :em", "em={$route->getVar()[0]}");
    if($read->getResult()) {
        $dados = $read->getResult()[0];
        $dados['nome'] = explode(' ', $dados['nome'])[0];
    }
}
$tpl = new \Helpers\Template('email-control');
$tpl->show('unsubscribe', $dados);