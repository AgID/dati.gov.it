<?php
header('Access-Control-Allow-Origin: https://datawrapper.dwcdn.net'); // accesso solo da datawrapper per gestire il grafico dinamico avanzamenti
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
// segue il file condiviso su google drive con statistiche dinamiche
$file=file_get_contents("https://docs.google.com/spreadsheets/d/e/2PACX-1vSDSGLc2yiSCJZ-BXTNBhNos6L7NzJyZrbwBhatBhpLdaYWdyEVAyJbZ6uTa547SN-pWazsBm_sAO2p/pub?gid=1431059372&single=true&output=csv");
$anno = "#";
$pos = strpos($file, $anno);
if($pos !== false){
header('Location: '.$_SERVER['REQUEST_URI']);
}else  {
$fileorario="/var/www/html/sites/default/txt/monitoraggioictn.csv";
  $fr111 = fopen($fileorario, 'w');
  fwrite($fr111, $file);
  // chiudiamo il file
  fclose($fr111);

echo $file;
}


?>
