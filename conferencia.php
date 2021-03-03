<?php

session_start();

// Verifica se usuário está logado

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

?>
 
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/custom.css">
        <link href="css/open-iconic-bootstrap.css" rel="stylesheet">
        <script src="js/vue.js"></script>
        <title>Doações decorrentes da pandemia do COVID-19</title>        
    </head>
<body style="margin: 1em">

<?php 
// Inclui arquivo de configuração
require_once "config.php";

// Declaração de variáveis

/* Muda o charset para UTF-8 */
if (!mysqli_set_charset($link, "utf8")) {
    printf("Erro ao definir charset: %s<br>", mysqli_error($link));
    exit();
}

// Obtém lista de responsaveis_atendimento (gestores)
$sql = "SELECT `rf`, `nome`, `nivel_acesso` FROM responsaveis WHERE `nivel_acesso` != 'admin' ORDER BY `nome`;";
// $sql = "SELECT `rf`, `nome`, `nivel_acesso` FROM responsaveis WHERE 1 ORDER BY `nome`;";
$retorno = $link->query($sql);
$nivel_acesso = "";
$editaResps = "[]"; // Quais responsaveis podem ter seus itens editados pelo usuario
$usrRespName = "";
$responsaveis = "[";
if ($retorno->num_rows > 0) {
    while ($row = $retorno->fetch_assoc()) {
        $responsaveis .= "'".$row['nome']."',";
        if ($row['rf'] === $_SESSION['IDUsuario']) {
            $nivel_acesso = $row['nivel_acesso'];
            $usrRespName = $row['nome'];
        }
    }
}
if (strpos($nivel_acesso, '.-.') !== false) {
    $editaResps = explode('.-.', $nivel_acesso)[1];
    $nivel_acesso = explode('.-.', $nivel_acesso)[0];
}
$responsaveis = rtrim($responsaveis, ',');
$responsaveis .= "]";
$link->close();

