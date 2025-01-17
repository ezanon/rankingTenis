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
		
		// se não admin
		$info = $this->banco->ver('jogador','id,nome_completo,admin,jogador,ranking',"login='$login' and senha='$senha'");
		if (!$info) {
			$str.= 'Acesso não autorizado, '. $login;
			$_SESSION['acesso_autorizado'] = false;
		}
		else {
			foreach ($info as $j){	
				$_SESSION['jogador']['id'] = $j['id'];
				$_SESSION['jogador']['ranking'] = $j['ranking'];
				$_SESSION['jogador']['jogador'] = $j['jogador'];
				$_SESSION['jogador']['admin'] = $j['admin'];
				$str.= "Bem vindo, " . $j['nome_completo'];
				if ($_SESSION['jogador']['admin']==1)
					$str.= " <strong>(admin)</strong>";
				$_SESSION['acesso_autorizado'] = true;
				break;
			}
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

}


?>