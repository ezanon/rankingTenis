<?php

class ranking {
	
	public $banco;
	
	function __construct(){
		$this->banco = banco::instanciar();
		// obtem maximas posicoes dos rankings
		$this->misto_max = $this->max('misto');
		$this->feminino_max = $this->max('feminino');
		return NULL;
	}
	
	public function max($ranking){
		$q = "select posicao from jogador where ranking='$ranking' order by posicao desc limit 1";
		$res = $this->banco->consultar($q);
		foreach ($res as $r)
			$p = $r['posicao'];
		return $p;
	}
        
        /*
      Exibe lista dos jogadores
     */

    public function listar($categoria) {
        $q = "select * from jogador where ranking='$categoria' and ativo=1 order by posicao";
        $jogadores = $this->banco->consultar($q);
        $str = '';
        $str .= "<center><h1>Ranking $categoria</h1></center><table class='table'>
					<tr>
						<th scope=col>Posição</th>
						<th scope=col>Nome</th>
						<th scope=col>Contatos</th>
						<th scope=col>Jogos</th>
						<th scope=col>Vitórias</th>
						<th scope=col>Derrotas</th>
						<th scope=col>Vit Consecutivas</th>
						<th scope=col>Der Consecutivas</th>
						<th scope=col>WxO</th>
						<th scope=col>Informações</th>
					</tr>
				";
        foreach ($jogadores as $j) {
            switch ($j['cor']) {
                case 'VERDE':$bgcolor = 'verde';
                    break;
                case 'BRANCO':$bgcolor = 'branco';
                    break;
                case 'AMARELO':$bgcolor = 'amarelo';
                    break;
            }
            $wo = "&nbsp;";
            if ($j['wo'] > 0)
                $wo = $j['wo'];
            $str .= " <tr class={$bgcolor} >";
            $str .= "     <th class=\"jogador_{$j['cor']} row align-middle\">" . $j['posicao'] . "</td>
                                <td class=nome>" . $j['nome_completo'] . "</td>
                                <td>{$j['email']}<br>{$j['telefone_celular']}</td>
                                <td>" . $j['jogos'] . "</td>
                                <td>" . $j['vitorias_total'] . "</td>
                                <td>" . $j['derrotas_total'] . "</td>
                                <td>" . $j['vitorias_consecutivas'] . "</td>
                                <td>" . $j['derrotas_consecutivas'] . "</td>
                                <td class=wo>" . $wo . "</td>
                                <td>" . $j['info'] . "</td>
                        </tr>
                    ";
        }
        $str .= "</table>";
        return $str;
    }

}