?>
<div id="app">
    <div class="container col-12">
        <div class="page-header row">
            <div class="col-3">
                <div style="
                    width: 90%;
                    margin: 0 auto -30%;
                    max-width: 150px;
                    text-align: center;">
                    <img src="img/logo_sp.png" alt="Cidade de São Paulo" style="max-width: 100%; max-height: 50%;">
                </div>            
            </div>
            <div class="col-6">
                <h1><center>SGD - Sistema de Gerenciamento de Doações (COVID-19)</center></h1>
            </div>
            <div class="col-3">
                <button class="btn btn-danger btn-sm float-right" @click="location.href='logout.php'">Sair do sistema</button>
                <br><br>
                <button class="btn btn-primary float-right" @click="location.href='index.php'">
                    Cadastrar nova doação
                </button>
            </div>
        </div>
    </div>
    <br>
    <br>
    <div>
        <center>
            <!-- <button class="btn btn-lg btn-info col-5" @click="obterLista()"><span class="oi oi-reload"></span> Atualizar Lista</button> -->
            <button class="btn btn-lg btn-warning col-3" @click="exportarCSV()" title="Exportar arquivo CSV (Excel)"><span class="oi oi-spreadsheet"></span> Exportar planilha</button>
        </center>
    </div>
    <!-- ALERTA DE OBTENÇÃO DE LISTA -->
    <!-- <div id="mensagem-alerta" v-if="alerta"><span>{{ mensagemAlerta }}</span></div> -->
    <div id="modal-alerta" class="modal" tabindex="-1" role="dialog" aria-labelledby="alerta" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aguarde um momento</h5>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
            </div>
            <div class="modal-body">
                <p>{{ mensagemAlerta }}</p>
            </div>          
        </div>
      </div>
    </div>
    <!-- DOAÇÕES ADICIONADAS -->
    <div id="div-tabela" class="table-responsive" style="resize: both; overflow-x: unset;">
        <h2>Doações cadastradas</h2>
        <div id="bt_mostrar_todas" class="opcoesFiltro">
            <input id="mostrarTodas" type="checkbox" name="mostrarTodas" v-model="mostrarTodas">
            <label for="mostrarTodas">Mostrar todas</label>
        </div>
        <div id="bt_apenas_sei" class="opcoesFiltro" v-if="apenasDadosSei()">            
            <input id="apenasSei" type="checkbox" v-model="ocultarNotSei">
            <label for="apenasSei">Ocultar campos não relacionados ao SEI</label>
        </div>
        <div id="soma-total">Soma total: <span>{{ somaTotal() }}</span></div>
        <table class="table table-striped mb-5">
            <tr>
                <th>#</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">DATA 1º CONTATO</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">ENTRADA DOAÇÃO</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">RESPONSÁVEL ATENDIMENTO</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">DOADOR</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">TIPO DE FORMALIZAÇÃO</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">CONTATO DOADOR</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">TELEFONE DOADOR</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">E-MAIL DOADOR</th>
                <!-- ITENS -->
                <th v-if="!apenasDadosSei() || !ocultarNotSei">ITENS</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">TIPO DE ITEM</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">CATEGORIA ITEM</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">DESCRIÇÃO DO ITEM</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">QUANTIDADE</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">UNIDADE</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">DESTINO DA DOAÇÃO</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">LOCAL DE DESTINAÇÃO (ENDEREÇO)</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">RESPONSÁVEL PELO RECEBIMENTO</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">ENTREGA</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">DISTRIBUIÇÃO</th>
                <!-- FIM ITENS -->
                <th v-if="!apenasDadosSei() || !ocultarNotSei">VALOR TOTAL DA DOAÇÃO</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">VALIDADE DOAÇÃO</th>
                <th v-if="!apenasDadosSei() || !ocultarNotSei">STATUS</th>
                <th>Nº DO SEI</th>
                <th>RELATÓRIO DO PROCESSO SEI</th>
                <th>ITENS PENDENTES NO PROCESSO SEI</th>
                <th>OBSERVAÇÃO</th>
                <th>Conferir/Atualizar</th>
                <th>Excluir</th>
            </tr>            
            <!-- <tr v-for="item in itens" :class="item.conferido ? 'table-success' : ''"> -->
            <tr v-for="item in itens" v-if="!item.blocked || mostrarTodas">
                <td>{{itens.indexOf(item)+1}}</td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei"><input class="form-control" v-model="item.data_entrada" placeholder="Data de entrada" title="Data de entrada" type="date" :disabled="item.blocked"></td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei"><input class="form-control w-100" v-model="item.entrada" placeholder="Entrada" title="Entrada" :disabled="item.blocked"></td>                
                <td v-if="!apenasDadosSei() || !ocultarNotSei">
                    <select class="form-control" v-model="item.responsavel_atendimento" title="Gestor / Responsável Atendimento" :disabled="nivelAcesso !== 'total'">
                        <option v-for="gestor in responsaveis_atendimento">{{gestor}}</option>
                    </select>
                </td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei"><input class="form-control" v-model="item.doador" placeholder="Doador" title="Doador" :disabled="item.blocked"></td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei">
                    <select v-model="item.tipo_formalizacao" class="form-control" title="Tipo de Formalização" style="min-width: 200px" :disabled="item.blocked">
                        <!-- <option disabled selected value="">Tipo de formalização</option> -->
                        <option>Pessoa física</option>
                        <option>Pessoa jurídica</option>
                        <option>Entidade religiosa</option>
                        <option>Entidade não governamental</option>
                    </select>
                </td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei"><input class="form-control" v-model="item.contato" placeholder="Contato" title="Contato" :disabled="item.blocked"></td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei"><input class="form-control" v-model="item.telefone_doador" placeholder="Telefone Doador (11) 1234-5678" title="Telefone Doador (11) 1234-5678" :disabled="item.blocked"></td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei"><input class="form-control" v-model="item.email_doador" placeholder="E-mail Doador" title="E-mail Doador" :disabled="item.blocked"></td>
 
                <!-- DOACAO_ITENS  -->
                <td v-if="!apenasDadosSei() || !ocultarNotSei">
                    <!-- ADICIONAR OU REMOVER DOACAO_ITEM -->
                    <div class="doacao-item" v-for="(doacao_item, item_index) in item.doacao_itens">                        
                        <button class="btn btn-danger" v-if="item.doacao_itens.length > 1" @click="removeDoacaoItem(item_index, itens.indexOf(item))" :disabled="item.blocked">X</button>
                    </div>
                    <br>
                    <button class="btn btn-outline-secondary bt-adiciona-item" 
                    @click="adicionaDoacaoItem(item)" :disabled="item.blocked">Adicionar item</button>
                </td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei">
                    <!-- TIPO DE ITEM -->
                    <div class="doacao-item" v-for="doacao_item in item.doacao_itens">
                        <select
                            v-model="doacao_item.tipo_item"   
                            class="form-control"
                            style="min-width: 130px"
                            title="Tipo de item"
                            :disabled="item.blocked"
                            >
                            <option disabled selected value="">Tipo de item</option>
                            <option v-for="i in tiposItem">{{i.tipo}}</option>
                        </select>
                    </div>
                </td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei">
                    <!-- CATEGORIA -->
                    <div class="doacao-item" v-for="doacao_item in item.doacao_itens">
                        <select class="form-control" v-model="doacao_item.categoria_item" title="Categoria do item" :disabled="item.blocked">
                            <option disabled selected value="">Categoria</option>                 
                            <option v-if="doacao_item.tipo_item == categoria.tipo" v-for="categoria in categoriasTipoitem">{{categoria.nome}}</option>
                            <option>Outros</option>
                        </select>
                    </div>
                </td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei"><div class="doacao-item" v-for="doacao_item in item.doacao_itens"><input class="form-control" v-model="doacao_item.descricao_item" placeholder="Descrição do Item" title="Descrição do Item" :disabled="item.blocked"></div></td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei"><div class="doacao-item" v-for="doacao_item in item.doacao_itens"><input class="form-control" v-model="doacao_item.quantidade" placeholder="Quantidade" title="Quantidade" @keyup="corrigeNumberType(doacao_item, 'quantidade')" :disabled="item.blocked"></div></td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei">
                    <div class="doacao-item" v-for="doacao_item in item.doacao_itens">
                        <select v-model="doacao_item.unidade_medida" class="form-control" title="Unidade de medida" style="min-width: 100px" :disabled="item.blocked">
                            <option selected disabled value="">Unidade de medida</option>
                            <option v-for="unidade in unidadesDeMedida">{{ unidade }}</option>
                        </select>
                    </div>
                </td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei"><div class="doacao-item" v-for="doacao_item in item.doacao_itens"><input class="form-control" v-model="doacao_item.destino" placeholder="Destino da doação" title="Destino da doação" :disabled="item.blocked"></div></td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei"><div class="doacao-item" v-for="doacao_item in item.doacao_itens"><input class="form-control" v-model="doacao_item.endereco_entrega" placeholder="Local de Destinação (Endereço)" title="Local de Destinação (Endereço)" :disabled="item.blocked"></div></td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei"><div class="doacao-item" v-for="doacao_item in item.doacao_itens"><input class="form-control" v-model="doacao_item.responsavel_recebimento" placeholder="Responsável pelo recebimento da doação" title="Responsável pelo recebimento da doação" :disabled="item.blocked"></div></td>
                
                <!-- ENTREGAS -->
                <td v-if="!apenasDadosSei() || !ocultarNotSei">
                    <div class="doacao-item" v-for="doacao_item in item.doacao_itens">
                        <div v-for="(entrega, index) in doacao_item.recebimentos" class="form-row my-1 lista-interna">
                            <span class="badge badge-light">{{ index+1 }}</span>
                            <div class="input-group input-group-sm col">                            
                                <span class="badge badge-light" style="font-size: 11px">Data de recebimento</span>
                                <input class="form-control form-control-sm"
                                v-model="entrega.data_recebimento"
                                type="date" 
                                 :disabled="item.blocked">
                            </div>
                            <div class="input-group input-group-sm col">
                                <span class="badge badge-light" style="font-size: 11px">Quantidade</span>
                                <input class="form-control form-control-sm"
                                v-model="entrega.qtde_recebida"
                                placeholder="Qtde recebida" title="Qtde recebida"
                                @keyup.prevent="corrigeNumberType(entrega, 'qtde_recebida')"
                                @change="calculaSaldos(doacao_item)"
                                 :disabled="item.blocked">
                            </div>
                            <div class="input-group input-group-sm col">
                                <button type="button" title="Remover entrega" class="btn btn-danger btn-sm" @click="confirm('***************ATENÇÃO!***************\n\nTem certeza que deseja remover a entrega? (esta ação não pode ser desfeita!)') ? removeEntregaDist(entrega, doacao_item.recebimentos, index, doacao_item) : false" :disabled="item.blocked">
                                    <span class="oi oi-x"></span>
                                </button>
                            </div>
                        </div>
                        <br>
                        <button class="btn btn-primary float-left" @click="doacao_item.recebimentos.push({data_recebimento:'',qtde_recebida:''})" :disabled="item.blocked">Adicionar entrega</button>
                        <div class="float-right" v-if="doacao_item.quantidade && (doacao_item.saldo_residual === 0 || doacao_item.saldo_residual > 0)">
                            <span>Saldo residual: {{ doacao_item.saldo_residual }}</span>
                        </div>
                    </div>
                </td>
                <!-- DISTRIBUIÇÕES -->
                <td v-if="!apenasDadosSei() || !ocultarNotSei">
                    <div class="doacao-item" v-for="doacao_item in item.doacao_itens">
                        <div v-for="(distribuicao, index) in doacao_item.distribuicoes" class="form-row my-1 lista-interna">                        
                            <span class="badge badge-light">{{ index+1 }}</span>
                            <div class="input-group input-group-sm col">
                                <span class="badge badge-light" style="font-size: 11px">Data de distribuição</span>
                                <input class="form-control form-control-sm"
                                v-model="distribuicao.data_distribuicao"
                                type="date" 
                                 :disabled="item.blocked">
                            </div>
                            <div class="col">
                                <span class="badge badge-light" style="font-size: 11px">Quantidade</span>
                                <input class="form-control form-control-sm"
                                v-model="distribuicao.qtde_distribuicao"
                                placeholder="Qtde distribuição" title="Qtde distribuição"
                                @keyup.prevent="corrigeNumberType(distribuicao, 'qtde_distribuicao')"
                                @change="calculaSaldos(doacao_item)"
                                 :disabled="item.blocked">
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-danger btn-sm" @click="confirm('Tem certeza que deseja remover a distribuição?') ? removeEntregaDist(distribuicao, doacao_item.distribuicoes, index, doacao_item) : false" :disabled="item.blocked">
                                    <span class="oi oi-x"></span>
                                </button>
                            </div>
                        </div>
                        <br>
                        <button class="btn btn-primary" @click="doacao_item.distribuicoes.push({data_distribuicao: '',qtde_distribuicao:''})" :disabled="item.blocked">Adicionar distribuição</button>
                        <div class="float-right" v-if="doacao_item.saldo_a_distribuir">
                            <span>Saldo a distribuir: {{ doacao_item.saldo_a_distribuir }}</span>
                        </div>
                    </div>
                </td>
                <!-- FIM DOACAO_ITENS -->

                <td v-if="!apenasDadosSei() || !ocultarNotSei"><input class="form-control" v-model="item.valor_total" title="Valor total" :disabled="nivelAcesso !== 'total'"><div class="valor_mask">{{corrigeValor(item.valor_total)}}</div></td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei"><input class="form-control" v-model="item.validade_doacao" placeholder="Validade Doação" title="Validade Doação" :disabled="item.blocked"></td>
                <td v-if="!apenasDadosSei() || !ocultarNotSei">
                    <select id="status" v-model="item.status" class="form-control" style="min-width: 200px" title="Status" :disabled="item.blocked">
                        <option disabled :value="null">Status</option>                                    
                        <option v-for="status in statuses">{{status}}</option>
                    </select>
                </td>
                <td><input class="form-control" v-model="item.numero_sei" placeholder="Número SEI" title="Número SEI" :disabled="item.blocked" style="min-width: 200px"></td>
                <td><textarea class="form-control" v-model="item.relatorio_sei" placeholder="Relatório do processo SEI" title="Relatório do processo SEI" :disabled="item.blocked"></textarea></td>
                <td><textarea class="form-control" v-model="item.itens_pendentes_sei" placeholder="Itens pendentes no processo SEI" title="Itens pendentes no processo SEI" :disabled="item.blocked"></textarea></td>
                <td><textarea class="form-control" v-model="item.observacao" placeholder="Observação" title="Observação" :disabled="item.blocked"></textarea></td>
                <!-- BOTÃO PARA CONFIRMAR ITEM -->
                <td>
                    <center>                        
                        <button type="button" class="btn btn-success btn-sm" @click="conferir(item)" title="Conferir/Atualizar" :disabled="item.blocked">
                            <span :class="item.conferido ? 'oi oi-loop-circular' : 'oi oi-check'"></span>
                        </button>
                    </center>
                </td>
                <!-- BOTÃO PARA REMOVER ITEM -->
                <td>
                    <center>
                        <button type="button" class="btn btn-danger btn-sm" @click="confirm('***************ATENÇÃO!***************\n\nTem certeza que deseja remover o item do cadastro? (esta ação não pode ser desfeita!)') ? remover(item) : false" title="Remover item" :disabled="item.blocked">
                            <span class="oi oi-x"></span>
                        </button>
                    </center>
                </td>
            </tr>
        </table>        
    </div>
