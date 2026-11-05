<?php
include('../class/Dados.php');

$filtra_tabelas = new Dados();

$dados = $filtra_tabelas->exibePo_sts();

print $dados->relatorio;