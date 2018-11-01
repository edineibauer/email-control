<?php
$content = file_get_contents('php://input');
if(!empty($content)) {
    $contentArray = json_decode($content, true);
    if(!empty($contentArray[0]['msys']['message_event'])) {

        $data['data'] = "recebido";
        $post = json_decode($content, true)[0]['msys']['message_event'];
        $type = $post['type'];
        $id = $post['transmission_id'];

        if($type === "delivery") {
            $dados['email_recebido'] = 1;
        } elseif($type === "open") {
            $dados['email_aberto'] = 1;
        } elseif($type === "click") {
            $dados['email_clicado'] = 1;
        } elseif($type === "spam_complaint") {
            $dados['email_spam'] = 1;
        }

        if(isset($dados)) {
            $up = new \ConnCrud\Update();
            $up->exeUpdate("email_envio", $dados, "WHERE transmission_id = :id", "id={$id}");
            if($up->getError())
                $data['error'] = $up->getError();
            else
                $data['data'] = "atualizado";
        }
    }
}