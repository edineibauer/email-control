<?php

/**
 * Email [ MODEL ]
 * Modelo responável por configurar a SparkPost, validar os dados e disparar e-mails do sistema!
 *
 * @copyright (c) 2018, Edinei J. Bauer
 */

namespace EmailControl;

use Helpers\DateTime;
use Helpers\Template;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSparkPost
{
    private $assunto;
    private $mensagem;
    private $html;
    private $destinatarioNome;
    private $destinatarioEmail;
    private $anexo;
    private $data;
    private $remetenteEmail;
    private $remetenteNome;
    private $replyToEmail;
    private $replyToNome;
    private $copyToEmail;
    private $copyToNome;
    private $library;
    private $template;
    private $result;

    public function __construct()
    {
        $this->remetenteNome = "Contato" . defined('SITENAME') ? " " . SITENAME : "";
        $this->remetenteEmail = defined('EMAIL') ? EMAIL : "contato@uebster.com";
        $this->assunto = "Contato através do site " . SITENAME;
        $this->mensagem = "";
        $this->html = "";
        $this->destinatarioNome = "";
    }

    /**
     * @param string $template
     * @param array $data
     */
    public function setTemplate(string $template, array $data = [])
    {
        $this->template = trim(strip_tags($template));
        if($data)
            $this->data = $data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param mixed $library
     */
    public function setLibrary($library)
    {
        $this->library = trim(strip_tags($library));
    }

    /**
     * @param mixed $assunto
     */
    public function setAssunto($assunto)
    {
        $this->assunto = trim(strip_tags($assunto));
    }

    /**
     * @param string $mensagem
     */
    public function setMensagem(string $mensagem)
    {
        $this->mensagem = trim(strip_tags($mensagem));
    }

    /**
     * @param string $nome
     */
    public function setDestinatarioNome(string $nome)
    {
        $this->destinatarioNome = trim(strip_tags($nome));
    }

    /**
     * @param string $email
     */
    public function setDestinatarioEmail(string $email)
    {
        $this->destinatarioEmail = trim(strip_tags($email));
    }

    /**
     * @param string $emailRemetente
     */
    public function setRemetenteEmail(string $emailRemetente)
    {
        $this->remetenteEmail = trim(strip_tags($emailRemetente));
    }

    /**
     * @param string $nomeRemetente
     */
    public function setRemetenteNome(string $nomeRemetente)
    {
        $this->remetenteNome = trim(strip_tags($nomeRemetente));
    }

    /**
     * @param string $copyToEmail
     */
    public function setCopyToEmail(string $copyToEmail)
    {
        $this->copyToEmail = trim(strip_tags($copyToEmail));
    }

    /**
     * @param string $copyToNome
     */
    public function setCopyToNome(string $copyToNome)
    {
        $this->copyToNome = trim(strip_tags($copyToNome));
    }

    /**
     * @param string $replyToEmail
     */
    public function setReplyToEmail(string $replyToEmail)
    {
        $this->replyToEmail = trim(strip_tags($replyToEmail));
    }

    /**
     * @param string $replyToNome
     */
    public function setReplyToNome(string $replyToNome)
    {
        $this->replyToNome = trim(strip_tags($replyToNome));
    }

    /**
     * @param string $html
     */
    public function setHtml(string $html)
    {
        $this->html = $html;
    }

    public function setAnexo($file, $name)
    {
        $this->anexo[$file] = $name;
    }

    /**
     * @return mixed
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
        if (defined("EMAILKEY") && !empty(EMAILKEY) && (!empty($this->mensagem) || !empty($this->template) || !empty($this->html))) {
            try {
                $mail = new PHPMailer(true); // Passing `true` enables exceptions
                $mail->isSMTP();
                $mail->isHTML(true);
                $mail->Host = 'smtp.sparkpostmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'SMTP_Injection';
                $mail->Password = EMAILKEY;
                $mail->SMTPSecure = 'STARTTLS';
                $mail->Port = 587;

                //Set Destinatário(s)
                if ($email) {
                    if (is_array($email)) {
                        foreach ($email as $m)
                            $mail->addAddress($m, $this->prepareNameFromEmail($m));

                    } elseif (is_string($email)) {
                        $mail->addAddress($email, $this->prepareNameFromEmail($email));
                    }
                } elseif (!empty($this->destinatarioEmail)) {
                    $mail->addAddress($this->destinatarioEmail, (!empty($this->destinatarioNome) ? $this->destinatarioNome : $this->prepareNameFromEmail($this->destinatarioEmail)));
                } else {
                    $this->result = "Email de destino não informado";
                }

                //Remetente
                $mail->setFrom($this->remetenteEmail, $this->remetenteNome);

                //Envio de uma cópia do email (para teste dev)
                if ($this->copyToEmail)
                    $mail->addCC($this->copyToEmail, (!empty($this->copyToNome) ? $this->copyToNome : $this->prepareNameFromEmail($this->copyToEmail)));

                //Retornar email para o endereço específico
                if ($this->replyToEmail)
                    $mail->addReplyTo($this->replyToEmail, (!empty($this->replyToNome) ? $this->replyToNome : $this->prepareNameFromEmail($this->remetenteEmail)));
                else
                    $mail->addReplyTo($this->remetenteEmail, (!empty($this->remetenteNome) ? $this->remetenteNome : $this->prepareNameFromEmail($this->remetenteEmail)));

                //Mensagem
                if(!empty($this->html))
                    $mail->Body = $this->html;
                elseif(!empty($this->template))
                    $mail->Body = $this->getTemplateData($this->template);
                else
                    $mail->Body = $this->turnMensagemIntoEmail();

                $mail->Subject = $this->assunto;
                $mail->AltBody = $this->mensagem;

                //Anexos (caminho completo do arquivo (com PATH_HOME))
                if($this->anexo) {
                    foreach ($this->anexo as $anexo) {
                        $nomeAnexo = (preg_match("\/", $anexo) ? ucwords(str_replace(['.', '_', '-'], ' ', explode('.', explode('/', $anexo)[1])[0])) : $anexo);
                        $mail->addAttachment($anexo, $nomeAnexo);
                    }
                }
                $mail->send();

            } catch (\Exception $e) {
                $this->result = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
            }
        } else {
            if(defined("EMAILKEY") && !empty(EMAILKEY))
                $this->result = "Conteúdo do email não definido, informe uma mensagem ou template pré-definido";
            else
                $this->result = "Key de SparkPost não informado nas configurações.";
        }
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    /**
     * Retorna estrutura HTML default com mensagem
     * @param string $template
     * @return string
     */
    private function getTemplateData(string $template)
    {
        switch ($template) {
            case "password":
                $this->assunto = "Recuperação de Senha";
                break;
        }

        $tpl = new Template($this->library ?? "email-control");
        return $tpl->getShow($template, $this->getData());
    }

    /**
     * Retorna estrutura HTML default com mensagem
     * @return string
     */
    private function turnMensagemIntoEmail()
    {
        $tpl = new Template("email-control");
        $data = $this->getData();
        $data['email_header'] = $tpl->getShow("model/header", $data);
        $data['email_footer'] = $tpl->getShow("model/footer", $data);
        $data['email_content'] = $tpl->getShow("model/container", $data);

        return $tpl->getShow("model/base", $data);
    }

    /**
     * Retorna dados padrão para passar para templates
     * @return array
     */
    private function getData(): array
    {
        list($color, $background) = $this->getColorTheme();
        $date = new DateTime();
        $data = [
            "assunto" => $this->assunto,
            "mensagem" => $this->mensagem,
            "email" => $this->destinatarioEmail,
            "nome" => $this->destinatarioNome,
            "remetente" => $this->remetenteEmail,
            "remetenteNome" => $this->remetenteNome,
            "sitename" => SITENAME,
            "home" => HOME,
            "sitedesc" => SITEDESC,
            "sitesub" => SITESUB,
            "logo" => LOGO,
            "favicon" => FAVICON,
            "date" => $date->getDateTime(date("Y-m-d H:i:s"), 'd/m/Y H:i'),
            "footerColor" => "dddddd",
            "headerColor" => $color,
            "headerBackground" => $background
        ];

        return !empty($this->data) ? array_merge($this->data, $data) : $data;
    }

    /**
     * Retorna um nome a partir do email
     * @param string $email
     * @return string
     */
    private function prepareNameFromEmail(string $email): string
    {
        return ucwords(str_replace(['.', '_'], ' ', explode('@', $email)[0]));
    }


    private function getColorTheme()
    {
        $assets = DEV ? "assetsPublic" : "assets";
        if (file_exists(PATH_HOME . "{$assets}/theme/theme.css")) {
            $theme = file_get_contents(PATH_HOME . "{$assets}/theme/theme.css");
            $theme = explode('.theme {', $theme)[1];
            $color = trim(explode('!important', explode('color:', $theme)[1])[0]);
            $backgroun = trim(explode('!important', explode('background-color:', $theme)[1])[0]);
            return [$color, $backgroun];
        }

        return ["#111", "70bbd9"];
    }
}
