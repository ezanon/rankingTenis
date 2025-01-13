<div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
            <h3 class="text-center mb-4">Login</h3>
            <form method="POST" action="?module=fe&action=feLogin">
                <div class="mb-3">
                    <label for="login" class="form-label">Usuário</label>
                    <input type="text" class="form-control" id="username" name="login" placeholder="Digite seu usuário" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="senha" placeholder="Digite sua senha" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
<!--            <div class="text-center mt-3">
                <a href="/forgot-password" class="text-decoration-none">Esqueceu a senha?</a>
            </div>-->
        </div>
    </div>

