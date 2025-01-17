<div class="container d-flex justify-content-center align-items-center vh-100">
<div class="card p-4 shadow">

<!-- 
DISPONIBILIDADE 
-->
<form method="POST" action="?module=fe&action=salvarDisponibilidade">
  <div class="p-3 border rounded mb-3">
    <!-- Título -->
    <h4 class="text-center mb-4">Definir Disponibilidade</h4>
    <p class="text-muted text-center">Selecione os horários disponíveis para a próxima rodada</p>

    <!-- Opções de Disponibilidade -->
    <div class="form-check mb-2">
      <input class="form-check-input" type="checkbox" id="sabado_13h" name="disponibilidade[]" value="sábado às 13h00" >
      <label class="form-check-label" for="sabado_13h">Sábado às 13h00</label>
    </div>
    <div class="form-check mb-2">
      <input class="form-check-input" type="checkbox" id="sabado_14h30" name="disponibilidade[]" value="sábado às 14h30" >
      <label class="form-check-label" for="sabado_14h30">Sábado às 14h30</label>
    </div>
    <div class="form-check mb-2">
      <input class="form-check-input" type="checkbox" id="sabado_15h30" name="disponibilidade[]" value="sábado às 15h30" >
      <label class="form-check-label" for="sabado_15h30">Sábado às 15h30</label>
    </div>
    <div class="form-check mb-2">
      <input class="form-check-input" type="checkbox" id="domingo_10h" name="disponibilidade[]" value="domingo às 10h00" >
      <label class="form-check-label" for="domingo_10h">Domingo às 10h00</label>
    </div>
    <div class="form-check mb-2">
      <input class="form-check-input" type="checkbox" id="domingo_11h30" name="disponibilidade[]" value="domingo às 11h30" >
      <label class="form-check-label" for="domingo_11h30">Domingo às 11h30</label>
    </div>

    <!-- Botão -->
    <button type="submit" class="btn btn-primary">Salvar Disponibilidade</button>
  </div>
</form>


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
<form method="POST" action="?module=fe&action=atualizarContatos">
  <div class="p-3 border rounded mb-3">
    <!-- Título -->
    <h4 class="text-center mb-4">Atualizar Contatos</h4>

    <!-- Campo para Email -->
    <div class="mb-3">
      <label for="email" class="form-label">Email:</label>
      <input type="email" id="email" name="email" class="form-control" placeholder="Digite seu email" required>
    </div>

    <!-- Campo para Telefone -->
    <div class="mb-3">
      <label for="telefone" class="form-label">Telefone:</label>
      <input type="tel" id="telefone" name="telefone" class="form-control" placeholder="Digite seu telefone" required>
    </div>

    <!-- Botão -->
    <button type="submit" class="btn btn-primary">Atualizar Contatos</button>
  </div>
</form>

<!--
ALTERAÇÃO DE SENHA 
-->
<form method="POST" action="?module=fe&action=alterarSenha">
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
  </div>
</form>

<!-- Validação de Senhas com JavaScript -->
<script>
  document.querySelector('form').addEventListener('submit', function (e) {
    const novaSenha = document.getElementById('nova_senha').value;
    const confirmarSenha = document.getElementById('confirmar_senha').value;

    if (novaSenha !== confirmarSenha) {
      e.preventDefault(); // Impede o envio do formulário
      alert('As senhas não correspondem. Por favor, verifique.');
    }
  });
</script>




</div></div>