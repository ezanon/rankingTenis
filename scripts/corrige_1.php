<?php

$files = file('files.txt');

foreach ($files as $file_name){
    $partes = explode('.', $file_name); // parte 1 fica com o nome sem a extensao
    $partes = explode('_', $partes[0]);
    $file_name = $partes[0] . '_' . $partes[1] . '.xml'; // reconstroi nome para tirar o enter do fim        
    $file_str = file_get_contents($file_name);
    $new_file_str = str_replace("</numero>\n<jogador>", "</ano>\n<jogador>", $file_str);
    $fp = fopen($file_name, 'w');
    fwrite($fp, $new_file_str);
    fclose($fp); 
}