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
        <title>SICABE - Conferência de Bens Patrimoniais</title>        
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
                <h1>Conferência de Bens Patrimoniais</h1>
            </div>
            <div class="col-3">
                <button class="btn btn-primary float-right" v-on:click="location.href='formulario.php'">
                    Cadastro de bens
                </button>
                <br><br>
                <button class="btn btn-danger btn-sm float-right" v-on:click="location.href='logout.php'">Sair do sistema</button>
            </div>
        </div>
    </div>
    <br>
    <br>
    <!-- BENS ADICIONADOS -->
    <div id="div-tabela" class="table-responsive">
        <h2>Bens registrados em {{fiscal.setor + (fiscal.divisao ? ('/'+fiscal.divisao) : '')}}</h2>
        <table class="table table-striped">
            <tr>
                <th>#</th>
                <th>Nome do Servidor</th>
                <th>Nº da chapa</th>
                <th>Nº chapa outra unidade</th>
                <th>Nome outra unidade</th>
                <th style="min-width: 300px;">Discriminação do bem</th>
                <th>Descrição personalizada</th>
                <th>Servível</th>
                <th>Cor</th>
                <th>Comprimento</th>
                <th>Profundidade</th>
                <th>Altura</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Nº de série</th>
                <th>Conferir</th>
                <th>Excluir</th>
            </tr>
            <tr v-for="item in itens" :class="item.conferido ? 'table-success' : ''">
                <td>{{itens.indexOf(item)+1}}</td>
                <td><input class="form-control" v-model="item.nomeServidor" disabled="true"></td>
                <td><input class="form-control" v-model="item.chapa" style="max-width: 150px"></td>
                <td><input class="form-control" v-model="item.chapaOutraUnidade" style="max-width: 150px"></td>
                <td><input class="form-control" v-model="item.nomeOutraUnidade" style="max-width: 100px"></td>
                <!-- <td><input class="form-control" v-model="item.discriminacao"></td> -->
                <td>
                    <select name="discriminacao" class="form-control" v-model="item.discriminacao" :title="item.discriminacao">
                        <option v-for="descritivo in descritivos">{{descritivo}}</option>
                        <option value="Não listado">Discriminação não listada...</option>
                    </select>
                </td>
                <td><input class="form-control" v-model="item.descricaoPersonalizada"></td>
                <td>                    
                    <input :id="item.id" type="checkbox" class="form-control" style="width: 30px; margin: auto;" v-model="item.servivel">
                    <!-- <label :for="item.id" :class="'badge form-check-label '+(item.servivel ? 'badge-success' : 'badge-danger')" style="font-size: 1em; margin: auto;">{{ item.servivel ? 'Servível' : 'Inservível' }}</label>                     -->
                </td>
                <td><input class="form-control" v-model="item.cor" :title="item.cor" style="min-width: 50px; max-width: 80px"></td>
                <td><input class="form-control" v-model="item.comprimento" style="max-width: 70px"></td>
                <td><input class="form-control" v-model="item.profundidade" style="max-width: 70px"></td>
                <td><input class="form-control" v-model="item.altura" style="max-width: 70px"></td>
                <td><input class="form-control" v-model="item.marca" style="max-width: 150px"></td>
                <td><input class="form-control" v-model="item.modelo"></td>
                <td><input class="form-control" v-model="item.numSerie"></td>                
                <!-- BOTÃO PARA CONFIRMAR ITEM -->
                <td>
                    <center>                        
                        <button type="button" class="btn btn-success btn-sm" v-on:click="conferir(item)">
                            <span :class="item.conferido ? 'oi oi-loop-circular' : 'oi oi-check'"></span>
                        </button>
                    </center>
                </td>
                <!-- BOTÃO PARA REMOVER ITEM -->
                <td>
                    <center>
                        <button type="button" class="btn btn-danger btn-sm" v-on:click="confirm('***************ATENÇÃO!***************\n\nTem certeza que deseja remover o item do cadastro? (esta ação não pode ser desfeita!)') ? itens.splice(itens.indexOf(remover(item)), 1) : false">
                            <span class="oi oi-x"></span>
                        </button>
                    </center>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <center>
        <button class="btn btn-lg btn-info col-5" v-on:click="obterLista()"><span class="oi oi-reload"></span> Atualizar Lista</button>
    </center>
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
        nome: "<?php echo $_SESSION['nomeUsuario']; ?>",
        setor: "<?php echo $_SESSION['setorFiscal']; ?>",
        divisao: "<?php echo $_SESSION['divisaoFiscal']; ?>"
    }
    const BemPatrimonial = class {
        constructor(){
            this.nomeServidor = '';
            this.rf = '';
            this.orgao = '';
            this.setor = '';
            this.divisao = '';
            this.sala = '';
            this.andar = '';
            this.chapa = '';
            this.chapaOutraUnidade = '';
            this.nomeOutraUnidade = '';
            this.discriminacao = '';
            this.cor = '';
            this.comprimento = '';
            this.profundidade = '';
            this.altura = '';
            this.marca = '';
            this.modelo = '';
            this.numSerie = '';
        }
    };
    var app = new Vue({
        el: '#app',
        data: {
            novoItem: new BemPatrimonial,
            itens: [],
            usuario: {
                nome: "<?php echo $_SESSION['nomeUsuario']; ?>",
                rf: "<?php echo $_SESSION['IDUsuario']; ?>"
            },
            descritivos: [
                '01 - Sofá de 3 lugares, em couro sintético',
                '02 - Rack para ti',
                '03 - Quadro de cortiça',
                '04 - Mesa "L", com uma das extremidades arredondada, pés em estrutura metálica',
                '05 - Mesa retangular, pés em estrutura metálica com rodas',
                '06 - Estação de trabalho, pés em estrutura metálica',
                '07 - Mesa de trabalho, pés em estrutura metálica',
                '08 - Mesa de reunião, pés em estrutura metálica',
                '09 - Mesa em L “60°”, pés em estrutura metálica',
                '10 - Mesa de impressora',
                '11 - Mapoteca vertical, estrutura em aço',
                '12 - Mapoteca horizontal',
                '13 - Longarina com assentos estrutura dos pés em aço',
                '14 - Gaveteiro com rodas, puxadores preto, 03 gavetas',
                '15 - Frigobar',
                '16 - Gaveteiro em estrutura metálica, com rodas, 02 gavetas',
                '17 - Estante em aço, prateleiras',
                '18 - Estante de aço, prateleiras',
                '19 - Estação de trabalho pés em estrutura metálica',
                '20 - Estação de trabalho, pés em estrutura metálica',
                '21 - Estação de trabalho, pés em estrutura metálica',
                '22 - Estação de trabalho, pés em estrutura metálica',
                '23 - Estação de trabalho, pés em madeira',
                '24 - Estação de trabalho, pés em madeira',
                '25 - Carrinho para transporte, 4 rodas, estrutura em arame soldado',
                '26 - Cadeira giratória, sem rodas, apoio de braço acoplado ao assento e encosto',
                '27 - Cadeira fixa sem apoio de braços',
                '28 - Cadeira giratória com apoio de braços',
                '29 - Cadeira fixa, encosto e pés de metal',
                '30 - Cadeira escolar adulto',
                '31 - Cadeira fixa sem apoio de braços',
                '32 - Cadeira giratória com apoio de braços',
                '33 - Cadeira giratória com apoio de braços',
                '34 - Cadeira fixa sem apoio de braços',
                '35 - Cadeira fixa com apoio de braços',
                '36 - Cadeira fixa com apoio de braços',
                '37 - Cadeira giratória com apoio de braços',
                '38 - Cadeira giratória sem apoio de braço, assento alto',
                '39 - Armário',
                '40 - Arquivo de aço com gavetas',
                '41 - Arquivos deslizante com carros',
                '42 - Mesa em L 90°, pés em madeira',
                '43 - Armário',
                '44 - Ar condicionado portátil',
                '45 - Ar condicionado central',
                '46 - Aparelho split para ar condicionado',
                '47 - Mesa de luz, v',
                '48 - Armário de aço',
                '49 - Armário com porta de vidro',
                '50 - Mesa com tampo de vidro',
                '51 - Mesa de apoio',
                '52 - Mesa em madeira maciça',
                '53 - Forno de micro-ondas'
            ],
            fotoUrl: '',
            orgaos: [],
            setores: [],
            divisoes: [],
            orgao: '',
            setor: '',
            divisao: '',
            andar: '',
            sala: ''
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
                ADIÇÃO DE ITENS À LISTA
            */
            obterLista: function (){
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        app.itens = JSON.parse(this.response);                        
                    }
                };
                xhttp.open("POST", "conferir.php", true);
                xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xhttp.send("fiscal="+JSON.stringify(fiscal));
                console.log("Lista obtida.");
            },
            conferir: function(itemConferido) {
                itemConferido.conferido = this.usuario.rf;
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        // app.itens = JSON.parse(this.response);
                        // console.log(this.response);
                        console.log(this.response === '1' ? "SUCESSO!" : this.response);
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
                        console.log(this.response === '1' ? "SUCESSO!" : this.response);
                        if(this.response === '1')
                            return itemRemovido;
                        else {
                            window.alert('Erro ao remover item! Contate o desenvolvedor.');
                            return false;
                        }
                    }
                };
                xhttp.open("DELETE", "conferir.php", true);
                xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xhttp.send(itemRemovido.id);                
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
