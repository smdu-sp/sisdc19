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
                    clip-path: inset(0px 0px 20% 0px);
                    margin: 0 auto -30%;
                    max-width: 150px;
                    text-align: center;">
                    <img src="img/logo_smdu.png" alt="Cidade de São Paulo" style="max-width: 100%; max-height: 50%;">
                </div>            
            </div>
            <div class="col-6">
                <h1>Conferência registros de doações</h1>
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
                <th>ENTRADA</th>
                <th>DATA DE ENTRADA</th>
                <th>RESPONSÁVEL DO ATENDIMENTO / ANDAMENTO</th>
                <th>DOADOR</th>
                <th>TIPO DE FORMALIZAÇÃO</th>
                <th>DESCRIÇÃO DO ITEM</th>
                <th>TIPO DO ITEM</th>
                <th>QUANTIDADE</th>
                <th>VALOR TOTAL DA DOAÇÃO</th>
                <th>DESTINO DA DOAÇÃO</th>
                <th>CONTATO</th>
                <th>PRAZO ENTREGA / PERÍODO DISPONIBILIZAÇÃO</th>
                <th>ENDEREÇO DE ENTREGA</th>
                <th>RESPONSÁVEL PELO RECEBIMENTO DA DOAÇÃO</th>                
                <th>STATUS</th>
                <th>Nº DO SEI</th>                    
                <th>OBSERVAÇÃO</th>
                <th>COMENTÁRIO SMS</th>
                <th>BREVE RELATÓRIO DO PROCESSO SEI</th>
                <th>ITENS PENDENTES NO PROCESSO SEI</th>
                <th>MONITORAMENTO</th>
                <th>Conferir/ Corrigir</th>
                <th>Excluir</th>
            </tr>            
            <!-- <tr v-for="item in itens" :class="item.conferido ? 'table-success' : ''"> -->
            <tr v-for="item in itens">
                <td>{{itens.indexOf(item)+1}}</td>
                <td><input class="form-control w-100" v-model="item.entrada"></td>
                <td><input class="form-control" v-model="item.data_entrada" type="date"></td>
                <td><input class="form-control" v-model="item.responsavel_atendimento"></td>
                <td><input class="form-control" v-model="item.doador"></td>
                <td><input class="form-control" v-model="item.tipo_formalizacao"></td>
                <td><input class="form-control" v-model="item.descricao_item"></td>
                <td><input class="form-control" v-model="item.tipo_item"></td>
                <td><input class="form-control" v-model="item.quantidade"></td>
                <td><input class="form-control" v-model="item.valor_total"></td>
                <td><input class="form-control" v-model="item.destino"></td>
                <td><input class="form-control" v-model="item.contato"></td>
                <td><input class="form-control" v-model="item.prazo_periodo"></td>
                <td><input class="form-control" v-model="item.endereco_entrega"></td>
                <td><input class="form-control" v-model="item.responsavel_recebimento"></td>
                <td><input class="form-control" v-model="item.status"></td>
                <td><input class="form-control" v-model="item.numero_sei"></td>
                <!-- <td><input class="form-control" v-model="item.observacao"></td> -->
                <td><textarea class="form-control" v-model="item.observacao" style="min-width: 200px; min-height: 100px"></textarea></td>
                <td><input class="form-control" v-model="item.comentario_sms"></td>
                <td><textarea class="form-control" v-model="item.relatorio_sei" style="min-width: 300px; min-height: 100px"></textarea></td>
                <td><textarea class="form-control" v-model="item.itens_pendentes_sei" style="min-width: 200px; min-height: 100px"></textarea></td>
                <td><input class="form-control" v-model="item.monitoramento"></td>
                <!-- BOTÃO PARA CONFIRMAR ITEM -->
                <td>
                    <center>                        
                        <button type="button" class="btn btn-success btn-sm" @click="conferir(item)">
                            <span :class="item.conferido ? 'oi oi-loop-circular' : 'oi oi-check'"></span>
                        </button>
                    </center>
                </td>
                <!-- BOTÃO PARA REMOVER ITEM -->
                <td>
                    <center>
                        <button type="button" class="btn btn-danger btn-sm" @click="confirm('***************ATENÇÃO!***************\n\nTem certeza que deseja remover o item do cadastro? (esta ação não pode ser desfeita!)') ? remover(item) : false">
                            <span class="oi oi-x"></span>
                        </button>
                    </center>
                </td>
            </tr>
        </table>
        <!-- <div v-if="fiscal.setor === 'TODOS'" style="vertical-align: middle; margin: 6em auto; text-align: center;">
            <h4>Para agilizar a consulta, a tabela foi desativada. Clique no botão abaixo para visualizar a planilha:</h4>
        </div> -->
    </div>    
</div>
    
<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="js/lodash.min.js"></script>
<script type="text/javascript" src="js/popper.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<?php 

// SECRETARIA (full):
// $_SESSION["usrData"]['description'][0]
// Secretaria (completo): description
// Secretaria (sigla): physicaldeliveryofficename
// Nome (Completo): name

// echo "<script>const usrData = JSON.parse('".json_encode($_SESSION["usrData"])."');</script>" ?>
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
            }
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
                    }
                };
                xhttp.open("POST", "conferir.php", true);
                xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xhttp.send("fiscal="+JSON.stringify(fiscal));
                console.log("Lista obtida.");
            },
            corrigeValores: function() {
                for (var i = 0; i < app.itens.length; i++) {
                    // Se o valor contiver centavos, converte ponto em vírgula
                    app.itens[i].valor_total = app.itens[i].valor_total.toString();
                    // console.warn("FOR... ",i);
                    // console.log(app.itens[i].valor_total);
                    if (app.itens[i].valor_total.indexOf('.') >= 0) {
                        app.itens[i].valor_total = app.itens[i].valor_total.replace('.', ',');
                    }
                    else if (app.itens[i].valor_total.length > 0 && app.itens[i].valor_total.indexOf(',') < 0) {
                        app.itens[i].valor_total += ',00';
                    }
                }
            },
            conferir: function(itemConferido) {
                let adicionarVirgulas = false;
                itemConferido.conferido = this.usuario.nome+' - '+this.usuario.rf;
                if(itemConferido.numero_sei.length > 0)
                    itemConferido.numero_sei = this.apenasNumeros(itemConferido.numero_sei);
                // console.log(itemConferido.valor_total.indexOf(','));
                if(itemConferido.valor_total.length > 0 && itemConferido.valor_total.indexOf(',') < 0) {
                    itemConferido.valor_total = this.apenasNumeros(itemConferido.valor_total)/100;
                }

                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log(this.response === '1' ? "SUCESSO!" : this.response);
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
            }
        },
        mounted: function() {
            this.obterLista();
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
</style>

</body>
</html>
