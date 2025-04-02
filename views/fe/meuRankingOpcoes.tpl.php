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
            echo '<h5 class="text-center mb-2">' . $j->nome_completo . '</h5>';
            if ($j->admin == 1) {
                echo '<span class="badge badge-pill mr-2 badge-dark">Admin</span>';
            }
            if ($j->misto == 1) {
                echo '<span class="badge badge-pill mr-2 badge-success">Categoria Misto</span>';
                $space = ' ';
            }
            if ($j->feminino == 1) {
                echo '<span class="badge badge-pill badge-warning">Categoria Feminino</span>';
            }
            ?>       
        </div>

<!-- DISPONIBILIDADE 2a a 4a 12h -->
        <?php
// Obtém o dia da semana (1 = Segunda, 7 = Domingo) e a hora atual
        $diaSemana = date('N');
        $horaAtual = date('H:i');

// Verifica se está entre segunda 00:01 e quarta 12:00
        $exibirFormulario = ($diaSemana == 1) || // Segunda-feira (qualquer horário)
                ($diaSemana == 2) || // Terça-feira (qualquer horário)
                ($diaSemana == 3 && $horaAtual <= "12:00"); // Quarta-feira antes das 12:00

        if ($exibirFormulario):
            $d = explode(',', $j->disponibilidade);
            ?>
            <form id="disponibilidadeForm">
                <div class="p-3 border rounded mb-3">
                    <h4 class="text-center mb-4">Definir Disponibilidade</h4>
                    <p class="text-muted text-center">Selecione os horários disponíveis para a próxima rodada</p>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="sabado_13h" name="disponibilidade[]" value="1" <?php if (in_array(1, $d)) echo 'checked'; ?>>
                        <label class="form-check-label" for="sabado_13h">[1] Sábado às 13h00</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="sabado_14h30" name="disponibilidade[]" value="2" <?php if (in_array(2, $d)) echo 'checked'; ?>>
                        <label class="form-check-label" for="sabado_14h30">[2] Sábado às 14h30</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="sabado_15h30" name="disponibilidade[]" value="3" <?php if (in_array(3, $d)) echo 'checked'; ?>>
                        <label class="form-check-label" for="sabado_15h30">[3] Sábado às 15h30</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="domingo_10h" name="disponibilidade[]" value="4" <?php if (in_array(4, $d)) echo 'checked'; ?>>
                        <label class="form-check-label" for="domingo_10h">[4] Domingo às 10h00</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="domingo_11h30" name="disponibilidade[]" value="5" <?php if (in_array(5, $d)) echo 'checked'; ?>>
                        <label class="form-check-label" for="domingo_11h30">[5] Domingo às 11h30</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Salvar Disponibilidade</button>
                    <div id="respostaDisponibilidade" class="mt-3"></div>
                </div>
            </form>

            <script>
                document.getElementById("disponibilidadeForm").addEventListener("submit", function (event) {
                    event.preventDefault();

                    const formData = new FormData(this);

                    fetch("ajax.php?module=ajax&action=salvarDisponibilidade", {
                        method: "POST",
                        body: formData
                    })
                            .then(response => response.text())
                            .then(data => {
                                document.getElementById("respostaDisponibilidade").innerHTML = `<div class="alert alert-success">${data}</div>`;
                            })
                            .catch(error => {
                                document.getElementById("respostaDisponibilidade").innerHTML = `<div class="alert alert-danger">Erro ao salvar disponibilidade.</div>`;
                            });
                });
            </script>

        <?php else: ?>
            <div class="alert alert-info text-center mt-4">
                <i class="bi bi-info-circle-fill"></i> O período para definir a disponibilidade está fechado. Volte na próxima segunda-feira!
            </div>
        <?php endif; ?>
<!-- DISPONIBILIDADE 2a a 4a 12h FIM -->


<!-- REGISTRO DE RESULTADOS DE JOGOS AGENDADOS -->
        <?php
