<?php
require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";

    $con = new ConexaoSCI;
    
   
	
	$keyword = $_POST["keyword"];


	$query = "select Nome_Fantasia
				from CARGOSOL..CLIENTE with (nolock)
				join CARGOSOL..PESSOA with (nolock) on
					PESSOA.Pessoa_Id = cliente.Pessoa_Id
					and pessoa.Tab_Status_Id in (1,1071)
				where cliente.Tab_Status_Id in (1,1071)
				and PESSOA.Nome_Fantasia like '$keyword%'
				ORDER BY Nome_Fantasia";
	//print $query;
	$result = $con->select($query);

	// ?>
	 <ul class='select-list ' id="select-list" style="width:500px">
	 <?php
	foreach($result as $autocomplete) {
	// ?>
	 <li onClick="selectList('<?php echo $autocomplete["Nome_Fantasia"]; ?>');"><?php echo $autocomplete["Nome_Fantasia"]; ?></li>
	 <?php } ?>
	 </ul>

	<?php


