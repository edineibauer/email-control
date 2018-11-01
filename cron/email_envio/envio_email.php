<?php

$read = new \ConnCrud\Read();
$read->exeRead("email_envio", "WHERE email_enviado = 0 && data_de_envio <= NOW() ORDER BY data_de_envio ASC");
if ($read->getResult()) {

    // Para cada email disponível para ser enviado
    $up = new \ConnCrud\Update();

    foreach ($read->getResult() as $email) {

        $resultData["email_enviado"] = 1;
        $emailSend = new \EmailControl\Email();
        try {

            $emailSend->setDestinatarioEmail($email['email_destinatario']);
            $emailSend->setAssunto($email['assunto']);
            $emailSend->setMensagem($email['mensagem']);

            if(!empty($dados['nome_destinatario']))
                $emailSend->setDestinatarioNome($email['nome_destinatario']);

            if (!empty($email['template']))
                $emailSend->setTemplate($email['template']);

            if(!empty($dados['anexos']))
                $emailSend->setAnexo($dados['anexos']);

            $emailSend->setVariables([
                'image' => (!empty($email['imagem_capa']) ? HOME . json_decode($email['imagem_capa'], true)[0]['url'] : ""),
                'background' => (!empty($email['background']) ? HOME . json_decode($email['background'], true)[0]['url'] : ""),
                'btn' => !empty($email['texto_do_botao']) ? $email['texto_do_botao'] : "",
                'link' => !empty($email['link_do_botao']) ? $email['link_do_botao'] : "",
            ]);

            $emailSend->enviar();

            if($emailSend->getResult())
                $resultData['transmission_id'] = $emailSend->getResult();

        } catch (Exception $e) {
            $emailSend->setError($e->getMessage());
        }

        if ($emailSend->getError())
            $resultData["email_error"] = 1;

        $up->exeUpdate("email_envio", $resultData, "WHERE id = :id", "id={$dados['id']}");
    }
}