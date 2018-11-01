<?php
$post = json_decode(file_get_contents('php://input'), true)[0]['msys']['message_event'];
$type = $post['type'];
$id = $post['transmission_id'];

$up = new \ConnCrud\Update();

if($type === "delivery") {
    $dados['email_entregue'] = 1;
} elseif($type === "open") {
    $dados['email_aberto'] = 1;
} elseif($type === "click") {
    $dados['email_clicado'] = 1;
} elseif($type === "spam_complaint") {
    $dados['email_spam'] = 1;
}

$up->exeUpdate("email_envio", $dados, "WHERE transmission_id = :id", "id={$id}");