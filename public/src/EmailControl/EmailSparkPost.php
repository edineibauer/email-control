<?php

/**
 * Email [ MODEL ]
 * Modelo responável por configurar a SparkPost, validar os dados e disparar e-mails do sistema!
 *
 * @copyright (c) 2018, Edinei J. Bauer
 */

namespace EmailControl;

use Helpers\Check;
use Helpers\DateTime;
use Helpers\Template;
use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class EmailSparkPost
{
    private $assunto;
    private $mensagem;
    private $html;
    private $destinatarioNome;
    private $destinatarioEmail;
    private $anexo;
    private $data;
    private $serverEmail;
    private $serverPassword;
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
        $this->remetenteNome = "Contato" . (defined('SITENAME') ? " " . SITENAME : "");
        $this->serverEmail = defined('EMAIL') ? EMAIL : "contato@uebster.com";
        $this->setRemetenteEmail($this->serverEmail);
        $this->assunto = "Contato através do site " . (defined('SITENAME') ? SITENAME : "");
        $this->mensagem = "";
        $this->html = "";
        $this->destinatarioNome = "";
        $this->destinatarioEmail = [];
    }

    /**
     * @param string $template
     * @param array $data
     */
    public function setTemplate(string $template, array $data = [])
    {
        $this->template = trim(strip_tags($template));
        if ($data)
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
        if (empty($this->html))
            $this->html = $this->mensagem;
    }

    /**
     * @param mixed $serverEmail
     */
    public function setServerEmail($serverEmail)
    {
        $this->serverEmail = $serverEmail;
    }

    /**
     * @param mixed $serverPassword
     */
    public function setServerPassword($serverPassword)
    {
        $this->serverPassword = $serverPassword;
    }

    /**
     * @param string $nome
     */
    public function setDestinatarioNome(string $nome)
    {
        $this->destinatarioNome = trim(strip_tags($nome));
    }

    /**
     * @param string|array $email
     */
    public function setDestinatarioEmail($email)
    {
        if (!empty($email)) {
            if (is_array($email)) {
                if(isset($email['email'])){
                    if (!empty($email['name'])) {
                        $this->destinatarioEmail[] = ['address' => [
                            'name' => trim(strip_tags($email['name'])),
                            'email' => trim($email['email'])
                        ]];
                    } else {
                        $this->destinatarioEmail[] = ['address' => ['name' => $this->prepareNameFromEmail($email['email']), 'email' => trim($email['email'])]];
                    }
                } else {
                    foreach ($email as $item) {
                        if (is_array($item) && !empty($item['email']) && Check::email($item['email'])) {
                            if (!empty($item['name'])) {
                                $this->destinatarioEmail[] = ['address' => [
                                    'name' => trim(strip_tags($item['name'])),
                                    'email' => trim(strip_tags($item['email']))
                                ]];
                            } else {
                                $this->destinatarioEmail[] = ['address' => ['email' => trim($item['email'])]];
                            }
                        } elseif (is_string($item) && Check::email($item)) {
                            $this->destinatarioEmail[] = ['address' => ['name' => $this->prepareNameFromEmail($item), 'email' => trim($item)]];
                        }
                    }
                }
            } elseif (is_string($email) && Check::email($email)) {
                $this->destinatarioEmail[] = ['address' => ['name' => $this->prepareNameFromEmail($email), 'email' => trim($email)]];
            }
        }
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
                if (!empty($email))
                    $this->setDestinatarioEmail(!empty($this->destinatarioNome) ? ['name' => $this->destinatarioNome, 'email' => $email] : $email);

                if(!empty($this->destinatarioEmail)) {
                    $this->html = !empty($this->html) ? $this->html : (!empty($this->template) ? $this->getTemplateData($this->template) : $this->turnMensagemIntoEmail());

                    $httpClient = new GuzzleAdapter(new Client());
                    $sparky = new SparkPost($httpClient, ['key' => EMAILKEY]);
                    $sparky->setOptions(['async' => false]);

                    $results = $sparky->transmissions->post([
                        'content' => [
                            'from' => ['name' => $this->remetenteNome, 'email' => $this->remetenteEmail],
                            'subject' => $this->assunto,
                            'html' => $this->html
                        ],
                        'recipients' => $this->destinatarioEmail
                    ]);
                }

            } catch (\Exception $e) {
                $this->result = 'Erro ao enviar';
            }
        } else {
            if (defined("EMAILKEY") && !empty(EMAILKEY))
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
            "sitename" => defined('SITENAME') ? SITENAME : "",
            "home" => defined('HOME') ? HOME : "",
            "sitedesc" => defined('SITEDESC') ? SITEDESC : "",
            "sitesub" => defined('SITESUB') ? SITESUB : "",
            "logo" => defined('LOGO') ? HOME . LOGO : "",
            "favicon" => defined('FAVICON') ? HOME . FAVICON : "",
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
        if (file_exists(PATH_HOME . "public/assets/theme.min.css")) {
            $theme = file_get_contents(PATH_HOME . "public/assets/theme.min.css");
            $theme = explode('.theme{', $theme)[1];
            $color = trim(explode('!important', explode('color:', $theme)[1])[0]);
            $backgroun = trim(explode('!important', explode('background-color:', $theme)[1])[0]);
            return [$color, $backgroun];
        }

        return ["#111", "70bbd9"];
    }
}
