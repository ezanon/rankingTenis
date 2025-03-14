<div class="container d-flex justify-content-center align-items-center vh-100">
<div class="card p-4 shadow">
       
    <?php 
    global $dev;
    $id = $_SESSION['jogador']['id'];
    $j = new jogador($id);
//    if ($dev){
//        echo '<pre>';
//        echo "SESSAO: \n"; 
//        var_dump($_SESSION);  
//        echo '</pre>';
//    }
    ?>
    
    <div class="p-3 border rounded mb-3 text-center">
        <?php 
            echo '<h5 class="text-center mb-4">' . $j->nome_completo . '</h5>';
            if ($j->misto==1) {
                echo '<span class="badge badge-pill badge-success">Categoria Misto</span>';
            }
            if ($j->feminino==1) {
                if ($j->misto==1) echo ' ';
                echo '<span class="badge badge-pill badge-warning">Categoria Feminino</span>';
            }
        ?>       
    </div>

<!-- DISPONIBILIDADE -->
<?php
$d = explode(',',$j->disponibilidade);
?>
<form id="disponibilidadeForm">
  <div class="p-3 border rounded mb-3">
    <!-- Título -->
    <h4 class="text-center mb-4">Definir Disponibilidade</h4>
    <p class="text-muted text-center">Selecione os horários disponíveis para a próxima rodada</p>

    <!-- Opções de Disponibilidade com valores numéricos -->
    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" id="sabado_13h" name="disponibilidade[]" value="1" <?php if(in_array(1, $d)) echo 'checked'; ?>>
      <label class="form-check-label" for="sabado_13h">[1] Sábado às 13h00</label>
    </div>
    <div class="form-check mb-2">
      <input class="form-check-input" type="checkbox" id="sabado_14h30" name="disponibilidade[]" value="2" <?php if(in_array(2, $d)) echo 'checked'; ?>>
      <label class="form-check-label" for="sabado_14h30">[2] Sábado às 14h30</label>
    </div>
    <div class="form-check mb-2">
      <input class="form-check-input" type="checkbox" id="sabado_15h30" name="disponibilidade[]" value="3" <?php if(in_array(3, $d)) echo 'checked'; ?>>
      <label class="form-check-label" for="sabado_15h30">[3] Sábado às 15h30</label>
    </div>
    <div class="form-check mb-2">
      <input class="form-check-input" type="checkbox" id="domingo_10h" name="disponibilidade[]" value="4" <?php if(in_array(4, $d)) echo 'checked'; ?>>
      <label class="form-check-label" for="domingo_10h">[4] Domingo às 10h00</label>
    </div>
    <div class="form-check mb-2">
      <input class="form-check-input" type="checkbox" id="domingo_11h30" name="disponibilidade[]" value="5" <?php if(in_array(5, $d)) echo 'checked'; ?>>
      <label class="form-check-label" for="domingo_11h30">[5] Domingo às 11h30</label>
    </div>

    <!-- Botão -->
    <button type="submit" class="btn btn-primary">Salvar Disponibilidade</button>

    <!-- Área para mostrar a resposta -->
    <div id="respostaDisponibilidade" class="mt-3"></div>
  </div>
</form>

<!-- Script AJAX -->
<script>
  document.getElementById("disponibilidadeForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Impede a recarga da página

    const formData = new FormData(this);

    fetch("ajax.php?module=ajax&action=salvarDisponibilidade", {
      method: "POST",
      body: formData
    })
    .then(response => response.text()) // Converte a resposta para texto
    .then(data => {
      document.getElementById("respostaDisponibilidade").innerHTML = `<div class="alert alert-success">${data}</div>`;
    })
    .catch(error => {
      document.getElementById("respostaDisponibilidade").innerHTML = `<div class="alert alert-danger">Erro ao salvar disponibilidade.</div>`;
    });
  });
</script>


<!-- 
REGISTRO DE RESULTADOS 
-->
<form method="POST" action="?module=fe&action=salvarResultados" id="resultadoForm">
  <div class="p-3 border rounded mb-3">
    <!-- Título -->
    <h4 class="text-center mb-4">Registro de Resultados</h4>

    <!-- Jogador Vencedor -->
    <div class="mb-3">
      <label for="vencedor" class="form-label">Jogador Vencedor:</label>
      <select id="vencedor" name="vencedor" class="form-select" required>
        <option value="" disabled selected>Selecione o vencedor</option>
        <option value="jogador1">Jogador 1</option>
        <option value="jogador2">Jogador 2</option>
        <option value="WO_Duplo" disabled>WO Duplo</option> <!-- WO Duplo desabilitado -->
      </select>
    </div>

    <!-- Resultado (Parciais Simplificadas) -->
    <div class="mb-3">
      <label for="resultado" class="form-label">Resultado:</label>
      <select id="resultado" name="resultado" class="form-select" required>
        <option value="" disabled selected>Selecione o resultado</option>
        <option value="2x0">2x0</option>
        <option value="2x1">2x1</option>
        <option value="WO">WO</option>
        <option value="Abandono">Abandono</option>
        <option value="Falta com Aviso">Falta com Aviso</option>
      </select>
    </div>

    <!-- Parciais Detalhadas -->
    <div class="mb-3">
      <label for="parciais" class="form-label">Parciais Detalhadas:</label>
      <textarea id="parciais" name="parciais" class="form-control" placeholder="Exemplo: 6-4, 3-6, 10-8" rows="2"></textarea>
    </div>

    <!-- Quem levou as bolas -->
    <div class="mb-3">
      <label for="bolas" class="form-label">Quem levou o tubo de bolas novas?</label>
      <select id="bolas" name="bolas" class="form-select" required>
        <option value="" disabled selected>Selecione uma opção</option>
        <option value="jogador1">Jogador 1</option>
        <option value="jogador2">Jogador 2</option>
        <option value="bolas_usadas">Foram usadas bolas usadas</option>
      </select>
    </div>

    <!-- Observações -->
    <div class="mb-3">
      <label for="observacoes" class="form-label">Observações:</label>
      <textarea id="observacoes" name="observacoes" class="form-control" placeholder="Insira observações adicionais sobre o jogo" rows="2"></textarea>
    </div>

    <!-- Botão -->
    <button type="submit" class="btn btn-primary">Registrar Resultado</button>
  </div>
