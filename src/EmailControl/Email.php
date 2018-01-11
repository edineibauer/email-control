<?php

/**
 * Email [ MODEL ]
 * Modelo responável por configurar a Mailgun, validar os dados e disparar e-mails do sistema!
 *
 * @copyright (c) 2017, Edinei J. Bauer
 */

namespace EmailControl;

use Helpers\Template;
use Mailgun\Mailgun;

class Email
{

    /** CORPO DO E-MAIL */
    private $assunto = "";
    private $mensagem = "";
    private $html = "";
    private $nome = "Anônimo";
    private $email;
    private $anexo;

    private $emailRemetente;
    private $nomeRemetente;
    private $mailGunApi;
    private $mailGunDomain;
    private $library = "email-control";
    private $folder;
    private $template;
    private $data;

    /** CONSTROLE */
    private $result;

    public function __construct($library = null)
    {
        if ($library)
            $this->setLibrary($library);
        $this->nomeRemetente = "Contato" . defined('SITENAME') ? " " . SITENAME : "";
        $this->emailRemetente = defined('EMAIL') ? EMAIL : "contato@ontab.com.br";
        $this->mailGunApi = defined('MAILGUNKEY') ? MAILGUNKEY : null;
        $this->mailGunDomain = defined('MAILGUNDOMAIN') ? MAILGUNDOMAIN : "ontab.com.br";
    }

    /**
     * @param string $template
     * @param mixed $data
     */
    public function setTemplate(string $template, $data = null)
    {
        $this->template = $template;
        if ($data && is_array($data))
            $this->setData($data);
    }

    /**
     * @param mixed $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $this->preData($data);
    }

    /**
     * @param mixed $library
     */
    public function setLibrary($library)
    {
        $this->library = $library;
    }

    /**
     * @param mixed $assunto
     */
    public function setAssunto($assunto)
    {
        $this->assunto = $assunto;
    }

    /**
     * @param mixed $mensagem
     */
    public function setMensagem($mensagem)
    {
        $this->mensagem = $mensagem;
    }

    /**
     * @param mixed $nome
     */
    public function setDestinatarioNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @param mixed $email
     */
    public function setDestinatarioEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param mixed $emailRemetente
     */
    public function setRemetenteEmail($emailRemetente)
    {
        $this->emailRemetente = $emailRemetente;
    }

