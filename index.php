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
// $sqlQuery = "SELECT * FROM Doacoes_patrimoniais;";
// $retornoQuery = $link->query($sqlQuery);

// $link->close();

?>
<div class="container" id="app">    
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
		<div class="col">
			<h1>Doações decorrentes da pandemia do COVID-19</h1>
		</div>
		<div class="col-3">            
			<button class="btn btn-danger btn-sm float-right" @click="location.href='logout.php'">Sair do sistema</button>
			<br><br>
			<button class="btn btn-primary float-right" @click="location.href='conferencia.php'">
				Conferência de dados
			</button>
		</div>
	</div>
	<br>
	<br>
	<div class="container">		
		<!-- ADICIONAR DOAÇÃO -->
		<div id="formulario" class="card bg-light mb-3">
			<div class="card-header"><strong>Cadastrar doação</strong></div>
		<div class="card-body">            
			<div class="form-group">
				<div class="form-row">
					<div class="col">
						<!-- ENTRADA, DATA, RESPONSÁVEL, DOADOR, FORMALIZAÇÃO -->
						<div class="form-row">
							<div class="input-group input-group-sm col col-3">
								<div class="input-group-prepend">
									<span class="input-group-text">Data 1º contato</span>
								</div>								
								<input
									class="form-control form-control-sm"
									v-model="novoItem.data_entrada"
									placeholder="Data de entrada"
									type="date"
									>
							</div>
							<div class="col col-2">
								<input 
								class="form-control form-control-sm"
								v-model="novoItem.entrada"
								id="entrada"
								placeholder="Entrada"
								>
							</div>
							<div class="col col-2">
								<input
								class="form-control form-control-sm"
								v-model="novoItem.responsavel_atendimento"
								placeholder="Responsável atendimento"
								>
							</div>
							<div class="col col-3">
								<input 
								class="form-control form-control-sm"
								v-model="novoItem.doador"
								id="doador"
								placeholder="Doador"
								>
							</div>
							<div class="col col-2">
								<select id="tipo_formalizacao" v-model="novoItem.tipo_formalizacao" class="form-control form-control-sm">
									<option disabled selected value="">Tipo de formalização</option>
									<option>Pessoa física</option>
									<option>Pessoa jurídica</option>
									<option>Entidade religiosa</option>
									<option>Entidade não governamental</option>
								</select>
							</div>
						</div>
						<br>
                        <!-- CONTATO DOADOR -->
                        <div class="form-row">
                            <div class="col">
                                <input
                                class="form-control form-control-sm"
                                v-model="novoItem.contato"
                                placeholder="Contato"
                                >
                            </div>
                            <div class="col">
                                <input
                                class="form-control form-control-sm"
                                v-model="novoItem.telefone_doador"
                                placeholder="Telefone Doador (11) 1234-5678"
                                >
                            </div>
                            <div class="col">
                                <input
                                class="form-control form-control-sm"
                                v-model="novoItem.email_doador"
                                placeholder="E-mail Doador"
                                >
                            </div>
                        </div>
                        <br>
						<!-- TIPO, CATEGORIA E DESCRIÇÃO DO ITEM -->
						<div class="form-row">							
							<div class="col col-2">
								<select
                                id="tipo_item"
                                v-model="novoItem.tipo_item"   
                                @change="atualizaTipos()"
                                class="form-control form-control-sm">
									<option disabled selected value="">Tipo de item</option>
									<option v-for="i in tiposItem">{{i.tipo}}</option>
								</select>
							</div>
                            <div class="col col-2">
                                <select class="form-control form-control-sm" v-model="novoItem.categoria_item">
                                    <option disabled selected value="">Categoria</option>                 
                                    <option v-if="novoItem.tipo_item" v-for="categoria in categoriasTipoitem">{{categoria}}</option>
                                    <option>Outros</option>
                                </select>
                            </div>
                            <div class="col">
                                <input
                                class="form-control form-control-sm"
                                v-model="novoItem.descricao_item"
                                placeholder="Descrição do Item"
                                >
                            </div>
                        </div>
						<br>
                        <!-- STATUS, DESTINO, RECEBIMENTO -->
                        <div class="form-row">
                            <div class="col">
                                <select id="status" v-model="novoItem.status" class="form-control form-control-sm">
                                    <option disabled selected value="">Status</option>
                                    <option>Contato não iniciado</option>
                                    <option>Contato iniciado</option>
                                    <option>Em processo de formalização</option>
                                    <option>Aguardando entrega</option>
                                    <option>Produto/serviço entregue</option>
                                    <option>Finalizado com termo de recebimento</option>
                                    <option>Encerrado</option>
                                </select>
                            </div>
                            <div class="col">
                                <input
                                class="form-control form-control-sm"
                                v-model="novoItem.destino"
                                placeholder="Destino da doação"
                                >
                            </div>
                            <div class="col">
                                <input
                                class="form-control form-control-sm"
                                v-model="novoItem.endereco_entrega"
                                placeholder="Local de Destinação (Endereço)"
                                >
                            </div>
                            <div class="col">
                                <input
                                class="form-control form-control-sm"
                                v-model="novoItem.responsavel_recebimento"
                                placeholder="Responsável pelo recebimento da doação"
                                >
                            </div>
                        </div>
                        <br>
                        <!-- QUANTIDADE TOTAL DOADA, UNIDADE MEDIDA, VALOR TOTAL, FRACIONADA, QTDE ENTREGAS -->
                        <div class="form-row">
                            <div class="col col-2">
                                <input
                                class="form-control form-control-sm"
                                v-model="novoItem.quantidade"
                                placeholder="Quantidade"
                                type="number"
                                >
                            </div>
                            <div class="col col-2">
                                <select v-model="novoItem.unidade_medida" class="form-control form-control-sm">
                                    <option selected disabled value="">Unidade de medida</option>
                                    <option v-for="unidade in unidadesDeMedida">{{ unidade }}</option>
                                </select>
                            </div>
                            <div class="col input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">R$</span>
                                </div>
                                <input
                                class="form-control form-control-sm"
                                v-model="novoItem.valor_total"
                                placeholder="Valor total da doação"
                                title="Valor total (ex.: 999999,00)"
                                >
                            </div>
                            <!-- <div class="col"> -->
                            <div class="input-group input-group-sm col col-3">
                                <div class="input-group-prepend">
                                    <label class="input-group-text my-0" @click="novoItem.entrada_fracionada = novoItem.entrada_fracionada == 0 ? 1 : 0">Entrada fracionada?</label>
                                </div>
                                <div class="form-row customRadio">
                                    <div class="col">
                                        <input id="nao_fracionada" type="radio" v-model="novoItem.entrada_fracionada" value="0">
                                        <label for="nao_fracionada">Não</label>
                                    </div>
                                    <div class="col">
                                        <input id="fracionada" type="radio" v-model="novoItem.entrada_fracionada" value="1">
                                        <label for="fracionada">Sim</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <!-- QUANTIDADE DE ENTREGAS, DATAS RECEBIMENTO / DISTRIBUIÇÃO -->
                        <!-- TODO: CRIAR DIV COM INSERÇÃO DINÂMICA DE ITENS -->



						<!-- PRAZO ENTREGA, ENDEREÇO ENTREGA, REPONSÁVEL RECEBIMENTO -->
						<div class="form-row">
							<div class="col">
								<input
								class="form-control form-control-sm"
								v-model="novoItem.prazo_periodo"
								placeholder="Prazo entrega / Período disponibilização"
								>
							</div>
						</div>
						<br>
						<!-- STATUS, NUMERO SEI, OBSERVAÇÃO -->
						<div class="form-row">
							<div class="col col-2">
								<input
								class="form-control form-control-sm"
								v-model="novoItem.numero_sei"
								placeholder="Número SEI"
								>
							</div>
							<div class="col">
								<input
								class="form-control form-control-sm"
								v-model="novoItem.observacao"
								placeholder="Observação"
								>
							</div>
						</div>
						<br>
						<!-- COMENTÁRIO SMS, BREVE RELATÓRIO DO PROCESSO SEI, ITENS PENDENTES NO PROCESSO SEI, MONITORAMENTO -->
						<div class="form-row">
							<div class="col">
								<input 
								class="form-control form-control-sm"
								v-model="novoItem.comentario_sms"
								placeholder="Comentário SMS"
								>
							</div>							
							<div class="col">
								<input
								class="form-control form-control-sm"
								v-model="novoItem.relatorio_sei"
								placeholder="Breve relatório do processo SEI"
								>
							</div>
							<div class="col">
								<input
								class="form-control form-control-sm"
								v-model="novoItem.itens_pendentes_sei"
								placeholder="Itens pendentes no processo SEI"
								>
							</div>
							<div class="col">
								<input
								class="form-control form-control-sm"
								v-model="novoItem.monitoramento"
								placeholder="Monitoramento"
								>
							</div>
						</div>
					</div>					
				</div>
				<br>
				<button class="btn btn-primary float-left" style="cursor: pointer;" v-on:click="adicionarItem()">Adicionar</button>
			</div>
		</div>
		</div>
		<br>
		<hr>
		<!-- ITENS ADICIONADOS -->
		<!-- <div id="div-tabela">
			<h2>Doações adicionadas</h2>
			<table class="table table-striped">
				<tr>
					<th>ENTRADA</th>
					<th>DATA DE ENTRADA</th>
					<th>RESPONSÁVEL DO ATENDIMENTO/ ANDAMENTO</th>
					<th>DOADOR</th>
					<th>STATUS</th>
					<th>Nº DO SEI</th>                    
					<th>OBSERVAÇÃO</th>
					<th>COMENTÁRIO SMS</th>
					<th>BREVE RELATÓRIO DO PROCESSO SEI</th>
					<th>ITENS PENDENTES NO PROCESSO SEI</th>
					<th>MONITORAMENTO</th>
					<th></th>
				</tr>
				<tr v-for="item in itens">
					<td>{{item.entrada}}</td>
					<td>{{item.data_entrada}}</td>
					<td>{{item.responsavel_atendimento}}</td>
					<td>{{item.doador}}</td>
					<td>{{item.status}}</td>
					<td>{{item.numero_sei}}</td>
					<td>{{item.observacao}}</td>
					<td>{{item.comentario_sms}}</td>
					<td>{{item.relatorio_sei}}</td>
					<td>{{item.itens_pendentes_sei}}</td>					
					<td>{{item.monitoramento}}</td>
					<td>
						<center>
							<button title="Remover item" type="button" class="btn btn-danger btn-sm" v-on:click="itens.splice(itens.indexOf(item), 1)">
								<span style="font-size: 2em; line-height: 1">&times;</span>
							</button>
						</center>
					</td>
				</tr>
			</table>
			<br>
			<button class="btn btn-success btn-lg" v-on:click="cadastrarDoacoes()">Enviar</button>
		</div> -->
	</div>
