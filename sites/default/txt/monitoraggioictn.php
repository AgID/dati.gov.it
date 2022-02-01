<?php   
header('Access-Control-Allow-Origin: https://datawrapper.dwcdn.net');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
$file=file_get_contents('/var/www/html/sites/default/txt/monitoraggioictn.csv');

echo $file

?>
