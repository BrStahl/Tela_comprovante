<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";


class Funcoes extends ConexaoSCI{

	
	//CRIA ARQUIVO
	public function criaArquivo($arquivo,$edi){
		

		//CRIACAO DO ARQUIVO
		//$arquivo = fopen('./arquivo/edi.txt','w+');
	 
		// gravamos os dados no arquivo.rss
		fwrite($arquivo,utf8_encode($edi));
	 
		// fechamos nosso arquivo
		//fclose($arquivo);
		
		/*
		//CRIA UM ARQUIVO BKP
		
		$load_id_bkp = $load_id.'_'.date("Ymd_His");
		
		$arquivo1 = fopen('./arquivos_bkp/'.$load_id_bkp.'.txt','w+');
	 
		// gravamos os dados no arquivo.rss
		fwrite($arquivo1,utf8_encode($edi));
	 
		// fechamos nosso arquivo
		fclose($arquivo1);			
		
		
		//ENVIANDO O ARQUIVO PARA O SERVIDOR
		$nome_arq = $load_id.'.txt';
		*/

	}
	

}


?>