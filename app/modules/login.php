<?php

class login {

	public $banco;
	
	public function __construct() {
		$this->banco = banco::instanciar();
	}
	
	public function logar(){
		global $config;
                $login = filter_input(INPUT_POST, 'login', FILTER_DEFAULT);
                $senha = md5(filter_input(INPUT_POST, 'senha', FILTER_DEFAULT));
		$str = '';
		// se admin
		if ($login=='admin'){
                    if ($this->getAdminPass($senha)){
                    //if ($senha==$config['pass_admin']){
                            $_SESSION['jogador']['id'] = -99;
                            $_SESSION['jogador']['ranking'] = -99;
                            $_SESSION['jogador']['jogador'] = 0;
                            $_SESSION['jogador']['admin'] = 1;
                            $str.= "Bem vindo, ADMININISTRADOR";
                            $_SESSION['acesso_autorizado'] = true;
                    }
                    else {
                            echo "##nao admin senha $senha " . $config['pass_admin'] . " ## ";
                            $str.= 'Acesso não autorizado, '. $login;
                            $_SESSION['acesso_autorizado'] = false;
                    }
                    return $str;
		}
		
                $id = $this->loginJogador($login, $senha);
                if ($id){
                    $j = new jogador($id);
                    $_SESSION['jogador']['id'] = $id;
                    $_SESSION['jogador']['nome'] = $j->nome_completo;
                    $_SESSION['jogador']['admin'] = $j->admin;
                    $_SESSION['jogador']['misto'] = $j->misto;
                    $_SESSION['jogador']['feminino'] = $j->feminino;
                    
                    $str.= "Bem vindo, " . $j->nome_completo;
                    if ($j->admin==1)
                            $str.= " <strong>(admin)</strong>";
                    $_SESSION['acesso_autorizado'] = true;
                }
                else {
                    $str.= 'Acesso não autorizado, '. $login;
                    $_SESSION['acesso_autorizado'] = false;
                }
                
		return $str;
	}
	
	public function sair(){
		session_destroy();
		$this->banco = NULL;
		return true;
	}
        
        private function getAdminPass($md5pass){
            $num = $this->banco->contar('admin','password',"'$md5pass'");
            if ($num>0) return true;
            else return false;
        }
        
        public function loginJogador($login,$senha) {
            // Monta a query para buscar o ID do jogador
            $q = "SELECT id FROM jogador WHERE login = ? AND senha = ? LIMIT 1";
            // Executa a consulta usando parâmetros seguros
            $res = $this->banco->consultar($q, [$login, $senha]);
            // Retorna o ID do jogador se encontrado, caso contrário retorna null
            return !empty($res) ? $res[0]['id'] : false;
        }

}


?>