</div>
	
<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="js/lodash.min.js"></script>
<script type="text/javascript" src="js/popper.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>

<!-- Vue.js -->
<script>	
	const DoacaoObj = {
			data_entrada: '',
			entrada: '',
			responsavel_atendimento: '',
			doador: '',
			tipo_formalizacao: '',
			contato: '',
            telefone_doador: '',
            email_doador: '',
			tipo_item: '',
            categoria_item: '',
			descricao_item: '',
			status: '',
			destino: '',
			endereco_entrega: '',
			responsavel_recebimento: '',
			quantidade: '',
            unidade_medida: '',
			valor_total: '',
            entrada_fracionada: 0,
			prazo_periodo: '',
			numero_sei: '',
			observacao: '',
			comentario_sms: '',
			relatorio_sei: '',
			itens_pendentes_sei: '',
			monitoramento: ''
	};
	
	var app = new Vue({
		el: '#app',
		data: {
			novoItem: JSON.parse(JSON.stringify(DoacaoObj)),
			itens: [],
			usuario: {
				nome: "<?php echo $_SESSION['nomeUsuario']; ?>",
				rf: "<?php echo $_SESSION['IDUsuario']; ?>"
			},
            categoriasTipoitem: [],
            tiposItem: [
                {
                    tipo: 'Comodato',
                    categorias: ['Espaço físico']
                },
                {
                    tipo: 'Dinheiro',
                    categorias: ['Recursos financeiros']
                },                
                {
                    tipo: 'Produto',
                    categorias: [
                        'Álcool',
                        'Alimentos',
                        'Insumo hospitalar'
                    ]
                },
                {
                    tipo: 'Serviço',
                    categorias: ['Hospedagem']
                }
            ],
            unidadesDeMedida: [
                'Unidades',
                'Litros',
                'Quilos',
                'Caixas',
                'Horas',
                'm²',
                'R$',
            ],
			keepInfo: false
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
			adicionarItem: function (){
				// Limpa número SEI
				if(this.novoItem.numero_sei)
					this.novoItem.numero_sei = this.apenasNumeros(this.novoItem.numero_sei);
				if(this.novoItem.valor_total)
					this.novoItem.valor_total = this.apenasNumeros(this.novoItem.valor_total)/100;

				// Insere item à lista de cadastro
				this.itens.push(JSON.parse(JSON.stringify(this.novoItem)));

				document.getElementById("entrada").focus();

				this.novoItem = DoacaoObj;
				this.cadastrarDoacoes(); // Remover caso seja preciso retomar o modo de inclusão em massa
			},
            atualizaTipos: function () {
                this.novoItem.categoria_item = "";
                for (var i = 0; i < this.tiposItem.length; i++) {
                    if(this.tiposItem[i].tipo == this.novoItem.tipo_item){
                        this.categoriasTipoitem = this.tiposItem[i].categorias;
                        break;
                    }
                }
            },
			/** 
				CADASTRO DE Doacoes
			*/
			cadastrarDoacoes: function () {
				if(this.itens.length === 0){
					window.alert("A lista está vazia! Adicione doações para cadastrar.");
					return;
				}

				let listaDeDoacoes = JSON.stringify(this.itens);
				
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						if(this.response > 0) {
							// Cadastro realizado com sucesso. Atualiza lista
							let concordancia = parseInt(this.response) > 1 ? " doações cadastradas" : " doação cadastrada";
							window.alert(parseInt(this.response)+concordancia+" com sucesso!");
							app.novoItem = DoacaoObj;
							app.itens = [];
							document.getElementById("entrada").focus();
						}                        
						else {
							window.alert("Falha ao cadastrar. Verifique os campos e tente novamente.\nSe o problema persistir, contate o desenvolvedor.");
							console.warn(this.response);
						}
					}
				};
				xhttp.open("POST", "cadastrar.php", true);
				xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				console.log(listaDeDoacoes.replace(/&/g,'CODREPEAMP',true));
				xhttp.send("insertList="+listaDeDoacoes.replace('&','CODREPEAMP'));
			}
		},
		computed: {
			criarNovoItem: function(){
				this.novoItem = this.itemModel;
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
#div-tabela {
	margin-left: 0;
	position: absolute;
	left: 10px;
	max-width: calc(100% - 20px);
}
.customRadio {
    margin-left: 5px;
    max-height: 30px;
}
.customRadio input {
    position: absolute;
    margin: auto 25%;
}
.customRadio label {
    line-height: 0.5em;
}
</style>

</body>
</html>
