<?php
header('Access-Control-Allow-Origin: https://datawrapper.dwcdn.net');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
$file=file_get_contents("https://docs.google.com/spreadsheets/d/e/2PACX-1vTfexl7t2kg_yGXgoPrTD2ab92QNhuKDGOAgGSE9XPn7jX9ITyyzLfIAEb9inZTudWyzLoKpprmWOx5/pub?gid=384894090&single=true&output=csv");
echo $file;

 ?>
