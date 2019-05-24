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
    <div class="container">
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
            <div class="col">
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
    <div id="div-tabela">
        <h2>Bens adicionados</h2>
        <table class="table table-striped">
            <tr>
                <th>Nome do Servidor</th>
                <th>Nº da chapa</th>
                <th>Nº chapa outra unidade</th>
                <th>Nome outra unidade</th>
                <th>Discriminação do bem</th>
                <th>Cor</th>
                <th>Comprimento</th>
                <th>Profundidade</th>
                <th>Altura</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Nº de série</th>
                <th></th>
            </tr>
            <tr v-for="item in itens">
                <td>
                    <input class="form-control" v-model="item.nome">
                </td>
                <td>{{item.chapa}}</td>
                <td>{{item.chapaOutraUnidade}}</td>
                <td>{{item.nomeOutraUnidade}}</td>
                <td>{{item.discriminacao+' '+item.cor+', '+item.comprimento+'x'+item.profundidade+'x'+item.altura+'cm'}}</td>
                <td>{{item.marca}}</td>
                <td>{{item.modelo}}</td>
                <td>{{item.numSerie}}</td>
                <!-- BOTÃO PARA REMOVER ITEM -->
                <td>
                    <center>
                        <button type="button" class="btn btn-danger btn-sm" v-on:click="itens.splice(itens.indexOf(item), 1)">
                            <span style="font-size: 2em; line-height: 1">&times;</span>
                        </button>
                    </center>
                </td>
            </tr>
        </table>
        <br>
        <button class="btn btn-success btn-lg" v-on:click="cadastrarBens()">Enviar</button>
        <button v-on:click="obterLista()">OBTER LISTA</button>
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
                'Cadeira para escritório operacional',
                'Mesa retangular de vidro, com estrutura metálica'
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
                console.log("SHAZAM");
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log(this.response);
                        app.itens = this.JSON.parse(response);                        
                    }
                };
                xhttp.open("POST", "conferir.php", true);
                xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xhttp.send("fiscal="+JSON.stringify(fiscal));
                console.log("LISTA OBTIDA");
            },
            /** 
                CADASTRO DE BENS
            */
            cadastrarBens: function () {
                if(this.itens.length === 0){
                    window.alert("A lista está vazia! Adicione itens para cadastrar.");
                    return;
                }
                if(!this.orgao){
                    window.alert('Preencha o campo "Órgão"');
                    return;
                }
                if(!this.setor){
                    window.alert('Preencha o campo "Setor"');
                    return;
                }

                let listaDeBens = JSON.stringify(this.itens);
                console.log(listaDeBens);

                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        if(this.response > 0) {
                            // Cadastro realizado com sucesso. Atualiza lista
                            let concordancia = parseInt(this.response) > 1 ? " itens cadastrados" : " item cadastrado";
                            window.alert(parseInt(this.response)+concordancia+" com sucesso!");
                            app.novoItem = new BemPatrimonial;
                            app.itens = [];
                            // console.log();
                            document.getElementById("chapa").focus();
                        }                        
                        else {
                            window.alert("Falha ao cadastrar. Verifique os campos e tente novamente.\nSe o problema persistir, contate o desenvolvedor.");
                            console.warn(this.response);
                        }
                    }
                };
                xhttp.open("POST", "cadastrar.php", true);
                xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xhttp.send("insertList="+listaDeBens);
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
</style>

</body>
</html>
