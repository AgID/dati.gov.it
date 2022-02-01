<?php
header('Access-Control-Allow-Origin: https://datawrapper.dwcdn.net');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
$file=file_get_contents("https://docs.google.com/spreadsheets/d/e/2PACX-1vTfexl7t2kg_yGXgoPrTD2ab92QNhuKDGOAgGSE9XPn7jX9ITyyzLfIAEb9inZTudWyzLoKpprmWOx5/pub?gid=384894090&single=true&output=csv");
$fileorario="/var/www/html/sites/default/txt/monitoraggio.csv";
$anno = "Caricamento";
if(strpos($file, $anno) === false){
  $fr111 = fopen($fileorario, 'w');
  // inseriamo la riga
  fwrite($fr111, $file);
  //  echo ('regioni ok');
  // chiudiamo il file
  fclose($fr111);

  echo $file;
}else echo "errore";


//echo $file;

 ?>