// **Verificar se o formulário deve ser exibido**
        $exibirFormulario = false;

// Obter dia da semana e hora atual
        $diaSemana = date("w");
        $horaAtual = date("H");

// Verificar se é quarta-feira entre 12h e 18h
        $quartaRestrita = ($diaSemana == 3 && $horaAtual >= 12 && $horaAtual < 18);

// Criar instância do banco de dados
        $banco = banco::instanciar();

// Consultar se a rodada está em andamento
        $sqlRodada = "SELECT rodada_em_andamento FROM rodada_controle WHERE id = 1";
        $dadosRodada = $banco->consultar($sqlRodada);
        $rodadaEmAndamento = $dadosRodada ? $dadosRodada[0]["rodada_em_andamento"] : 0;

// Consultar se o jogador tem jogo agendado
        $sqlJogo = "SELECT id, jogador1_id, jogador2_id, quem_levou_bola, categoria, barragem, 
                   vencedor_id, resultado, parciais, observacoes 
            FROM jogos_agendados 
            WHERE jogador1_id = :jogador_id OR jogador2_id = :jogador_id";
        $dadosJogo = $banco->consultar($sqlJogo, ["jogador_id" => $id]);

        if ($dadosJogo) {
            $jogoId = $dadosJogo[0]["id"];
            $jogador1Id = $dadosJogo[0]["jogador1_id"];
            $jogador2Id = $dadosJogo[0]["jogador2_id"];
            $quemLevouBola = $dadosJogo[0]["quem_levou_bola"];
            $categoria = $dadosJogo[0]["categoria"];
            $barragem = $dadosJogo[0]["barragem"];
            $vencedorId = $dadosJogo[0]["vencedor_id"];
            $resultado = $dadosJogo[0]["resultado"];
            $parciais = $dadosJogo[0]["parciais"];
            $observacoes = $dadosJogo[0]["observacoes"];

            $colunaPosicao = ($categoria === 'WTA') ? 'posicao_feminino' : 'posicao_misto';
            $sqlNomes = "SELECT id, nome_completo, $colunaPosicao AS posicao FROM jogador WHERE id IN (:jogador1, :jogador2)";
            $dadosNomes = $banco->consultar($sqlNomes, ["jogador1" => $jogador1Id, "jogador2" => $jogador2Id]);

            foreach ($dadosNomes as $jogador) {
                if ($jogador["id"] == $jogador1Id) {
                    $nomeJogador1 = $jogador["nome_completo"];
                    $posicaoJogador1 = $jogador["posicao"];
                } elseif ($jogador["id"] == $jogador2Id) {
                    $nomeJogador2 = $jogador["nome_completo"];
                    $posicaoJogador2 = $jogador["posicao"];
                }
            }

            if ($rodadaEmAndamento == 1 && !$quartaRestrita) {
                $exibirFormulario = true;
            }
        }

        if ($exibirFormulario) :
            ?>
            <div class="p-3 border rounded mb-3 text-center">
                <h5>Jogo Agendado</h5>
                <p>
                    <span class="badge bg-secondary text-white"><?= htmlspecialchars($categoria) ?></span>
                    <span class="badge bg-primary text-white">B<?= $barragem ?></span><br>
                    <span class="badge bg-info text-white">#<?= $posicaoJogador1 ?></span>
                    <?= htmlspecialchars($nomeJogador1) ?>
                    <br>x<br>
                    <span class="badge bg-info text-white">#<?= $posicaoJogador2 ?></span>
                    <?= htmlspecialchars($nomeJogador2) ?>
                </p>
            </div>

            <form method="POST" action="?module=fe&action=salvarResultados" id="resultadoForm">
                <div class="p-3 border rounded mb-3">
                    <h4 class="text-center mb-4">Registro de Jogos Agendados</h4>

                    <div class="mb-3">
                        <label for="vencedor" class="form-label">Jogador Vencedor:</label>
                        <select id="vencedor" name="vencedor" class="form-select" required>
                            <option value="" disabled <?= empty($vencedorId) ? 'selected' : '' ?>>Selecione o vencedor</option>
                            <option value="<?= $jogador1Id ?>" <?= ($vencedorId == $jogador1Id) ? 'selected' : '' ?>><?= htmlspecialchars($nomeJogador1) ?></option>
                            <option value="<?= $jogador2Id ?>" <?= ($vencedorId == $jogador2Id) ? 'selected' : '' ?>><?= htmlspecialchars($nomeJogador2) ?></option>
                            <option value="WO_Duplo" disabled>WO Duplo</option>
                        </select>

                    </div>
                    <div class="mb-3">
                        <label for="resultado" class="form-label">Resultado:</label>
                        <select id="resultado" name="resultado" class="form-select" required>
                            <option value="" disabled <?= empty($resultado) ? 'selected' : '' ?>>Selecione o resultado</option>
                            <option value="2x0" <?= ($resultado == '2x0') ? 'selected' : '' ?>>2x0</option>
                            <option value="2x1" <?= ($resultado == '2x1') ? 'selected' : '' ?>>2x1</option>
                            <option value="WO" <?= ($resultado == 'WO') ? 'selected' : '' ?>>WO</option>
                            <option value="Abandono" <?= ($resultado == 'Abandono') ? 'selected' : '' ?>>Abandono</option>
                            <option value="FAV" <?= ($resultado == 'FAV') ? 'selected' : '' ?>>Falta com Aviso</option>
                        </select>

                    </div>
                    <div class="mb-3">
                        <label for="quemLevouBola" class="form-label">Quem levou a bola?</label>
                        <select id="quemLevouBola" name="quemLevouBola" class="form-select" required>
                            <option value="<?= $jogador1Id ?>" <?= ($quemLevouBola == $jogador1Id) ? 'selected' : '' ?>><?= htmlspecialchars($nomeJogador1) ?></option>
                            <option value="<?= $jogador2Id ?>" <?= ($quemLevouBola == $jogador2Id) ? 'selected' : '' ?>><?= htmlspecialchars($nomeJogador2) ?></option>
                            <option value="0" <?= ($quemLevouBola == 0) ? 'selected' : '' ?>>Não foram usadas bolas novas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="parciais" class="form-label">Parciais Detalhadas:</label>
                        <textarea id="parciais" name="parciais" class="form-control" placeholder="Exemplo: 6-4, 3-6, 10-8" rows="2"><?= htmlspecialchars($parciais) ?></textarea>

                    </div>
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações:</label>
                        <textarea id="observacoes" name="observacoes" class="form-control" rows="2"><?= htmlspecialchars($observacoes) ?></textarea>

                    </div>
                    <button type="submit" class="btn btn-primary">Registrar Resultado</button>
                </div>
            </form>
        <?php endif; ?>