    /**
     * @param mixed $nomeRemetente
     */
    public function setRemetenteNome($nomeRemetente)
    {
        $this->nomeRemetente = $nomeRemetente;
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
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $email
     */
    public function enviar($email = null)
    {
        if(!$this->data) $this->data = $this->preData([]);
        $tpl = new Template($this->library);
        $this->data['email_header'] = $tpl->getShow("model/header", $this->data);
        $this->data['email_footer'] = $tpl->getShow("model/footer", $this->data);

        if($this->template) {
            if ($this->folder) {
                $tpl->setFolder($this->folder);
                $this->data['email_content'] = $tpl->getShow($this->template, $this->data);
                $tpl->setFolder(null);
            } else {
                $this->data['email_content'] = $tpl->getShow($this->template, $this->data);
            }
        } else {
            $this->data['email_content'] = $tpl->getShow("model/container", $this->data);
        }

        $this->html = $tpl->getShow("model/base", $this->data);

        $this->clear();

        if ($email)
            $this->checkEmailDestinatario($email);
        else
            $this->sendEmail();
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    private function sendEmail()
    {
        if($this->mailGunApi) {
            $param = [
                'from' => "{$this->nomeRemetente} <{$this->emailRemetente}>",
                'to' => $this->email,
                'subject' => $this->assunto,
                'text' => $this->mensagem,
                'html' => $this->html
            ];

            $param = $this->checkAnexo($param);

            $mg = Mailgun::create($this->mailGunApi);
            $this->result = $mg->messages()->send($this->mailGunDomain, $param);
        }
    }

    /**
     * @param string $string1
     * @param string $string2
     */
    private function checkEmailInString(string $string1, string $string2)
    {
        if (preg_match('/@/i', $string1)) {
            $this->nome = $string2;
            $this->email = $string1;
        } else {
            $this->nome = $string1;
            $this->email = $string2;
        }
    }

    private function checkEmailDestinatario($email)
    {
        if (is_array($email)) {
            foreach ($email as $i => $m) {
                if (is_numeric($i)) {
                    foreach ($m as $a => $b) {
                        if (is_string($a) && is_string($b))
                            $this->checkEmailInString($a, $b);
                    }
                } elseif (is_string($i) && is_string($m)) {
                    $this->checkEmailInString($i, $m);
                }
                $this->sendEmail();
            }

        } elseif (is_string($email)) {
            $this->email = $email;
            $this->sendEmail();
        }
    }

    private function checkAnexo($param)
    {
        if ($this->anexo) {
            $anexo = "";
            foreach ($this->anexo as $file => $name) {
                $anexo .= (!empty($anexo) ? ", " : "") . "['filePath' => '{$file}', 'filename' => '{$name}']";
            }
            $param['attachment'] = $anexo;
        }

        return $param;
    }

    //Limpa código e espaços!
    private function clear()
    {
        $this->html = trim($this->html);
        $this->assunto = trim(strip_tags($this->assunto));
        $this->mensagem = trim(strip_tags($this->mensagem));
    }

    /**
     * Configura informações básicas que são submetidas aos templates de email
     *
     * @param array $data
     * @return array
     */
    private function preData(array $data): array
    {
        if ($this->template)
            $data = $this->preDataTemplate($data);

        $data['assunto'] = $data['assunto'] ?? $this->assunto;
        $data['title'] = $data['title'] ?? $data['assunto'];
        $data['mensagem'] = $data['mensagem'] ?? $this->mensagem;
        $data['remetenteEmail'] = $data['remetenteEmail'] ?? $this->emailRemetente;
        $data['remetenteNome'] = $data['remetenteNome'] ?? $this->nomeRemetente;
        $data['destinatarioEmail'] = $data['destinatarioEmail'] ?? $this->email;
        $data['destinatarioNome'] = $data['destinatarioNome'] ?? $this->nome;
        $data['email'] = $data['email'] ?? $this->email;
        $data['nome'] = $data['nome'] ?? $this->nome;
        $data['footerColor'] = $data['footerColor'] ?? "dddddd";
        $data['headerColor'] = $data['headerColor'] ?? "70bbd9";

        if ($data)
            $this->preDataClass($data);

        return $data;
    }

    /**
     *
     * @param array $data
     * @return array
     */
    private function preDataTemplate(array $data): array
    {
        switch ($this->template) {
            case "password":
                $data['assunto'] = "Recuperação de Senha";
        }

        return $data;
    }

    /**
     * Verifica se existe parâmetros de informação do email submetidos ao template
     * que não estão informados na classe
     *
     * @param array $data
     * @return array $data
     */
    private function preDataClass(array $data): array
    {
        if ($data['assunto'] && !$this->assunto)
            $this->assunto = $data['assunto'];
        if ($data['mensagem'] && !$this->mensagem)
            $this->mensagem = $data['mensagem'];
        if ($data['remetenteEmail'] && !$this->emailRemetente)
            $this->emailRemetente = $data['remetenteEmail'];
        if ($data['remetenteNome'] && !$this->nomeRemetente)
            $this->nomeRemetente = $data['remetenteNome'];
        if ($data['destinatarioEmail'] && !$this->email)
            $this->email = $data['email'];
        if ($data['destinatarioNome'] && !$this->nome)
            $this->nome = $data['nome'];
        if ($data['email'] && !$this->email)
            $this->email = $data['email'];
        if ($data['nome'] && !$this->nome)
            $this->nome = $data['nome'];

        return $data;
    }

}
