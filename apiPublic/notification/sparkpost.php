<?php

$content = file_get_contents('php://input');
if(!empty($content)) {
    $contentArray = json_decode($content, true);
    if(!empty($contentArray)) {
        $dados = [];
        $conv = [
            "delivery" => "email_recebido",
            "open" => "email_aberto",
            "click" => "email_clicado",
            "spam_complaint" => "email_spam",
        ];

        foreach ($contentArray as $message) {
            if (!empty($message['msys']['message_event'])) {
                $post = $message['msys']['message_event'];

                if(in_array($post['type'], array_keys($conv)))
                    $dados[$post['transmission_id']][$conv[$post['type']]] = 1;
            }
        }

        if (!empty($dados)) {
            $up = new \ConnCrud\Update();
            foreach ($dados as $id => $dado)
                $up->exeUpdate("email_envio", $dado, "WHERE transmission_id = :id", "id={$id}");
        }
    }
}