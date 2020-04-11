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
						<!-- ENTRADA, DATA, RESPONSÁVEL, DOADOR, FORMALIZAÇÃO -->
						<div class="form-row">
							<div class="col col-2">
								<!-- <label for="entrada">Origem de entrada</label> -->
								<input 
								class="form-control form-control-sm"
								v-model="novoItem.entrada"
								id="entrada"
								placeholder="Entrada"
								>
							</div>
							<div class="input-group input-group-sm col col-3">
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
							<div class="col col-2">
								<input
								class="form-control form-control-sm"
								v-model="novoItem.responsavel_atendimento"
								placeholder="Responsável"
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
						<br>						
						</div>
						<br>
						<!-- DESCRIÇÃO, TIPO, QUANTIDADE, VALOR, DESTINO -->
						<div class="form-row">
							<div class="col">
								<input
								class="form-control form-control-sm"
								v-model="novoItem.descricao_item"
								placeholder="Descrição do Item"
								>
							</div>
							<div class="col col-2">
								<select id="tipo_item" v-model="novoItem.tipo_item" class="form-control form-control-sm">
									<option disabled selected value="">Tipo de item</option>
									<option>Comodato</option>
									<option>Dinheiro</option>
									<option>Produto</option>
									<option>Serviço</option>
								</select>								
							</div>
							<div class="col">
								<input
								class="form-control form-control-sm"
								v-model="novoItem.quantidade"
								placeholder="Quantidade"
								>
							</div>
							<div class="col input-group input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input
								class="form-control form-control-sm"
								v-model="novoItem.valor_total"
								placeholder="Valor total da doação"
								type="number"
								>
							</div>
							<div class="col col-2">
								<input
								class="form-control form-control-sm"
								v-model="novoItem.destino"
								placeholder="Destino da doação"
								>
							</div>
						</div>
						<br>
						<!-- CONTATO, PRAZO ENTREGA, ENDEREÇO ENTREGA, REPONSÁVEL RECEBIMENTO -->
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
								v-model="novoItem.prazo_periodo"
								placeholder="Prazo entrega / Período disponibilização"
								>
							</div>
							<div class="col">
								<input
								class="form-control form-control-sm"
								v-model="novoItem.endereco_entrega"
								placeholder="Endereço de entrega"
								>
							</div>							
						</div>
						<br>
						<!-- STATUS, NUMERO SEI, OBSERVAÇÃO -->
						<div class="form-row">
							<div class="col">
								<input
								class="form-control form-control-sm"
								v-model="novoItem.responsavel_recebimento"
								placeholder="Responsável pelo recebimento da doação"
								>
							</div>
							<div class="col">
								<input
								class="form-control form-control-sm"
								v-model="novoItem.status"
								placeholder="Status"
								>
							</div>
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
			entrada: '',
			data_entrada: '',
			responsavel_atendimento: '',
			doador: '',
			tipo_formalizacao: '',
			descricao_item: '',
			tipo_item: '',
			quantidade: '',
			valor_total: '',
			destino: '',
			contato: '',
			prazo_periodo: '',
			endereco_entrega: '',
			responsavel_recebimento: '',
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
				// Limpa número SEI
				if(this.novoItem.numero_sei)
					this.novoItem.numero_sei = this.apenasNumeros(this.novoItem.numero_sei);

				// Insere item à lista de cadastro
				this.itens.push(JSON.parse(JSON.stringify(this.novoItem)));

				document.getElementById("entrada").focus();

				this.novoItem = DoacaoObj;
				this.cadastrarDoacoes(); // Remover caso seja preciso retomar o modo de inclusão em massa
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
</style>

</body>
</html>
