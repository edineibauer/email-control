<?php

if(!empty($route->getVar())){
    $read = new \ConnCrud\Read();
    $read->exeRead("email_envio", "WHERE id = :id", "id={$route->getVar()[0]}");
    if($read->getResult()) {

        $dados = $read->getResult()[0];

        $emailSend = new \EmailControl\Email();
        $emailSend->setDestinatarioEmail($dados['email_destinatario']);
        $emailSend->setAssunto($dados['assunto']);
        $emailSend->setMensagem($dados['mensagem']);

        if(!empty($dados['nome_destinatario']))
            $emailSend->setDestinatarioNome($dados['nome_destinatario']);

        if (!empty($dados['template']))
            $emailSend->setTemplate($dados['template']);

        if(!empty($dados['anexos']))
            $emailSend->setAnexo($dados['anexos']);

        $emailSend->setVariables([
            'id' => $dados['id'],
            'image' => (!empty($dados['imagem_capa']) ? HOME . json_decode($dados['imagem_capa'], true)[0]['url'] : ""),
            'background' => (!empty($dados['background']) ? HOME . json_decode($dados['background'], true)[0]['url'] : ""),
            'btn' => !empty($dados['texto_do_botao']) ? $dados['texto_do_botao'] : "",
            'link' => !empty($dados['link_do_botao']) ? $dados['link_do_botao'] : "",
        ]);

        echo $emailSend->getEmailContent();
    }
}