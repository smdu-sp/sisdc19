<?php

session_start();

// Verifica se usuário está logado
/*
if($_SESSION["setorFiscal"] == ''){
    header('location: index.php');
    exit;
}

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
*/
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

// Query padrao
// $sqlQuery = "SELECT * FROM bens_patrimoniais;";
// $retornoQuery = $link->query($sqlQuery);

// $link->close();

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
                <h1>Conferência de registros de doações</h1>
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
    <!-- DOAÇÕES ADICIONADAS -->
    <div id="div-tabela" class="table-responsive" style="resize: both; overflow-x: unset;">
        <h2>Doações cadastradas</h2>
        <table class="table table-striped">
            <tr>
                <th>#</th>
                <th>DATA 1º CONTATO</th>
                <th>ENTRADA DOAÇÃO</th>
                <th>RESPONSÁVEL ATENDIMENTO</th>
                <th>DOADOR</th>
                <th>TIPO DE FORMALIZAÇÃO</th>
                <th>CONTATO DOADOR</th>
                <th>TELEFONE DOADOR</th>
                <th>E-MAIL DOADOR</th>
                <th>TIPO DE ITEM</th>
                <th>CATEGORIA ITEM</th>
                <th>DESCRIÇÃO DO ITEM</th>
                <th>STATUS</th>
                <th>DESTINO DA DOAÇÃO</th>
                <th>LOCAL DE DESTINAÇÃO (ENDEREÇO)</th>
                <th>RESPONSÁVEL PELO RECEBIMENTO</th>
                <th>QUANTIDADE</th>
                <th>UNIDADE</th>
                <th>VALOR TOTAL DA DOAÇÃO</th>
                <th>ENTRADA FRACIONADA</th>
                <th>ENTREGA</th>
                <th>DISTRIBUIÇÃO</th>
                <th>VALIDADE DOAÇÃO</th>
                <th>Nº DO SEI</th>                    
                <th>RELATÓRIO DO PROCESSO SEI</th>
                <th>ITENS PENDENTES NO PROCESSO SEI</th>
                <th>OBSERVAÇÃO</th>
                <th>Conferir/Atualizar</th>
                <th>Excluir</th>
            </tr>            
            <!-- <tr v-for="item in itens" :class="item.conferido ? 'table-success' : ''"> -->
            <tr v-for="item in itens">
                <td>{{itens.indexOf(item)+1}}</td>
                <td><input class="form-control" v-model="item.data_entrada" placeholder="Data de entrada" title="Data de entrada" type="date"></td>
                <td><input class="form-control w-100" v-model="item.entrada" placeholder="Entrada" title="Entrada"></td>
                <td><input class="form-control" v-model="item.responsavel_atendimento" placeholder="Responsável atendimento" title="Responsável atendimento"></td>
                <td><input class="form-control" v-model="item.doador" placeholder="Doador" title="Doador"></td>
                <td>
                    <select id="tipo_formalizacao" v-model="item.tipo_formalizacao" class="form-control" title="Tipo de Formalização" style="min-width: 200px">
                        <!-- <option disabled selected value="">Tipo de formalização</option> -->
                        <option>Pessoa física</option>
                        <option>Pessoa jurídica</option>
                        <option>Entidade religiosa</option>
                        <option>Entidade não governamental</option>
                    </select>
                </td>
                <td><input class="form-control" v-model="item.contato" placeholder="Contato" title="Contato"></td>
                <td><input class="form-control" v-model="item.telefone_doador" placeholder="Telefone Doador (11) 1234-5678" title="Telefone Doador (11) 1234-5678"></td>
                <td><input class="form-control" v-model="item.email_doador" placeholder="E-mail Doador" title="E-mail Doador"></td>
                <td>
                    <select
                        id="tipo_item"
                        v-model="item.tipo_item"   
                        class="form-control"
                        style="min-width: 130px"
                        title="Tipo de item"
                        >
                        <option disabled selected value="">Tipo de item</option>
                        <option v-for="i in tiposItem">{{i.tipo}}</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" v-model="item.categoria_item" title="Categoria do item">
                        <option disabled selected value="">Categoria</option>                 
                        <option v-if="item.tipo_item == categoria.tipo" v-for="categoria in categoriasTipoitem">{{categoria.nome}}</option>
                        <option>Outros</option>
                    </select>
                </td>
                <td><input class="form-control" v-model="item.descricao_item" placeholder="Descrição do Item" title="Descrição do Item"></td>
                <td>
                    <select id="status" v-model="item.status" class="form-control" style="min-width: 200px" title="Status">
                        <option disabled :value="null">Status</option>                                    
                        <option v-for="status in statuses">{{status}}</option>
                    </select>
                </td>
                <td><input class="form-control" v-model="item.destino" placeholder="Destino da doação" title="Destino da doação"></td>
                <td><input class="form-control" v-model="item.endereco_entrega" placeholder="Local de Destinação (Endereço)" title="Local de Destinação (Endereço)"></td>
                <td><input class="form-control" v-model="item.responsavel_recebimento" placeholder="Responsável pelo recebimento da doação" title="Responsável pelo recebimento da doação"></td>
                <td><input class="form-control" v-model="item.quantidade" placeholder="Quantidade" title="Quantidade" @keyup="corrigeNumberType(item, 'quantidade')"></td>
                <td>
                    <select v-model="item.unidade_medida" class="form-control" title="Unidade de medida" style="min-width: 100px">
                        <option selected disabled value="">Unidade de medida</option>
                        <option v-for="unidade in unidadesDeMedida">{{ unidade }}</option>
                    </select>
                </td>
                <td><input class="form-control" v-model="item.valor_total" title="Valor total"></td>
                <td>
                    <div class="form-row customRadio">
                        <div class="col">
                            <input id="nao_fracionada" type="radio" v-model="item.entrada_fracionada" value="0" title="Entrada fracionada">
                            <label for="nao_fracionada">Não</label>
                        </div>
                        <div class="col">
                            <input id="fracionada" type="radio" v-model="item.entrada_fracionada" value="1" title="Entrada fracionada">
                            <label for="fracionada">Sim</label>
                        </div>
                    </div>                    
                </td>
                <td>
                    <div v-for="(entrega, index) in item.recebimentos" class="form-row my-1 lista-interna">
                        <span class="badge badge-light">{{ index+1 }}</span>
                        <div class="input-group input-group-sm col">                            
                            <span class="badge badge-light" style="font-size: 11px">Data de recebimento</span>
                            <input class="form-control form-control-sm"
                            v-model="entrega.data_recebimento"
                            type="date" 
                            >
                        </div>
                        <div class="input-group input-group-sm col">
                            <span class="badge badge-light" style="font-size: 11px">Quantidade</span>
                            <input class="form-control form-control-sm"
                            v-model="entrega.qtde_recebida"
                            placeholder="Qtde recebida" title="Qtde recebida"
                            @keyup.prevent="corrigeNumberType(entrega, 'qtde_recebida')"
                            @change="calculaSaldos(item)"
                            >
                        </div>
                        <div class="input-group input-group-sm col">
                            <button type="button" title="Remover entrega" class="btn btn-danger btn-sm" @click="confirm('***************ATENÇÃO!***************\n\nTem certeza que deseja remover a entrega? (esta ação não pode ser desfeita!)') ? removeEntregaDist(entrega, item.recebimentos, index, item) : false">
                                <span class="oi oi-x"></span>
                            </button>
                        </div>
                    </div>
                    <br>
                    <button class="btn btn-primary float-left" @click="item.recebimentos.push({data_recebimento:'',qtde_recebida:''})">Adicionar entrega</button>
                    <div class="float-right" v-if="item.quantidade && (item.saldo_residual === 0 || item.saldo_residual > 0)">
                        <span>Saldo residual: {{ item.saldo_residual }}</span>
                    </div>
                </td>
                <td>
                    <div v-for="(distribuicao, index) in item.distribuicoes" class="form-row my-1 lista-interna">                        
                        <span class="badge badge-light">{{ index+1 }}</span>
                        <div class="input-group input-group-sm col">
                            <span class="badge badge-light" style="font-size: 11px">Data de distribuição</span>
                            <input class="form-control form-control-sm"
                            v-model="distribuicao.data_distribuicao"
                            type="date" 
                            >
                        </div>
                        <div class="col">
                            <span class="badge badge-light" style="font-size: 11px">Quantidade</span>
                            <input class="form-control form-control-sm"
                            v-model="distribuicao.qtde_distribuicao"
                            placeholder="Qtde distribuição" title="Qtde distribuição"
                            @keyup.prevent="corrigeNumberType(distribuicao, 'qtde_distribuicao')"
                            @change="calculaSaldos(item)"
                            >
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-danger btn-sm" @click="confirm('Tem certeza que deseja remover a distribuição?') ? removeEntregaDist(distribuicao, item.distribuicoes, index, item) : false">
                                <span class="oi oi-x"></span>
                            </button>
                        </div>
                    </div>
                    <br>
                    <button class="btn btn-primary" @click="item.distribuicoes.push({data_distribuicao: '',qtde_distribuicao:''})">Adicionar distribuição</button>
                    <div class="float-right" v-if="item.saldo_a_distribuir">
                        <span>Saldo a distribuir: {{ item.saldo_a_distribuir }}</span>
                    </div>
                </td>
                <td><input class="form-control" v-model="item.validade_doacao" placeholder="Validade Doação" title="Validade Doação"></td>
                <td><input class="form-control" v-model="item.numero_sei" placeholder="Número SEI" title="Número SEI"></td>
                <td><textarea class="form-control" v-model="item.relatorio_sei" placeholder="Relatório do processo SEI" title="Relatório do processo SEI"></textarea></td>
                <td><textarea class="form-control" v-model="item.itens_pendentes_sei" placeholder="Itens pendentes no processo SEI" title="Itens pendentes no processo SEI"></textarea></td>
                <td><textarea class="form-control" v-model="item.observacao" placeholder="Observação" title="Observação"></textarea></td>
                <!-- BOTÃO PARA CONFIRMAR ITEM -->
                <td>
                    <center>                        
                        <button type="button" class="btn btn-success btn-sm" @click="conferir(item)" title="Conferir/Atualizar">
                            <span :class="item.conferido ? 'oi oi-loop-circular' : 'oi oi-check'"></span>
                        </button>
                    </center>
                </td>
                <!-- BOTÃO PARA REMOVER ITEM -->
                <td>
                    <center>
                        <button type="button" class="btn btn-danger btn-sm" @click="confirm('***************ATENÇÃO!***************\n\nTem certeza que deseja remover o item do cadastro? (esta ação não pode ser desfeita!)') ? remover(item) : false" title="Remover item">
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
<script type="text/javascript" src="js/sisprops.json"></script>