<!-- REGISTRO DE RESULTADOS DE JOGOS AGENDADOS FIM -->


<!-- REGISTRO DE RESULTADOS DE JOGOS POSSÍVEIS -->
<?php
// **Verificar se há uma rodada em andamento**
$rodada = new rodada3;
$rodadaEmAndamento = $rodada->em_andamento();

if (!$rodadaEmAndamento) {
    echo '<p class="text-center text-danger">Nenhuma rodada está em andamento.</p>';
    return;
}
//if ($diaSemana == 3 && $horaAtual >= 12 && $horaAtual < 18) {
//    echo '<p class="text-center text-danger">O registro de jogos está suspenso entre 12h e 18h das quartas-feiras.</p>';
//    return;
//}

// **Obter dados do jogador**
$jogador = new jogador3;
$dadosJogador = $jogador->dados($id);

// **Verificar quais rankings o jogador participa**
$categoriasParaBuscar = [];
if ($dadosJogador['ranking_misto'] == 1) {
    $categoriasParaBuscar[] = ['categoria' => $dadosJogador['categoria_misto'], 'barragem' => $dadosJogador['barragem_misto'], 'ranking' => 'misto'];
}
if ($dadosJogador['ranking_feminino'] == 1) {
    $categoriasParaBuscar[] = ['categoria' => $dadosJogador['categoria_feminino'], 'barragem' => $dadosJogador['barragem_feminino'], 'ranking' => 'feminino'];
}

