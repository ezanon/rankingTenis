<?php

require_once("fpdf17/fpdf.php");

class sumula {
	
	public $banco;
	public $ids = array();  // armazena id de cada jogo agendado da rodada atual
	public $pdf;
	
	public function __construct() {
		$this->banco = banco::instanciar();
		return true;
	}
	
	/*
	* entrega pdf
	*/
	public function obter_pdf(){
		$this->gerar_pdf();
		$this->pdf->Output();
		return true;
	}
	
	/*
	* gera pdf com sumulas
	*/
	private function gerar_pdf(){
		$this->pdf = new FPDF('P','mm','A4'); // landscape, milimetros, a4
		$this->pdf->SetMargins(15,20,10);
		if (!$this->obter_rodada())
			return false;
		foreach ($this->ids as $id){ // passa por todos ids coletados em obter_rodada
			$jogo = new jogo();
			$jogo->get($id,'rodada_atual');
			$this->pdf->AddPage();
			$this->cabecalho();
			$this->pdf->Ln(10);
			$this->pdf->Cell(0,0,'',1,1,'C');
			$this->pdf->Ln(10);
			if ($jogo->desafio == 1){
				$this->pdf->SetFont('Courier','B',12);
				$this->pdf->Cell(0,0,'- - - D E S A F I O - - - ',0,1,'C');
				$this->pdf->Ln(8);
			}
			$desafiante = new jogador($jogo->desafiante);
			$desafiado = new jogador($jogo->desafiado);
			$this->pdf->SetFont('Courier','B',14);
			$this->pdf->Cell(0,0,'[A] ' . $desafiante->nome_completo . ' (' . $desafiante->posicao . ')',0,1,'C');
			$this->pdf->Ln(8);
			$this->pdf->Cell(0,0,'X',0,1,'C');
			$this->pdf->Ln(8);
			$this->pdf->Cell(0,0,'[B] ' . $desafiado->nome_completo . ' (' . $desafiado->posicao . ')',0,1,'C');
			// exibe rodada, data, horario e local do jogo
			$rodada = new rodada();
			$this->pdf->Ln(15);
			$this->pdf->Cell(0,0,'',1,1,'C');
			$this->pdf->Ln(10);
			if ($jogo->quadra != 0){
				$quadra = new quadra($jogo->quadra);
				if (substr_count($quadra->horario,'SABADO'))
					$data = date("d/m", strtotime("next Saturday"));
				else 
					$data = date("d/m", strtotime("next Sunday"));
				$this->pdf->Cell(0,0,'Rodada ' . $rodada->numero . '/' . $rodada->ano  . ' : ' . $data . ' - ' . $quadra->quadra . ' - ' . $quadra->horario,0,1,'C');
			}
			else
				$this->pdf->Cell(0,0,'Jogo Possivel',0,1,'C');
			$this->pdf->Ln(10);
			$this->pdf->Cell(0,0,'',1,1,'C');
			$this->pdf->Ln(10);
			$lc = 62; // largura celula
			$ac = 10; // altura celula
			$this->pdf->Cell($lc,$ac,'Resultado 1o SET','LTRB',0,'C');
			$this->pdf->Cell($lc,$ac,'Resultado 2o SET','LTRB',0,'C');
			$this->pdf->Cell($lc,$ac,'Resultado 3o SET','LTRB',1,'C');
			$ac = 16; // altura celula
			$this->pdf->Cell($lc,$ac,'[A]','LTRB',0,'L');
			$this->pdf->Cell($lc,$ac,'[A]','LTRB',0,'L');
			$this->pdf->Cell($lc,$ac,'[A]','LTRB',1,'L');
			$this->pdf->Cell($lc,$ac,'[B]','LTRB',0,'L');
			$this->pdf->Cell($lc,$ac,'[B]','LTRB',0,'L');
			$this->pdf->Cell($lc,$ac,'[B]','LTRB',1,'L');
			$this->pdf->Ln(10);
			$lc = 93; // largura celula
			$ac = 10; // altura celula
			$this->pdf->Cell($lc * 2,$ac,'Resultado Final','LTRB',1,'C');
			$ac = 16; // altura celula
			$this->pdf->SetFont('Courier','B',10);
			$this->pdf->Cell($lc,$ac,'[A] ' . $desafiante->nome_completo,'LTRB',0,'C');
			$this->pdf->Cell($lc,$ac,'[B] ' . $desafiado->nome_completo,'LTRB',1,'C');
			$this->pdf->Cell($lc,$ac,'','LTRB',0,'L');
			$this->pdf->Cell($lc,$ac,'','LTRB',1,'L');
			$this->pdf->Ln(5);
			$lc = 40; // largura celula
			$ac = 12; // altura celula
			$this->pdf->SetFont('Courier','B',10);
			$this->pdf->Cell($lc,$ac,'Vencedor : ',0,0,'R');
			$this->pdf->Cell(0,$ac,'','B',1,'C');
			$this->pdf->Cell($lc,$ac,'jogador [A] : ',0,0,'R');
			$this->pdf->Cell(0,$ac,'','B',1,'C');
			$this->pdf->Cell($lc,$ac,'jogador [B] : ',0,0,'R');
			$this->pdf->Cell(0,$ac,'','B',1,'C');
			// Horario em que foi gerado arquivo
			$agora = date('d-m-Y H:i');
			$this->pdf->SetFont('Courier','I',8);
			$this->pdf->Ln(5);
			$this->pdf->Cell(0,0,'Gerado em ' . $agora,0,0,'R');

		}
		return true;
	}
	
	/*
	* escreve cabecalho no pdf
	*/
	private function cabecalho(){
		$this->pdf->Image('images/logo_cepeusp.jpg',15,15,30);
		$this->pdf->SetFont('Courier','B',12);
		$this->pdf->Cell(0,0,'Centro de Praticas Esportivas da Universidade de Sao Paulo',0,0,'R');
		$this->pdf->Ln(15);
		$this->pdf->SetFont('Courier','BI',18);
		$this->pdf->Cell(0,0,'Ranking de Tenis de Campo',0,1,'C');
		$this->pdf->Ln(8);
		$this->pdf->SetFont('Courier','B',14);
		$this->pdf->Cell(0,0,'SUMULA',0,1,'C');
		return true;
	}
	
	
	/*
	* obtém dados da rodada atual
	*/
	private function obter_rodada(){
		$q = "select id from rodada_atual where confirmado=1 order by quadra";
		//$q = "select id from rodada_atual";
		$res = $this->banco->consultar($q);
		$i = 0;
		foreach ($res as $r){
			$this->ids[] = $r['id'];
			$i++;
		}
		if ($i==0)
			return false;
		else
			return true;
	}
	
}

?>