<?php

// files.txt tem o nome dos arquivos no padrão errado
$files = file('files.txt');

foreach ($files as $file_name){
    $partes = explode('.', $file_name); // parte 1 fica com o nome sem a extensao
    $partes = explode('_', $partes[0]);
    if ($partes[1] >= 10) continue;
    $new_file_name = $partes[0] . '_' . str_pad($partes[1], 2, "0", STR_PAD_LEFT) . '.xml';
    $file_name = $partes[0] . '_' . $partes[1] . '.xml'; // reconstroi nome para tirar o enter do fim
    rename("$file_name", "$new_file_name");
    echo $file_name . " -> " . $new_file_name . "<br>\n";
}
