<?php

if($dados['email_enviado'] == 0) {

    $resultData["email_enviado"] = 1;
    $emailSend = new \EmailControl\Email();
    try {
        $emailSend->setDestinatarioNome($dados['nome_destinatario']);
        $emailSend->setDestinatarioEmail($dados['email_destinatario']);
        $emailSend->setAssunto($dados['assunto']);
        $emailSend->setMensagem($dados['mensagem']);

        if (!empty($dados['template']))
            $emailSend->setTemplate($dados['template']);

        if(!empty($dados['anexos']))
            $emailSend->setAnexo($dados['anexos']);

        $emailSend->setVariables([
            'image' => (!empty($dados['imagem_capa']) ? json_decode($dados['imagem_capa'], true)[0]['url'] : ""),
            'background' => (!empty($dados['background']) ? json_decode($dados['background'], true)[0]['url'] : ""),
            'btn' => !empty($dados['texto_do_botao']) ? $dados['texto_do_botao'] : "",
            'link' => !empty($dados['link_do_botao']) ? $dados['link_do_botao'] : "",
        ]);

        $emailSend->enviar();

        if($emailSend->getResult())
            $resultData['transmission_id'] = $emailSend->getResult();

    } catch (Exception $e) {
        $emailSend->setError($e->getMessage());
    }

    if ($emailSend->getError())
        $resultData["email_error"] = 1;

    $up = new \ConnCrud\Update();
    $up->exeUpdate("email_envio", $resultData, "WHERE id = :id", "id={$dados['id']}");
}