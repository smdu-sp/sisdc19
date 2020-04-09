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
				clip-path: inset(0px 0px 20% 0px);
				margin: 0 auto -30%;
				max-width: 150px;
				text-align: center;">
				<img src="img/logo_smdu.png" alt="Cidade de São Paulo" style="max-width: 100%; max-height: 50%;">
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
						<div class="form-row">
							<div class="col">
								<!-- <label for="entrada">Origem de entrada</label> -->
								<input 
								class="form-control form-control-sm"
								v-model="novoItem.entrada"
								id="entrada"
								placeholder="Entrada"
								>
							</div>
							<div class="input-group input-group-sm col col-4">
								<div class="input-group-prepend">
									<span class="input-group-text">Data de entrada</span>
								</div>								
								<input
									class="form-control form-control-sm"
									v-model="novoItem.data_entrada"
									placeholder="Data de entrada"
									type="date"
									>
							</div>
							<div class="col">
								<input
								class="form-control form-control-sm"
								v-model="novoItem.responsavel_atendimento"
								placeholder="Responsável"
								>
							</div>
						<br>
						<!-- DESCRIÇÃO -->
						<!-- <label for="discriminacao">Discriminação do bem</label> -->
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
					<!-- <div class="col-3">
						<img v-if="fotoUrl" :src="fotoUrl" :alt="novoItem.discriminacao" class="mw-100">
					</div> -->
				</div>
				<input type="checkbox" id="manterInfo" v-model="keepInfo">				
				<br>
				<button class="btn btn-primary float-left" style="cursor: pointer;" v-on:click="adicionarItem()">Adicionar</button>
			</div>
		</div>
		</div>
		<br>
		<hr>
		<!-- ITENS ADICIONADOS -->
		<div id="div-tabela">
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
			<button class="btn btn-success btn-lg" v-on:click="cadastrarDoacoes()">Enviar</button>
		</div>
	</div>
</div>
	
<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="js/lodash.min.js"></script>
<script type="text/javascript" src="js/popper.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>

<!-- Vue.js -->
<script>	
	const DoacaoObj = {
			entrada: '',
			data_entrada: '',
			responsavel_atendimento: '',
			doador: '',
			status: '',
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
				// Verifica campos obrigatórios
				let pendentes = [];
				if(!this.setor)
					pendentes.push("setor");
				// if(this.divisoes && this.divisoes.length > 0 && !this.divisao){
				//     pendentes.push("divisão");
				// }
				if(!this.sala || !this.andar)
					pendentes.push("andar / sala");

				if(!this.novoItem.discriminacao)
					pendentes.push("discriminação");
				if(!this.novoItem.cor)
					pendentes.push("cor");
				
				if(pendentes.length > 0){
					let erro = "Por favor, preencha os seguintes campos:";
					for(item in pendentes)
						erro+="\n"+pendentes[item];
					window.alert(erro);
					return;
				}

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
				this.itens.push(JSON.parse(JSON.stringify(this.novoItem)));

				document.getElementById("chapa").focus();

				// let tempItem = this.novoItem;
				// Limpa formulário se opção "Cadastrar item similar" estiver desmarcada
				if(app.keepInfo === true){
				// if(false){
					app.novoItem.chapa = '';
					app.novoItem.chapaOutraUnidade = '';
					app.novoItem.numSerie = '';
				}
				else {
					this.novoItem = DoacaoObj;
					this.fotoUrl = '';
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
							let concordancia = parseInt(this.response) > 1 ? " itens cadastrados" : " item cadastrado";
							window.alert(parseInt(this.response)+concordancia+" com sucesso!");
							app.novoItem = DoacaoObj;
							app.itens = [];
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
</style>

</body>
</html>
