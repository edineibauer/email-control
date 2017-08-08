<?php

/**
 * Email [ MODEL ]
 * Modelo responável por configurar a Mailgun, validar os dados e disparar e-mails do sistema!
 *
 * @copyright (c) 2017, Edinei J. Bauer
 */

namespace EmailControl;

use Helpers\View;
use Mailgun\Mailgun;

class Email{

    /** CORPO DO E-MAIL */
    private $assunto;
    private $mensagem;
    private $html;
    private $nome;
    private $email;
    private $templateUrl;
    private $anexo;

    private $emailRemetente;
    private $nomeRemetente;

    /** CONSTROLE */
    private $result;

    public function __construct()
    {
        $this->templateUrl = "vendor/conn/email-control/src/EmailControl/view";
        $this->nomeRemetente = "Contato " . defined('SITENAME') ? SITENAME : "";
        $this->emailRemetente = defined('EMAIL') ? EMAIL : "contato@email-control.com";
    }

    /**
     * @param string $templateUrl
     */
    public function setTemplateUrl($templateUrl)
    {
        $this->templateUrl = $templateUrl . "src/EmailControl/view";
    }

    /**
     * @param mixed $assunto
     */
    public function setAssunto($assunto) {
        $this->assunto = $assunto;
    }

    /**
     * @param mixed $mensagem
     */
    public function setMensagem($mensagem) {
        $this->mensagem = $mensagem;
    }

    /**
     * @param mixed $nome
     */
    public function setDestinatarioNome($nome) {
        $this->nome = $nome;
    }

    /**
     * @param mixed $email
     */
    public function setDestinatarioEmail($email) {
        $this->email = $email;
    }

    /**
     * @param mixed $html
     */
    public function setHtml($html) {
        $this->html = $html;
    }

    /**
     * @param mixed $emailRemetente
     */
    public function setRemetenteEmail($emailRemetente) {
        $this->emailRemetente = $emailRemetente;
    }

    /**
     * @param mixed $nomeRemetente
     */
    public function setRemetenteNome($nomeRemetente) {
        $this->nomeRemetente = $nomeRemetente;
    }

    public function setTemplate($template, $data = array()) {
        $view = new View();
        $view->setBase($this->templateUrl);
        $this->html = $view->getShow($template, $data);
    }

    public function setAnexo($file, $name)
    {
        $this->anexo[$file] = $name;
    }

    /**
     * <b>Verificar Envio:</b> Executando um getResult é possível verificar se foi ou não efetuado
     * o envio do e-mail. Para mensagens execute o getError();
     * @return BOOL $Result = TRUE or FALSE
     */
    public function getResult() {
        return $this->result;
    }

    public function enviar($emailDestino = null) {
        if($emailDestino) {
            $this->email = $emailDestino;
        }

        $this->Clear();
        $this->PreFormat();

        $param = [
            'from'    => "{$this->nomeRemetente} <{$this->emailRemetente}>",
            'to'      => $this->email,
            'subject' => $this->assunto,
            'text'    => $this->mensagem,
            'html' => $this->html
        ];

        $param = $this->checkAnexo($param);

        $mg = Mailgun::create(MAILGUNKEY);
        $this->result = $mg->messages()->send(MAILGUNDOMAIN, $param);
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    private function checkAnexo($param)
    {
        if($this->anexo) {
            $anexo = "";
            foreach ($this->anexo as $file => $name) {
                $anexo .= (!empty($anexo) ? ", " : "") . "['filePath' => '{$file}', 'filename' => '{$name}']";
            }
            $param['attachment'] = $anexo;
        }

        return $param;
    }

    //Limpa código e espaços!
    private function Clear() {
        $this->html = trim($this->html);
        $this->assunto = trim(strip_tags($this->assunto));
        $this->mensagem = trim(strip_tags($this->mensagem));
    }

    //Formatar ou Personalizar a Mensagem!
    private function PreFormat() {
        $this->Mensagem = "{$this->html}<hr><small>Recebida em: " . date('d/m/Y H:i') . "</small>";
    }

}
