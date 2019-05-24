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
        <title>SICABE - Cadastro de Bens Patrimoniais</title>        
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
<div class="container" id="app">    
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
            <h1>Cadastro de Bens Patrimoniais</h1>
        </div>
        <div class="col-3">
            <button class="btn btn-danger float-right" v-on:click="location.href='logout.php'">Sair do sistema</button>
        </div>
    </div>
    <br>
    <br>
    <div class="container">
        <div id="dadosUsuario" class="card bg-light mb-3">
            <div class="card-header">
                <strong>Dados do servidor</strong>
            </div>
            <div class="card-body">
                <form class="form-group">
                    <div class="form-row">
                        <div class="col col-3">
                            <input 
                            class="form-control form-control-sm"
                            v-model="usuario.nome"
                            readonly="true"
                            :title="usuario.nome" 
                            >
                        </div>
                        <div class="col">
                            <input 
                            class="form-control form-control-sm"
                            v-model="usuario.rf"
                            readonly="true"                        
                            >
                        </div>
                        <div class="col">
                            <select
                            class="form-control form-control-sm"
                            name="orgao"
                            id="orgao"
                            v-model="orgao"
                            placeholder="Órgão"
                            >
                                <option value="" selected disabled>Órgão</option>
                                <option v-for="orgao in prefeitura.orgaos">{{orgao.sigla}}</option>
                            </select>
                        </div>
                        <div class="col">
                            <select
                            class="form-control form-control-sm"
                            name="setor"
                            id="setor"
                            v-model="setor"
                            placeholder="Setor"
                            :disabled="!orgao"
                            >
                                <option value="" selected>Setor</option>
                                <option v-for="setor in setores">{{setor.sigla}}</option>
                            </select>
                        </div>
                        <div class="col">
                            <select
                            class="form-control form-control-sm"
                            name="divisao"
                            id="divisao"
                            v-model="divisao"
                            placeholder="Divisão"
                            :disabled="!divisoes || !divisoes.length > 0"
                            >
                                <option value="" selected>Divisão</option>
                                <option v-for="divisao in divisoes">{{divisao.sigla}}</option>
                            </select>
                        </div>
                        <div class="col">
                            <input 
                            class="form-control form-control-sm"
                            v-model="sala"
                            placeholder="Sala"
                            >
                        </div>
                        <div class="col">
                            <!-- <input 
                            class="form-control form-control-sm"
                            v-model="usuario.andar"
                            type="number" 
                            placeholder="Andar"
                            > -->
                            <select
                            class="form-control form-control-sm"
                            name="andar"
                            id="andar"
                            v-model="andar"
                            placeholder="Andar"
                            >
                                <option value="" selected>Andar</option>
                                <option v-for="andar in andares">{{andar}}</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- ADICONAR BEM -->
        <div id="formulario" class="card bg-light mb-3">
            <div class="card-header"><strong>Adicionar Bem Patrimonial</strong></div>
        <div class="card-body">            
            <div class="form-group">
                <div class="form-row">
                    <div class="col">
                        <div class="form-row">
                            <div class="col">
                                <!-- <label for="chapa">Número da Chapa</label> -->
                                <input 
                                class="form-control form-control-sm"
                                v-model="novoItem.chapa"
                                id="chapa"
                                placeholder="Nº da chapa"
                                autocomplete="off"
                                >
                            </div>
                            <div class="input-group input-group-sm col col-8">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Chapa de outra unidade</span>
                                </div>
                                <!-- <div class="col"> -->
                                    <!-- <label for="chapaOutraUnidade">Nº da chapa</label> -->
                                    <input
                                    v-model="novoItem.chapaOutraUnidade"
                                    id="chapaOutraUnidade"
                                    aria-label="Nº da chapa"
                                    placeholder="Nº da chapa"
                                    class="form-control form-control-sm"
                                    autocomplete="off"
                                    >
                                <!-- </div> -->
                                <!-- <div class="col"> -->
                                    <!-- <label for="nomeOutraUnidade">Nome da unidade</label> -->
                                    <input v-model="novoItem.nomeOutraUnidade" id="nomeOutraUnidade" aria-label="Nome da unidade" placeholder="Nome da unidade" class="form-control form-control-sm">
                                <!-- </div> -->
                            </div>
                        </div>
                        <br>
                        <!-- DESCRIÇÃO -->
                        <!-- <label for="discriminacao">Discriminação do bem</label> -->
                        
                        <!-- MARCA/MODELO -->
                        <div class="form-row">                    
                            <div class="col">
                                <!-- <input type="text" class="form-control form-control-sm" v-model="novoItem.discriminacao" id="discriminacao" placeholder="Discriminação do bem"> -->
                                <select
                                class="form-control form-control-sm"
                                name="discriminacao"
                                id="discriminacao"
                                v-model="novoItem.discriminacao"
                                placeholder="Discriminação do bem"
                                @change="atualizaFoto()"
                                >
                                    <option value="undefined" selected disabled>Discriminação do bem</option>
                                    <option v-for="descritivo in descritivos">{{descritivo}}</option>
                                </select>
                            </div>                            
                        </div>
                        <br>
                        <div class="form-row">
                            <div class="col">
                                <input type="text" class="form-control form-control-sm" v-model="novoItem.cor" placeholder="Cor">
                            </div>
                            <div class="input-group input-group-sm col col-8">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Dimensões (cm)</span>
                                </div>                                
                                    <input
                                    v-model="novoItem.comprimento"
                                    placeholder="Comprimento"
                                    class="form-control form-control-sm"
                                    autocomplete="off"
                                    type="number"
                                    >
                                    <input
                                    v-model="novoItem.profundidade"
                                    placeholder="Profundidade"
                                    class="form-control form-control-sm"
                                    autocomplete="off"
                                    >
                                    <input
                                    v-model="novoItem.altura"
                                    placeholder="Altura"
                                    class="form-control form-control-sm"
                                    autocomplete="off"
                                    >
                            </div>
                        </div>
                        <br>
                        <div class="form-row">
                            <div class="col">
                                <!-- <label for="marca">Marca</label> -->
                                <input type="text" class="form-control form-control-sm" v-model="novoItem.marca" id="marca" placeholder="Marca">
                            </div>
                            <div class="col">
                                <!-- <label for="modelo">Modelo</label> -->
                                <input type="text" class="form-control form-control-sm" v-model="novoItem.modelo" id="modelo" placeholder="Modelo" title="Modelo">
                            </div>
                            <div class="col input-group">
                                <!-- <div class="input-group-prepend">
                                    <span class="input-group-text">Equipamento elétrico/eletrônico</span>
                                </div> -->
                                <input type="text" class="form-control form-control-sm" v-model="novoItem.numSerie" id="numSerie" placeholder="Nº de série (Equipamento elétrico/eletrônico)" title="Nº de série (Equipamento elétrico/eletrônico)">
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <img :src="fotoUrl" :alt="novoItem.discriminacao" class="mw-100">
                    </div>
                </div>
                <br>
                <button class="btn btn-primary" style="cursor: pointer;" v-on:click="adicionarItem()">Adicionar</button>
            </div>
        </div>
        </div>
        <br>
        <hr>
        <!-- BENS ADICIONADOS -->
        <div id="div-tabela">
            <h2>Bens adicionados</h2>
            <table class="table table-striped">
                <tr>
                    <th>Nº da chapa</th>
                    <th>Nº chapa outra unidade</th>
                    <th>Nome da unidade</th>
                    <th>Discriminação do bem</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Nº de série</th>
                    <th></th>
                </tr>
                <tr v-for="item in itens">
                    <td>{{item.chapa}}</td>
                    <td>{{item.chapaOutraUnidade}}</td>
                    <td>{{item.nomeOutraUnidade}}</td>
                    <td>{{item.discriminacao}}</td>
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
        </div>
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
            prefeitura: {
                orgaos: {
                    sel: {
                        nome: 'SEL - Secretaria Executiva de Licenciamento',
                        sigla: 'SEL',
                        setores: [                            
                            {sigla: 'Gabinete'},
                            {sigla: 'ASSEC'},
                            {sigla: 'ATEL'},                            
                            {
                                sigla: 'RESID',
                                divisoes: [
                                    {sigla: 'DRPM'},
                                    {sigla: 'DRGP'}
                                ]
                            },
                            {
                                sigla: 'COMIN',
                                divisoes: [
                                    {sigla: 'DCIMP'},
                                    {sigla: 'DCIGP'}
                                ]
                            },
                            {
                                sigla: 'SERVIN',
                                divisoes: [
                                    {sigla: 'DSIMP'},
                                    {sigla: 'DSIGP'}
                                ]
                            },
                            {
                                sigla: 'PARHIS',
                                divisoes: [
                                    {sigla: 'DHIS'},
                                    {sigla: 'DHMP'},
                                    {sigla: 'DPS'},
                                ]
                            },
                            {
                                sigla: 'SEGUR',
                                divisoes: [
                                    {sigla: 'DAE'},
                                    {sigla: 'DACESS'},
                                    {sigla: 'DLR'},
                                    {sigla: 'DMIS'}
                                ]
                            },
                            {
                                sigla: 'CASE',
                                divisoes: [
                                    {sigla: 'STEL'},
                                    {sigla: 'DCAD'},
                                    {sigla: 'DLE'},
                                    {sigla: 'DDU'}
                                ]
                            },
                            {
                                sigla: 'CGPATRI',
                                divisoes: [
                                    {sigla: 'Destinação'},
                                    {sigla: 'Informação'},
                                    {sigla: 'Engenharia'},
                                    {sigla: 'Avaliação'}
                                ]
                            },
                            {
                                sigla: 'GTEC',
                                divisoes: []
                            }
                        ]
                    },
                    smdu: {
                        nome: 'SMDU - Secretaria Municipal de Desenvolvimento Urbano',
                        sigla: 'SMDU',
                        setores: [
                            {sigla: 'Gabinete'},
                            {sigla: 'AJ'},
                            {sigla: 'AOC'},
                            {sigla: 'ASCOM'},
                            {sigla: 'ATIC'},
                            {sigla: 'ATU'},
                            {
                                sigla: 'CAF',
                                divisoes: [
                                    {sigla: 'DCL'},
                                    {sigla: 'DGP'},
                                    {sigla: 'DOF'},
                                    {sigla: 'DRV'},
                                    {sigla: 'DSUP'}
                                ]
                            },
                            {
                                sigla: 'CAP',
                                divisoes: [
                                    {sigla: 'DEPROT'},
                                    {sigla: 'DPCI'},
                                    {sigla: 'DPD'}
                                ]
                            },
                            {
                                sigla: 'CEPEUC',
                                divisoes: [
                                    {sigla: 'DCIT'},
                                    {sigla: 'DVF'},
                                    {sigla: 'DDOC'}
                                ]
                            },
                            {
                                sigla: 'DEUSO',
                                divisoes: [
                                    {sigla: 'DMUS'},
                                    {sigla: 'DNUS'},
                                    {sigla: 'DSIZ'}
                                ]
                            },
                            {
                                sigla: 'GEOINFO',
                                divisoes: [
                                    {sigla: 'DSIG'},
                                    {sigla: 'DAG'},
                                    {sigla: 'DAD'},
                                    {sigla: 'Observatorio de Indicadores'}
                                ]
                            },
                            {
                                sigla: 'PLANURBE',
                                divisoes: [
                                    {sigla: 'DMA'},
                                    {sigla: 'DOT'},
                                    {sigla: 'DART'}
                                ]
                            }
                        ]
                    }
                }
            },
            descritivos: [
                'Cadeira para escritório operacional',
                'Mesa retangular de vidro, com estrutura metálica'
            ],
            fotoUrl: '',
            orgaos: [],
            setores: [],
            divisoes: [],
            andares: [8,17,18,19,20,21,22],
            orgao: '',
            setor: '',
            divisao: '',
            andar: '',
            sala: ''
        },
        methods: {
            atualizaFoto: function(){
                for(i in this.descritivos){
                    // console.log("Descritivo: "+i+", texto: "+this.descritivos[i]);
                    if(this.novoItem.discriminacao === this.descritivos[i]){
                        let num = i++;
                        let addZero = num < 10 ? "0" : "";
                        this.fotoUrl = 'img/bens/'+addZero+i+'.jpg';
                    }
                }                
            },
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
            adicionarItem: function (){
                console.log(this.novoItem);
                // Insere dados do servidor
                this.novoItem.nomeServidor = this.usuario.nome;
                this.novoItem.rf = this.usuario.rf;
                this.novoItem.orgao = this.orgao;
                this.novoItem.setor = this.setor;
                this.novoItem.divisao = this.divisao;
                this.novoItem.sala = this.sala;
                this.novoItem.andar = this.andar;

                // Limpa números de chapa
                if(this.novoItem.chapa)
                    this.novoItem.chapa = this.apenasNumeros(this.novoItem.chapa);
                if(this.novoItem.chapaOutraUnidade)
                    this.novoItem.chapaOutraUnidade = this.apenasNumeros(this.novoItem.chapaOutraUnidade);


                // Insere item à lista de cadastro
                this.itens.push(this.novoItem);
                this.novoItem = new BemPatrimonial;
                document.getElementById("chapa").focus();
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
        },
        computed: {
            criarNovoItem: function(){
                this.novoItem = this.itemModel;
            }
        },
        watch: {
            orgao: function(){
                this.setores = [];
                this.setor = '';
                if (this.orgao.length > 0){
                    this.setores = this.prefeitura.orgaos[this.orgao.toLowerCase()].setores;
                }
            },
            setor: function(){
                this.divisoes = [];
                this.divisao = '';
                if (this.setor.length > 0){
                    let allSetores = this.prefeitura.orgaos[this.orgao.toLowerCase()].setores;
                    for (var i = 0; i < allSetores.length; i++) {
                        if (allSetores[i].sigla === this.setor){
                            this.divisoes = allSetores[i].divisoes;
                            return;
                        }
                    }
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
</style>

</body>
</html>
