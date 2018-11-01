<div class="overlay"></div>
<video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop">
    <source src="{$home}{$vendor}email-control/assets/video/bg.mp4" type="video/mp4">
</video>

{if !empty($email)}
    <div class="masthead">
        <div class="masthead-bg"></div>
        <div class="container h-100">
            <div class="row h-100">
                <div class="col-12 my-auto">
                    <div class="masthead-content text-white py-5 py-md-0">
                        <h2 class="mb-3"><b>DESCULPAS</b> {(!empty($nome)) ? $nome : ""}</h2>
                        <p class="mb-5">por enviar emails de forma não desejada, saiba que esse <strong>NÃO É</strong>
                            nosso objetivo!</p>

                        <div class="container">
                            <div class="row">
                                <h3 class="text-white">Vamos Reajustar as preferências!</h3>
                            </div>
                            <div class="row">
                                <div class="col-sm p-2 m-1 text-white">
                                    <label>Conteúdo</label>
                                    <div class="col">
                                        <div class="custom-control custom-checkbox">
                                            <input checked="checked" type="checkbox" class="custom-control-input" id="promo">
                                            <label class="custom-control-label text-white" style="cursor: pointer"
                                                   for="promo">Promoções</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="artigos">
                                            <label class="custom-control-label text-white" style="cursor: pointer"
                                                   for="artigos">Artigos</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="outros">
                                            <label class="custom-control-label text-white" style="cursor: pointer"
                                                   for="outros">Outros</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm p-2 m-1 text-white">
                                    <label>Frequência</label>
                                    <div class="col">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="2semana" value="2" name="frequencia"
                                                   class="custom-control-input frequencia">
                                            <label class="custom-control-label" for="2semana" style="cursor: pointer">
                                                2x Semana
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="semanal" value="3" name="frequencia"
                                                   class="custom-control-input frequencia">
                                            <label class="custom-control-label" for="semanal" style="cursor: pointer">Semanal</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" checked="checked" id="mensal" value="4" name="frequencia"
                                                   class="custom-control-input frequencia">
                                            <label class="custom-control-label" for="mensal" style="cursor: pointer">Mensal</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <button class="btn text-white theme-d2 theme-border p-3 col shadow" id="save">
                                    Salvar Preferências
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="social-icons">
        <div class="container">
            <div class="row">
                <h2 class="text-white">Caso queira Realmente sair, basta selecionar o Motivo</h2>
            </div>
            <div class="row" style="font-size: 17px">
                <div class="col-sm p-3 m-1 bg-success text-white shadow unsub" rel="3" style="cursor: pointer">
                    Muitos emails
                </div>
                <div class="col-sm p-3 m-1 bg-primary text-white shadow unsub" rel="2" style="cursor: pointer">
                    Nunca desejei receber isso
                </div>
                <div class="col-sm p-3 m-1 bg-warning shadow unsub" rel="1" style="cursor: pointer">
                    Conteúdo sem relevância
                </div>
            </div>
            <input type="hidden" id="email" value="{$email}" />
        </div>
    </div>
{else}
    <div class="masthead">
        <div class="masthead-bg"></div>
        <div class="container h-100">
            <div class="row h-100">
                <div class="col-12 my-auto">
                    <div class="masthead-content text-white py-5 py-md-0">
                        <h2 class="mb-3">Opa! Email inválido</h2>
                        <p class="mb-5">este email não consta no nosso sistema</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}