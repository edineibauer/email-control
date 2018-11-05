<?php

// 5 min Update Status email

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$httpClient = new GuzzleAdapter(new Client());
$sparky = new SparkPost($httpClient, ["key" => EMAILKEY]);
$read = new \ConnCrud\Read();
$up = new \ConnCrud\Update();

$read->exeRead("email_envio", "WHERE email_entregue = 1 && email_aberto = 0 && email_error = 0");
if ($read->getResult()) {
    $ids = [];
    foreach ($read->getResult() as $email)
        $ids[] = $email['transmission_id'];

    if (!empty($ids)) {
        $promise = $sparky->request('GET', 'message-events', [
            'transmission_ids' => $ids
        ]);
        try {
            $response = $promise->wait();
            if ($response->getStatusCode() === 200 && !empty($response->getBody())) {
                $response = $response->getBody()['results'];
                $dados = [];

                foreach ($response as $item) {
                    if ($item['type'] === "open")
                        $dados[$item['transmission_id']]['email_aberto'] = 1;
                    elseif ($item['type'] === "click")
                        $dados[$item['transmission_id']]['email_clicado'] = 1;
                    elseif ($item['type'] === "spam_complaint")
                        $dados[$item['transmission_id']]['email_spam'] = 1;
                }

                if (!empty($dados)){
                    foreach ($dados as $id => $dado)
                        $up->exeUpdate("email_envio", $dado, "WHERE transmission_id = :id", "id={$id}");
                }
            }
        } catch (\Exception $e) {
            echo $e->getCode() . "\n";
            echo $e->getMessage() . "\n";
        }
    }
}