</div>
    
<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="js/lodash.min.js"></script>
<script type="text/javascript" src="js/popper.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/sisprops.json?updated=20200507"></script>

<!-- Vue.js -->
<script>
    const fiscal = {
        nome: "<?php echo $_SESSION['nomeUsuario']; ?>"
    }
    const ItemDoacaoObj = {
        tipo_item: '',
        categoria_item: '',
        descricao_item: '',
        destino: '',
        endereco_entrega: '',
        responsavel_recebimento: '',
        quantidade: null,
        unidade_medida: '',
        entregas: [],
        distribuicoes: []
    }
    var app = new Vue({
        el: '#app',
        data: {
            itens: [],
            hItens: [],
            usuario: {
                nome: "<?=$_SESSION['nomeUsuario'];?>",
                rf: "<?=$_SESSION['IDUsuario'];?>"
            },
            categoriasTipoitem: [],
            editaResps: <?=$editaResps;?>,
            nivelAcesso: "<?=$nivel_acesso;?>",
            responsaveis_atendimento: <?=$responsaveis;?>,
            statuses: sisprops.statuses,
            tiposItem: sisprops.tiposItem,
            unidadesDeMedida: sisprops.unidadesDeMedida,
            mostrarTodas: true,
            ocultarNotSei: true,
            alerta: false,
            mensagemAlerta: '',
            allOtherBlocked: false
        },
        methods: {
            /** ADICIONA ITEM À DOAÇÃO */
            adicionaDoacaoItem: function (doacao) {
                console.log(doacao);
                doacao.doacao_itens.push(JSON.parse(JSON.stringify(ItemDoacaoObj)));
            },
            /** REMOVE ITEM DA DOAÇÃO */
            removeDoacaoItem: function (indiceItem, indiceDoacao) {
                let itemDoacao = this.itens[indiceDoacao].doacao_itens[indiceItem];
                if (itemDoacao.id > 0) {
                    // DOACAO_ITEM JÁ EXISTE NO BANCO
                    this.remover(itemDoacao);
                }
                else {
                    this.itens[indiceDoacao].doacao_itens.splice(indiceItem, 1);
                }
            },
            /** LIMPA NÚMEROS */
            apenasNumeros: function (string){
                var numsStr = string.replace(/[^0-9]/g,'');
                return numsStr;
            },
            /** VERIFICA SE DOAÇÃO ESTÁ BLOQUEADA PARA EDIÇÃO **/
            isBlocked: function (responsavel){
                if(this.nivelAcesso === 'total' || this.nivelAcesso === 'admin'){
                    return false;
                }

                for (var i = 0; i < this.editaResps.length; i++) {
                    if (this.editaResps[i] === responsavel){
                        return false;
                    }
                }

                return true;
            },
            /** VERIFICA SE USUÁRIO PODE ALTERAR APENAS INFORMAÇÕES REFERENTES AO SEI **/
            apenasDadosSei: function (){
                if(this.usuario.rf === 'd841268'){
                    // Dallmann
                    return true;
                }
                return false;
            },
            /** EXPORTA CSV */
            exportarCSV: function () {
                fiscal.rf = this.usuario.rf;
                window.location = ('exportar-csv.php?rf='+fiscal.rf);
            },
            ligaModal: function(mensagem) {
                this.mensagemAlerta = mensagem;
                this.alerta = true;
                $('#modal-alerta').modal('show');
            },
            desligaModal: function() {                
                app.alerta = false;
                window.setTimeout(function(){
                    $('#modal-alerta').modal('hide');
                    app.mensagemAlerta = "";
                }, 100);
            },
            blockOutrosItens: function(item, desbloquear = false) {
                if(this.allOtherBlocked) {
                    return;
                }
                for(var i = this.itens.length-1; i >= 0; i--) {
                    if (this.itens[i].id !== item.id) {
                        this.itens[i].blocked = !desbloquear;
                    }
                }
                this.allOtherBlocked = true;
            },
            /** ADIÇÃO DE ITENS À LISTA */
            obterLista: function (){
                this.ligaModal("Recarregando lista de doações...");
                fiscal.rf = this.usuario.rf;
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        app.itens = JSON.parse(this.response);
                        for (var i = app.itens.length - 1; i >= 0; i--) {
                            app.itens[i].blocked = app.isBlocked(app.itens[i].responsavel_atendimento);
                            // DEBUG: Identifica valores null
                            app.itens[i].valor_total = app.itens[i].valor_total ? app.itens[i].valor_total : 0;
                            let itemNameIndex = 'item_'+i;
                            app[itemNameIndex] = app.itens[i];
                            // ADICIONA WATCHER
                            app.$watch(itemNameIndex, (newVal, oldVal) => {
                                if(app.allOtherBlocked){
                                    return;
                                }
                                app.blockOutrosItens(oldVal);
                                app.$forceUpdate();
                                console.log("Block Others");
                            }, {deep: true});
                        }
                        app.allOtherBlocked = false;
                        app.desligaModal();
                    }
                };
                xhttp.open("POST", "conferir.php", true);
                xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xhttp.send("fiscal="+JSON.stringify(fiscal));
                console.log("Lista obtida.");
            },
            calculaSaldos: function(item) {
                let residual = item.quantidade;
                for (var i = 0; i < item.recebimentos.length; i++) {
                    if(!isNaN(parseFloat(item.recebimentos[i].qtde_recebida)))
                        residual -= parseFloat(item.recebimentos[i].qtde_recebida);
                }
                item.saldo_residual = residual;

                let aDistribuir = item.quantidade - residual;
                for (var i = 0; i < item.distribuicoes.length; i++) {
                    if(!isNaN(parseFloat(item.distribuicoes[i].qtde_distribuicao))){
                        aDistribuir -= parseFloat(item.distribuicoes[i].qtde_distribuicao);
                    }
                    else {
                        aDistribuir = 0;
                    }
                }
                item.saldo_a_distribuir = aDistribuir;
            },
            corrigeValor: function(valor) {
                valor = valor?.toString() || '0';
                // Se o valor contiver centavos, converte ponto em vírgula
                if (valor.indexOf('.') >= 0) {
                    valor = valor.replace('.', ',');
                }
                else if (valor.length > 0 && valor.indexOf(',') < 0) {
                    valor += ',00';
                }

                valor = "R$ " + valor.replace('R$ ', '');
                return valor;
            },
            corrigeValores: function() {
                // TODO: OTIMIZAR FUNÇÃO PARA TRABALHAR DE FORMA ASSINCRONA
                for (var i = 0; i < app.itens.length; i++) {
                    // Se o valor contiver centavos, converte ponto em vírgula
                    app.itens[i].valor_total = app.itens[i].valor_total.toString();
                    if (app.itens[i].valor_total.indexOf('.') >= 0) {
                        app.itens[i].valor_total = app.itens[i].valor_total.replace('.', ',');
                    }
                    else if (app.itens[i].valor_total.length > 0 && app.itens[i].valor_total.indexOf(',') < 0) {
                        app.itens[i].valor_total += ',00';
                    }

                    app.itens[i].valor_total = "R$ " + app.itens[i].valor_total.replace('R$ ', '');
                }
            },
            corrigeNumberType: function(objeto, prop) {
                // Verifica se número colado está no padrão brasileiro de pontuação e corrige de acordo
                let numero = objeto[prop].toString().replace(/[^0-9.,]/g,'');
                if(numero.match(/\,00/g) && numero.match(/\,00/g).length == 1) {
                    numero = numero.replace(",00", '').replace(/\./g, '');
                }
                if (numero.match(/\./g) && numero.match(/\./g).length > 1) {
                    numero = numero.replace(/\./g, '');
                }
                if (numero.match(/\,/g) && numero.match(/\,/g).length > 1) {
                    numero = numero.replace(/\,/g, '')
                }
                numero = numero.replace(',','.');

                objeto[prop] = numero;
            },
            consertaMoeda: function(valor) {
                if(valor.indexOf(',') > 0){
                    if(valor.length - 3 !== valor.indexOf(',')) {
                        window.alert("Valor total da doação INVÁLIDO. Por favor, verifique se o valor está correto.");
                        return false;
                    }
                    else {
                        valor = parseFloat(valor.replace(/R/g, '').replace(/\$/g, '').replace(/\./g, "").replace(",", "."));
                        return valor;
                    }                    
                }
                else {
                    return parseFloat(valor.replace(/R/g, '').replace(/\$/g, '').replace(/\./g, "").replace(",", "."));
                }
            },
            conferir: function(itemConferido) {
                this.ligaModal("Enviando informações...");
                let adicionarVirgulas = false;
                // remove propriedades virtuais
                delete itemConferido.blocked; 
                delete itemConferido.saldo_residual;
                delete itemConferido.saldo_a_distribuir;
                delete itemConferido.data_inclusao;
                delete itemConferido.data_alteracao;
                delete itemConferido.id_responsavel;

                itemConferido.conferido = this.usuario.nome+' - '+this.usuario.rf;

                if(itemConferido.valor_total.length > 0) {
                    itemConferido.valor_total = this.consertaMoeda(itemConferido.valor_total)
                }

                // REMOVE PROPRIEDADES QUE O USUÁRIO NÃO PODE ALTERAR
                if(this.apenasDadosSei()) {
                    delete itemConferido.conferido;
                    delete itemConferido.data_entrada;
                    delete itemConferido.entrada;
                    delete itemConferido.responsavel_atendimento;
                    delete itemConferido.doador;
                    delete itemConferido.tipo_formalizacao;
                    delete itemConferido.contato;
                    delete itemConferido.telefone_doador;
                    delete itemConferido.email_doador;
                    delete itemConferido.doacao_itens;
                    delete itemConferido.valor_total;
                    delete itemConferido.validade_doacao;
                    delete itemConferido.status;
                }

                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        app.obterLista();
                    }
                };
                xhttp.open("PUT", "conferir.php", true);
                xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xhttp.send(JSON.stringify(itemConferido));
            },
            remover: function(itemRemovido) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        if(this.response === '11'){
                            app.obterLista();
                            return itemRemovido;
                        }
                        else {
                            window.alert('Erro ao remover item! Contate o desenvolvedor.');
                            return false;
                        }
                    }
                };
                xhttp.open("DELETE", "conferir.php", true);
                xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xhttp.send(itemRemovido.id+".-."+this.usuario.rf+".-."+JSON.stringify(itemRemovido));
            },
            removeEntregaDist: function(itemRemovido, arrayPai, indice, item) {
                if (itemRemovido.id && itemRemovido.id > 0) {
                    this.remover(itemRemovido);
                }
                arrayPai.splice(indice, 1);
                this.calculaSaldos(item);
            },
            somaTotal: function() {
                let somaValor = 0;
                for (var i = 0; i < this.itens.length; i++) {
                    somaValor += parseFloat(this.itens[i].valor_total);
                }
                return somaValor.toLocaleString("pt-BR",{style:'currency', currency:'BRL', minimumFractionDigits: '2', maximumFractionDigits: '2'});
            },
            atualizaValor: function(item) {
                console.log("CONFERIR...");
                console.log(item);
                this.conferir(item);
            }
        },
        mounted: function() {
            // Especifica nome de usuário conforme tabela de responsáveis
            this.editaResps.push('<?=$usrRespName?>');
            // Obtem lista de doações
            this.obterLista();
            for (var i = 0; i < this.tiposItem.length; i++) {
                for (var j = 0; j < this.tiposItem[i].categorias.length; j++) {
                    this.categoriasTipoitem.push({nome: this.tiposItem[i].categorias[j], tipo: this.tiposItem[i].tipo});
                }
            }
        }
    });
