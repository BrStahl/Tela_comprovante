<?php
session_name("covre_ti");
session_start();



   // Determina que o arquivo é uma planilha do Excel
   header("Content-type: application/vnd.ms-excel");   

   // Força o download do arquivo
   header("Content-type: application/force-download");  

   // Seta o nome do arquivo
   header("Content-Disposition: attachment; filename=teste.xls");

   header("Pragma: no-cache");


include("../../SCA/includes/conect_sqlserver.php");

		$cont = 1;
		$max = 10;
        
	
			print"<table border='1' width='1200'>
				  <tr>
					 <td bgcolor='#CCCCCC'><font size='-2'><b><center>DOC</a></td>
				  </tr>";
		
			 while($cont <= $max)
			 {
				 	$cont = '0000'.$cont;

					print "<tr>
							<td bgcolor='#FFFFFF'><center><font size='-2'>".sprintf('%05d', $cont)."</center></b></td>
																																																																																																																																																
						</tr>";		 
				$cont++;					 
			}//fil while
			
			print "</table>";

	


?>