<!-- Vue.js -->
<script>
    const fiscal = {
        nome: "<?php echo $_SESSION['nomeUsuario']; ?>"
    }
    var app = new Vue({
        el: '#app',
        data: {
            itens: [],
            usuario: {
                nome: "<?php echo $_SESSION['nomeUsuario']; ?>",
                rf: "<?php echo $_SESSION['IDUsuario']; ?>"
            },
            categoriasTipoitem: [],
            statuses: sisprops.statuses,
            tiposItem: sisprops.tiposItem,
            unidadesDeMedida: sisprops.unidadesDeMedida
        },
        methods: {
            /**
                LIMPA NÚMEROS
            */
            apenasNumeros: function (string){
                var numsStr = string.replace(/[^0-9]/g,'');
                return numsStr;
            },
            /**
                EXPORTA CSV
            */
            exportarCSV: function () {
                fiscal.rf = this.usuario.rf;
                window.location = ('exportar-csv.php?rf='+fiscal.rf);
            },
            /**
                ADIÇÃO DE ITENS À LISTA
            */
            obterLista: function (){
                // if(fiscal.setor === 'TODOS')
                //     return;

                // ADD para restringir lista de gabinete de SEL/SMDU
                fiscal.rf = this.usuario.rf;

                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        app.itens = JSON.parse(this.response);
                        app.corrigeValores();
                        for (var i = 0; i < app.itens.length; i++) {
                            if(app.itens[i].recebimentos.length > 0)
                                app.calculaSaldos(app.itens[i]);
                        }
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
            corrigeValores: function() {
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
                let adicionarVirgulas = false;
                itemConferido.conferido = this.usuario.nome+' - '+this.usuario.rf;
                /*
                if(itemConferido.numero_sei.length > 0)
                    itemConferido.numero_sei = this.apenasNumeros(itemConferido.numero_sei);
                */
                // console.log(itemConferido.valor_total.indexOf(','));

                if(itemConferido.valor_total.length > 0) {
                    itemConferido.valor_total = this.consertaMoeda(itemConferido.valor_total)
                    console.log("Valor: ", itemConferido.valor_total);
                    if(!itemConferido.valor_total)
                        return;
                }

                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log(this.response === '1' ? "SUCESSO!" : this.response);
                        app.obterLista();
                        app.corrigeValores();
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
                        console.log(this.response === '11' ? "SUCESSO!" : this.response);
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
            }
        },
        mounted: function() {
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
.lista-interna {
    min-width: 420px;
    border: 1px solid white;
}
.lista-interna button {
    position: absolute;
    top: 25%;
}
</style>

</body>
</html>