</script>
<style>
label {
    margin: 1em auto 0;
}
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
textarea {
    min-width: 300px;
    min-height: 100px
}
#div-tabela table tbody tr td input {
    width: max-content;
}
.bt-adiciona-item {
    position: absolute;
    width: 350px;
    margin-top: -35px;
}
.check.icon {
  color: #000;
  position: absolute;
  margin-left: 3px;
  margin-top: 4px;
  width: 14px;
  height: 8px;
  border-bottom: solid 1px currentColor;
  border-left: solid 1px currentColor;
  -webkit-transform: rotate(-45deg);
          transform: rotate(-45deg);
}
.doacao-item {
    height: 60px;
    min-width: 40px;
}
.lista-interna {
    min-width: 420px;
    border: 1px solid white;
}
.lista-interna button {
    position: absolute;
    top: 25%;
}
#soma-total {
    width: 100%;
    font-size: 1.5em;
    color: black;
    position: fixed;
    background-color: rgba(243, 245, 245, 0.9);
    left: 0;
    bottom: 0;
    padding: 0.5em 1em;    
    box-shadow: 0px -3px 5px rgba(0,0,0,0.3);
    z-index: 3;
}
#soma-total span {
    color: #05b757;
    margin-left: 1em;
}
#mensagem-alerta {
    position: fixed;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    background-color: rgba(0,0,0,0.5);
    z-index: 3;
}
#mensagem-alerta span {
    position: absolute;
    display: block;
    background-color: white;
    padding: 2em;
    border-radius: 2px;
    font-size: 2em;
    color: black;
    text-align: center;
    width: 100%;
    top: calc(50% - 1em);
}
.opcoes-filtro {
    display: inline-block;
}
</style>

</body>
</html>
