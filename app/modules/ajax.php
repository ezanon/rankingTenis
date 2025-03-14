<?php

class ajax{
	
    public $banco;

    public function __construct(){
            $this->banco = banco::instanciar();
            return NULL;
    }
    
    public function salvarDisponibilidade() {
        $jogador_id = $_SESSION['jogador']['id']; // Obtém o ID do jogador logado

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['disponibilidade']) && is_array($_POST['disponibilidade'])) {
                $valores = implode(",", $_POST['disponibilidade']); // Junta os valores com vírgula

                // Query SQL para atualizar a disponibilidade
                $q = "UPDATE jogador SET disponibilidade = '$valores' WHERE id = $jogador_id";
                // Executa a query
                if ($this->banco->executar($q)) {
                    return "Disponibilidade salva com sucesso! Horários selecionados: $valores";
                } else {
                    return "Erro ao salvar disponibilidade.";
                }
            } else {
                return "Nenhum horário selecionado.";
            }
        }
        return "Erro ao processar requisição.";
    }
    
    
    public function atualizarContatos() {
        $jogador_id = $_SESSION['jogador']['id']; // Obtém ID do jogador logado

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = isset($_POST['email']) ? trim($_POST['email']) : "";
            $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : "";

            // Validação básica
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "Erro: Email inválido.";
            }
            if (!preg_match('/^\d{10,11}$/', preg_replace('/\D/', '', $telefone))) {
                return "Erro: Telefone inválido.";
            }

            // Query SQL segura com Prepared Statements
            $q = "UPDATE jogador SET email = ?, telefone_celular = ? WHERE id = ?";
            $sucesso = $this->banco->executar($q, [$email, $telefone, $jogador_id]);

            return $sucesso ? "Contatos atualizados com sucesso!" : "Erro ao atualizar contatos.";
        }

        return "Erro ao processar requisição.";
    }    
    
    
    public function alterarSenha() {
        $jogador_id = $_SESSION['jogador']['id']; // Obtém o ID do jogador logado

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $senha_atual = isset($_POST['senha_atual']) ? trim($_POST['senha_atual']) : "";
            $nova_senha = isset($_POST['nova_senha']) ? trim($_POST['nova_senha']) : "";

            // Verifica se a senha atual está correta
            $sql = "SELECT senha FROM jogador WHERE id = ?";
            $resultado = $this->banco->consultar($sql, [$jogador_id]);

            if (empty($resultado)) {
                return "Erro: Usuário não encontrado.";
            }

            $senha_armazenada = $resultado[0]['senha'];

            // Verifica se a senha atual confere
            if ($senha_armazenada !== md5($senha_atual)) {
                return "Erro: Senha atual incorreta.";
            }

            // Atualiza para a nova senha (criptografada com MD5)
            $nova_senha_cripto = md5($nova_senha);
            $q = "UPDATE jogador SET senha = ? WHERE id = ?";
            $sucesso = $this->banco->executar($q, [$nova_senha_cripto, $jogador_id]);

            return $sucesso ? "Senha alterada com sucesso!" : "Erro ao alterar a senha.";
        }

        return "Erro ao processar requisição.";
    }    

}