// **Verificar se já tem jogo possivel cadastrado**
$jogos = new jogos3;
$jogoAgendado = $jogos->tem_jogo_agendado($id);
$jogoPossivel = $jogos->tem_jogo_possivel($id);

if ($jogoAgendado) {
    $categoriaAgendada = strtolower($jogoAgendado[0]["categoria"]);
    $categoriasParaBuscar = array_filter($categoriasParaBuscar, function ($categoria) use ($categoriaAgendada) {
        return ($categoriaAgendada == 'wta' && $categoria['ranking'] == 'misto') || ($categoriaAgendada != 'wta' && $categoria['ranking'] == 'feminino');
    });
}

if ($jogoPossivel){
    $jogo = $jogoPossivel[0];
    $adversario_id = ($jogo['jogador1_id'] == $id) ? $jogo['jogador2_id'] : $jogo['jogador1_id'];
}

foreach ($categoriasParaBuscar as $categoriaInfo) {
    // **Buscar adversários possíveis**
    $sqlAdversarios = "SELECT id, nome_completo, posicao_misto, posicao_feminino FROM jogador
                        WHERE jogo_agendado = 0 
                        AND categoria_" . $categoriaInfo['ranking'] . " = :categoria
                        AND barragem_" . $categoriaInfo['ranking'] . " = :barragem
                        AND id != :jogador_id";
    $adversarios = $banco->consultar($sqlAdversarios, [
        "categoria" => $categoriaInfo['categoria'],
        "barragem" => $categoriaInfo['barragem'],
        "jogador_id" => $id
    ]);

    echo '<form method="POST" action="?module=fe&action=registrarJogoPossivel" id="jogoPossivelForm">';
    echo '<input type="hidden" name="categoria" id="categoria" value="' . htmlspecialchars($categoriaInfo['categoria']) . '">';
    echo '<input type="hidden" name="barragem" id="barragem" value="' . htmlspecialchars($categoriaInfo['barragem']) . '">';
    echo '<div class="p-3 border rounded mb-3">';
    echo '<h4 class="text-center mb-2">Registro de Jogo Possível</h4>';
    echo '<p class="text-center text-danger"><small>O cadastro de jogo deve ocorrer até quarta-feira às 12h00.</small></p>';

    echo '<div class="mb-3">';
    echo '<label for="adversario" class="form-label">Adversário:</label>';
    echo '<select id="adversario" name="adversario" class="form-select" required onchange="atualizarNomeVencedor()">';
    echo '<option value="" disabled selected>Selecione um adversário</option>';
    foreach ($adversarios as $adversario) {
        $selected = ($jogoPossivel && $adversario["id"] == $adversario_id) ? ' selected' : '';
        echo '<option value="' . $adversario["id"] . '"' . $selected . '>';
        echo '#' . htmlspecialchars($adversario['posicao_' . $categoriaInfo['ranking']]) . ' ' . htmlspecialchars($adversario["nome_completo"]);
        echo '</option>';
    }
    echo '</select>';
    echo '</div>';

    echo '<div class="mb-3">';
    echo '<label for="vencedor" class="form-label">Jogador Vencedor:</label>';
    $vencedorSelecionado = ($jogoPossivel && $jogoPossivel[0]["vencedor_id"] == $id) ? $id : 0;
    echo '<select id="vencedor" name="vencedor" class="form-select" required>';
    echo '<option value="" disabled>Selecione o vencedor</option>';
    echo '<option value="' . $id . '"' . ($vencedorSelecionado == $id ? ' selected' : '') . '>' . htmlspecialchars($dadosJogador["nome_completo"]) . '</option>';
    echo '<option id="opcaoAdversario" value="0"' . ($vencedorSelecionado == 0 ? ' selected' : '') . '>Meu oponente</option>';
    echo '</select>';
    echo '</div>';

    echo '<div class="mb-3">';
    echo '<label for="resultado" class="form-label">Resultado:</label>';
    $resultadoSelecionado = $jogoPossivel ? $jogoPossivel[0]["resultado"] : '';
    echo '<select id="resultado" name="resultado" class="form-select" required>';
    echo '<option value="" disabled' . ($resultadoSelecionado == '' ? ' selected' : '') . '>Selecione o resultado</option>';
    echo '<option value="2x0"' . ($resultadoSelecionado == "2x0" ? ' selected' : '') . '>2x0</option>';
    echo '<option value="2x1"' . ($resultadoSelecionado == "2x1" ? ' selected' : '') . '>2x1</option>';
    echo '<option value="WO"' . ($resultadoSelecionado == "WO" ? ' selected' : '') . '>WO</option>';
    echo '<option value="Abandono"' . ($resultadoSelecionado == "Abandono" ? ' selected' : '') . '>Abandono</option>';
    echo '<option value="FAV"' . ($resultadoSelecionado == "FAV" ? ' selected' : '') . '>Falta com Aviso</option>';
    echo '</select>';
    echo '</div>';

    echo '<div class="mb-3">';
    echo '<label for="parciais" class="form-label">Parciais:</label>';
    $parciais = $jogoPossivel ? htmlspecialchars($jogoPossivel[0]["parciais"]) : '';
    echo '<input type="text" id="parciais" name="parciais" class="form-control" placeholder="Exemplo: 6/4, 3/6, 7/5" value="' . $parciais . '">';
    echo '</div>';

    echo '<div class="mb-3">';
    echo '<label for="bolas" class="form-label">Quem levou bolas novas?</label>';
    $bolasNovas = $jogoPossivel ? $jogoPossivel[0]["quem_levou_bola"] : '';
    echo '<select id="bolas" name="bolas" class="form-select" required>';
    echo '<option value="" disabled ' . (!$bolasNovas ? 'selected' : '') . '>Selecione</option>';
    echo '<option value="' . $id . '" ' . ($bolasNovas == $id ? 'selected' : '') . '>' . htmlspecialchars($dadosJogador["nome_completo"]) . '</option>';
    echo '<option id="opcaoBolasAdversario" value="0" ' . ($bolasNovas == 0 ? 'selected' : '') . '>Meu oponente</option>';
    echo '</select>';
    echo '</div>';

    echo '<div class="mb-3">';
    echo '<label for="observacoes" class="form-label">Observações:</label>';
    $observacoes = $jogoPossivel ? htmlspecialchars($jogoPossivel[0]["observacoes"]) : '';
    echo '<textarea id="observacoes" name="observacoes" class="form-control">' . $observacoes . '</textarea>';
    echo '</div>';

    $textoBotao = $jogoPossivel ? "Atualizar Jogo" : "Registrar Jogo";
    echo '<button type="submit" class="btn btn-primary">' . $textoBotao . '</button>';

    echo '</div>';
    echo '</form>';
}
?>

<!-- REGISTRO DE RESULTADOS DE JOGOS POSSÍVEIS FIM -->


<!-- ATUALIZAR CONTATOS -->
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
            document.getElementById("contatoForm").addEventListener("submit", function (event) {
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
<!-- ATUALIZAR CONTATOS FIM -->

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
            document.getElementById("senhaForm").addEventListener("submit", function (event) {
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
<!-- ALTERAÇÃO DE SENHA FIM -->




    </div></div>