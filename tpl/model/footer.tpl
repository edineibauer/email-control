<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#{$footerColor}" style="padding: 30px 30px 30px 30px;">
    <tr>
        <td width="75%">
            &reg; {$sitename}, {$date}<br/><br/>
            <a href="{$home}email/subscribe/{$email}" target="_blank" style="color: #999999; text-decoration: none">
                Remover inscrição
            </a>
        </td>
        <td align="right">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    {if isset($twitter)}
                    <td>
                        <a href="https://twitter.com/{$twitter}">
                            <img src="http://dev.ontab.com.br/img/twitter.png" alt="Twitter" width="58" height="58"
                                 style="display: block;" border="0"/>
                        </a>
                    </td>
                    {/if}
                    {if isset($facebook)}
                    <td style="font-size: 0; line-height: 0;" width="20">&nbsp;</td>
                    <td>
                        <a href="https://www.facebook.com/{$facebook}">
                            <img src="http://dev.ontab.com.br/img/facebook.png" alt="Facebook" height="32"
                                 style="display: block;" border="0"/>
                        </a>
                    </td>
                    {/if}
                </tr>
            </table>
        </td>
    </tr>
</table>
