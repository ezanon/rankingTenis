<?php

// rodar dentro de /jogadores

$max = 700; // max id

for ($i=0; $i<=$max; $i++){
    $file_name = "$i/resultados.xml";
    if (!file_exists($file_name)) continue;
    // correcao 2
    $file_str = file_get_contents($file_name);
    $new_file_str = "<jogos>\n" . $file_str . "\n</jogos>";
    //$new_file_str = str_replace("><", ">\n<", $new_file_str);
    // correcao 3
    $new_file_str = str_replace("</desafiante>
							<desafiado_id>", "</desafiante_id><desafiado_id>", $new_file_str);
    $new_file_str = str_replace("</desafiado>
							<desafiante_posicao>", "</desafiado_id><desafiante_posicao>", $new_file_str);
    $fp = fopen($file_name, 'w');
    fwrite($fp, $new_file_str);
    fclose($fp);
    
    
    
}
    
    
