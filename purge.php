<?
echo "This page runs a script that clears the main cache.<br>";
db_query("DELETE FROM {cache} WHERE 1");
db_query("DELETE FROM {cache_filter} WHERE 1");
db_query("DELETE FROM {cache_menu} WHERE 1");
db_query("DELETE FROM {cache_page} WHERE 1");
echo "It's clear now.<br>";
?>