</form>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    const vencedorSelect = document.getElementById('vencedor');
    const resultadoSelect = document.getElementById('resultado');
    const form = document.getElementById('resultadoForm');

    // Função para verificar se "WO Duplo" está selecionado
    function verificarWoDuplo() {
      if (vencedorSelect.value === "WO_Duplo") {
        // Desativar todos os campos e o botão do formulário
        form.querySelectorAll('input, select, textarea, button').forEach(el => {
          el.disabled = true;
        });
      } else {
        // Reabilitar os campos se "WO Duplo" não estiver selecionado
        form.querySelectorAll('input, select, textarea, button').forEach(el => {
          el.disabled = false;
        });
      }
    }

    // Verificar "WO Duplo" no carregamento da página
    verificarWoDuplo();

    // Monitorar alterações no campo de vencedor
    vencedorSelect.addEventListener('change', verificarWoDuplo);
  });
</script>


<!-- 
ATUALIZAR CONTATOS 
-->
<?php
$email = $j->email;
$cel = $j->telcel;
?>
<!-- FORMULÁRIO DE ATUALIZAÇÃO DE CONTATO -->
<form id="contatoForm">
  <div class="p-3 border rounded mb-3">
    <!-- Título -->
    <h4 class="text-center mb-4">Atualizar Contatos</h4>

    <!-- Campo para Email -->
    <div class="mb-3">
      <label for="email" class="form-label">Email:</label>
      <input type="email" id="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
    </div>

    <!-- Campo para Telefone -->
    <div class="mb-3">
      <label for="telefone" class="form-label">Telefone:</label>
      <input type="tel" id="telefone" name="telefone" class="form-control" value="<?php echo $cel; ?>" required>
    </div>

    <!-- Botão -->
    <button type="submit" class="btn btn-primary">Atualizar Contatos</button>

    <!-- Área para mostrar resposta -->
    <div id="respostaContatos" class="mt-3"></div>
  </div>
</form>

<!-- AJAX para envio sem recarregar -->
<script>
  document.getElementById("contatoForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Evita o recarregamento da página

    const formData = new FormData(this);

    fetch("ajax.php?module=ajax&action=atualizarContatos", {
      method: "POST",
      body: formData
    })
    .then(response => response.text()) // Converte resposta para texto
    .then(data => {
      document.getElementById("respostaContatos").innerHTML = `<div class="alert alert-success">${data}</div>`;
    })
    .catch(error => {
      document.getElementById("respostaContatos").innerHTML = `<div class="alert alert-danger">Erro ao atualizar contatos.</div>`;
    });
  });
</script>


<!-- ALTERAÇÃO DE SENHA -->
<form id="senhaForm">
  <div class="p-3 border rounded mb-3">
    <!-- Título -->
    <h4 class="text-center mb-4">Alterar Senha</h4>
    <p class="text-muted text-center mb-4">Atualize sua senha para manter sua conta segura.</p>

    <!-- Senha Atual -->
    <div class="mb-3">
      <label for="senha_atual" class="form-label">Senha Atual:</label>
      <input type="password" id="senha_atual" name="senha_atual" class="form-control" placeholder="Digite sua senha atual" required>
    </div>

    <!-- Nova Senha -->
    <div class="mb-3">
      <label for="nova_senha" class="form-label">Nova Senha:</label>
      <input type="password" id="nova_senha" name="nova_senha" class="form-control" placeholder="Digite sua nova senha" required>
    </div>

    <!-- Confirmar Nova Senha -->
    <div class="mb-3">
      <label for="confirmar_senha" class="form-label">Confirmar Nova Senha:</label>
      <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" placeholder="Confirme sua nova senha" required>
    </div>

    <!-- Botão -->
    <button type="submit" class="btn btn-primary">Alterar Senha</button>

    <!-- Área para mostrar a resposta -->
    <div id="respostaSenha" class="mt-3"></div>
  </div>
</form>

<!-- AJAX para envio sem recarregar -->
<script>
  document.getElementById("senhaForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Evita o recarregamento da página

    const novaSenha = document.getElementById("nova_senha").value;
    const confirmarSenha = document.getElementById("confirmar_senha").value;

    if (novaSenha !== confirmarSenha) {
      document.getElementById("respostaSenha").innerHTML = `<div class="alert alert-danger">As senhas não correspondem.</div>`;
      return;
    }

    const formData = new FormData(this);

    fetch("ajax.php?module=ajax&action=alterarSenha", {
      method: "POST",
      body: formData
    })
    .then(response => response.text()) // Converte resposta para texto
    .then(data => {
      document.getElementById("respostaSenha").innerHTML = `<div class="alert alert-success">${data}</div>`;
    })
    .catch(error => {
      document.getElementById("respostaSenha").innerHTML = `<div class="alert alert-danger">Erro ao alterar senha.</div>`;
    });
  });
</script>





</div></div>