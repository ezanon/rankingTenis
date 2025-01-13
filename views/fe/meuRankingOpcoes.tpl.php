<div class="container d-flex justify-content-center align-items-center vh-100">
<div class="card p-4 shadow" style="width: 100%; max-width: 400px;">

<form method="POST" action="?module=fe&action=meuRankingOpcoes">
  <div class="p-3 border rounded">
    <!-- Título -->
    <h4 class="text-center mb-4">Indisponibilidade</h4>

    <!-- Conteúdo do Formulário -->
    <div class="d-flex align-items-center">
      <div class="form-check me-3">
        <input class="form-check-input" type="checkbox" value="1" id="indisponivel" name="indisponivel">
        <input type="hidden" id="funcao" name="funcao" value="indisponibilidade">
        <label class="form-check-label" for="indisponivel">
          Estou indisponível na próxima rodada.
        </label>
      </div>
      <button type="submit" class="btn btn-primary">Enviar Indisponibilidade</button>
    </div>
  </div>
</form>



<form method="POST" action="?module=fe&action=salvarResultados">
  <div class="p-3 border rounded mb-3">
    <!-- Título -->
    <h4 class="text-center mb-4">Registro de Resultados</h4>

    <!-- Jogador 1 -->
    <div class="d-flex align-items-center mb-2">
      <span class="me-2" style="min-width: 100px;">Jogador 1:</span>
      <input type="number" id="sets_jogador1" name="sets_jogador1" class="form-control" placeholder="Sets" min="0" required>
    </div>

    <!-- Jogador 2 -->
    <div class="d-flex align-items-center mb-2">
      <span class="me-2" style="min-width: 100px;">Jogador 2:</span>
      <input type="number" id="sets_jogador2" name="sets_jogador2" class="form-control" placeholder="Sets" min="0" required>
    </div>

    <!-- Parciais -->
    <div class="mb-2">
      <label for="parciais" class="form-label">Parciais:</label>
      <textarea id="parciais" name="parciais" class="form-control" placeholder="Digite as parciais (exemplo: 6-4, 3-6, 7-5)" rows="2" required></textarea>
    </div>

    <!-- Botão -->
    <button type="submit" class="btn btn-primary">Enviar Resultados</button>
  </div>
</form>


</div></div>