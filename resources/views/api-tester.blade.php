<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auth API — Tester</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        .response-box { min-height: 3rem; max-height: 20rem; overflow-y: auto; }
        input:focus, textarea:focus { outline: none; }
        .tab-btn.active { background: #1e40af; color: #fff; }
        .tab-btn { transition: all .15s; }
        .section { display: none; }
        .section.active { display: block; }
        .toast { animation: slideIn .3s ease; }
        @keyframes slideIn { from { transform: translateY(1rem); opacity: 0 } to { transform: none; opacity: 1 } }
    </style>
</head>
<body class="h-full bg-gray-950 text-gray-100">

<!-- BARRA SUPERIOR: status do token -->
<header class="fixed top-0 left-0 right-0 z-30 bg-gray-900 border-b border-gray-800 px-6 py-3 flex items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <span class="text-blue-400 font-bold text-lg tracking-tight">Auth API</span>
        <span class="text-gray-600 text-sm">tester</span>
    </div>

    <div class="flex items-center gap-4 text-sm">
        <!-- indicador de auth -->
        <div class="flex items-center gap-2">
            <span id="auth-dot" class="w-2 h-2 rounded-full bg-red-500"></span>
            <span id="auth-label" class="text-gray-400">Não autenticado</span>
        </div>
        <!-- usuário logado -->
        <div id="user-chip" class="hidden items-center gap-2 bg-blue-950 border border-blue-800 rounded-full px-3 py-1">
            <span class="text-blue-300 text-xs mono" id="user-email-chip">—</span>
        </div>
        <!-- TTL do token -->
        <div id="token-ttl" class="hidden mono text-xs text-gray-500"></div>
        <!-- botão limpar -->
        <button onclick="clearToken()" class="text-xs text-red-500 hover:text-red-400 border border-red-900 hover:border-red-700 px-2 py-1 rounded transition">
            Limpar token
        </button>
    </div>
</header>

<div class="flex h-full pt-14">

    <!-- SIDEBAR: navegação -->
    <nav class="fixed top-14 left-0 bottom-0 w-52 bg-gray-900 border-r border-gray-800 flex flex-col py-4 gap-1 px-3 overflow-y-auto">
        <p class="text-xs text-gray-600 uppercase tracking-widest px-2 mb-1">Autenticação</p>
        <button class="tab-btn active text-left px-3 py-2 rounded text-sm text-gray-300 hover:bg-gray-800" data-tab="register">Registrar</button>
        <button class="tab-btn text-left px-3 py-2 rounded text-sm text-gray-300 hover:bg-gray-800" data-tab="login">Login</button>
        <button class="tab-btn text-left px-3 py-2 rounded text-sm text-gray-300 hover:bg-gray-800" data-tab="logout">Logout / Refresh</button>

        <p class="text-xs text-gray-600 uppercase tracking-widest px-2 mt-4 mb-1">Conta</p>
        <button class="tab-btn text-left px-3 py-2 rounded text-sm text-gray-300 hover:bg-gray-800" data-tab="me">Meu perfil</button>
        <button class="tab-btn text-left px-3 py-2 rounded text-sm text-gray-300 hover:bg-gray-800" data-tab="profile">Editar perfil</button>
        <button class="tab-btn text-left px-3 py-2 rounded text-sm text-gray-300 hover:bg-gray-800" data-tab="password">Alterar senha</button>

        <p class="text-xs text-gray-600 uppercase tracking-widest px-2 mt-4 mb-1">E-mail</p>
        <button class="tab-btn text-left px-3 py-2 rounded text-sm text-gray-300 hover:bg-gray-800" data-tab="resend">Reenviar verificação</button>
        <button class="tab-btn text-left px-3 py-2 rounded text-sm text-gray-300 hover:bg-gray-800" data-tab="forgot">Esqueci a senha</button>
        <button class="tab-btn text-left px-3 py-2 rounded text-sm text-gray-300 hover:bg-gray-800" data-tab="reset">Redefinir senha</button>
    </nav>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="ml-52 flex-1 flex flex-col lg:flex-row gap-6 p-6 min-h-0">

        <!-- PAINEL ESQUERDO: formulários -->
        <div class="flex-1 min-w-0">

            <!-- REGISTRAR -->
            <div class="section active" id="sec-register">
                <h2 class="text-base font-semibold mb-4 text-gray-200">POST /api/auth/register</h2>
                <div class="space-y-3">
                    <div><label class="label">Nome</label><input id="reg-name" type="text" placeholder="João Silva" class="inp"></div>
                    <div><label class="label">E-mail</label><input id="reg-email" type="email" placeholder="joao@email.com" class="inp"></div>
                    <div><label class="label">Senha</label><input id="reg-pass" type="password" placeholder="Senha@1234" class="inp"></div>
                    <div><label class="label">Confirmar senha</label><input id="reg-pass2" type="password" placeholder="Senha@1234" class="inp"></div>
                    <button onclick="doRegister()" class="btn-primary">Registrar</button>
                </div>
            </div>

            <!-- LOGIN -->
            <div class="section" id="sec-login">
                <h2 class="text-base font-semibold mb-4 text-gray-200">POST /api/auth/login</h2>
                <div class="space-y-3">
                    <div><label class="label">E-mail</label><input id="log-email" type="email" placeholder="joao@email.com" class="inp"></div>
                    <div><label class="label">Senha</label><input id="log-pass" type="password" placeholder="Senha@1234" class="inp"></div>
                    <button onclick="doLogin()" class="btn-primary">Entrar</button>
                </div>
            </div>

            <!-- LOGOUT / REFRESH -->
            <div class="section" id="sec-logout">
                <h2 class="text-base font-semibold mb-4 text-gray-200">Sessão</h2>
                <div class="space-y-3">
                    <div class="p-4 bg-gray-800 rounded-lg">
                        <p class="text-sm text-gray-400 mb-3">Token atual:</p>
                        <p id="token-preview" class="mono text-xs text-gray-500 break-all">Nenhum token armazenado.</p>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="doLogout()" class="btn-danger flex-1">POST /logout</button>
                        <button onclick="doRefresh()" class="btn-secondary flex-1">POST /refresh</button>
                    </div>
                </div>
            </div>

            <!-- ME -->
            <div class="section" id="sec-me">
                <h2 class="text-base font-semibold mb-4 text-gray-200">GET /api/auth/me</h2>
                <p class="text-sm text-gray-500 mb-4">Retorna os dados do usuário autenticado. Requer e-mail verificado.</p>
                <button onclick="doMe()" class="btn-primary">Buscar meu perfil</button>
            </div>

            <!-- EDITAR PERFIL -->
            <div class="section" id="sec-profile">
                <h2 class="text-base font-semibold mb-4 text-gray-200">PUT /api/auth/profile</h2>
                <div class="space-y-3">
                    <div><label class="label">Nome</label><input id="pro-name" type="text" placeholder="João Silva" class="inp"></div>
                    <div><label class="label">E-mail</label><input id="pro-email" type="email" placeholder="joao@email.com" class="inp"></div>
                    <button onclick="doProfile()" class="btn-primary">Salvar alterações</button>
                </div>
            </div>

            <!-- ALTERAR SENHA -->
            <div class="section" id="sec-password">
                <h2 class="text-base font-semibold mb-4 text-gray-200">PUT /api/auth/password</h2>
                <div class="space-y-3">
                    <div><label class="label">Senha atual</label><input id="pw-current" type="password" placeholder="SenhaAtual@1" class="inp"></div>
                    <div><label class="label">Nova senha</label><input id="pw-new" type="password" placeholder="NovaSenha@2" class="inp"></div>
                    <div><label class="label">Confirmar nova senha</label><input id="pw-new2" type="password" placeholder="NovaSenha@2" class="inp"></div>
                    <button onclick="doChangePass()" class="btn-primary">Alterar senha</button>
                </div>
            </div>

            <!-- REENVIAR VERIFICAÇÃO -->
            <div class="section" id="sec-resend">
                <h2 class="text-base font-semibold mb-4 text-gray-200">POST /api/auth/email/resend</h2>
                <p class="text-sm text-gray-500 mb-4">Reenvia o e-mail de verificação para o usuário autenticado. O e-mail será registrado no <code class="mono text-blue-400">storage/logs/laravel.log</code>.</p>
                <button onclick="doResend()" class="btn-primary">Reenviar verificação</button>
            </div>

            <!-- ESQUECI SENHA -->
            <div class="section" id="sec-forgot">
                <h2 class="text-base font-semibold mb-4 text-gray-200">POST /api/auth/forgot-password</h2>
                <p class="text-sm text-gray-500 mb-4">Envia o link de redefinição para o e-mail. O token ficará registrado no <code class="mono text-blue-400">storage/logs/laravel.log</code>.</p>
                <div class="space-y-3">
                    <div><label class="label">E-mail</label><input id="fg-email" type="email" placeholder="joao@email.com" class="inp"></div>
                    <button onclick="doForgot()" class="btn-primary">Enviar link de reset</button>
                </div>
            </div>

            <!-- REDEFINIR SENHA -->
            <div class="section" id="sec-reset">
                <h2 class="text-base font-semibold mb-4 text-gray-200">POST /api/auth/reset-password</h2>
                <p class="text-sm text-gray-500 mb-3">Cole o token recebido no log. O token invalida todos os JWTs ativos (<code class="mono text-blue-400">token_version</code> é incrementado).</p>
                <div class="space-y-3">
                    <div><label class="label">E-mail</label><input id="rs-email" type="email" placeholder="joao@email.com" class="inp"></div>
                    <div><label class="label">Token (do log)</label><input id="rs-token" type="text" placeholder="05c506..." class="inp mono text-xs"></div>
                    <div><label class="label">Nova senha</label><input id="rs-pass" type="password" placeholder="NovaSenha@3" class="inp"></div>
                    <div><label class="label">Confirmar</label><input id="rs-pass2" type="password" placeholder="NovaSenha@3" class="inp"></div>
                    <button onclick="doReset()" class="btn-primary">Redefinir senha</button>
                </div>
            </div>

        </div>

        <!-- PAINEL DIREITO: resposta da API -->
        <div class="w-full lg:w-96 flex flex-col gap-4 shrink-0">
            <!-- request info -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase tracking-widest">Última requisição</span>
                    <span id="req-method" class="text-xs mono font-semibold text-gray-600">—</span>
                </div>
                <p id="req-url" class="mono text-xs text-blue-400 truncate">—</p>
            </div>

            <!-- status badge + resposta JSON -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 flex-1">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs text-gray-500 uppercase tracking-widest">Resposta</span>
                    <div class="flex items-center gap-2">
                        <span id="res-status-badge" class="text-xs mono px-2 py-0.5 rounded font-semibold bg-gray-800 text-gray-500">—</span>
                        <button onclick="copyResponse()" class="text-xs text-gray-600 hover:text-gray-300 transition">copiar</button>
                    </div>
                </div>
                <pre id="res-body" class="response-box mono text-xs text-gray-300 whitespace-pre-wrap break-all">Faça uma requisição para ver a resposta aqui.</pre>
            </div>

            <!-- token decodificado -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase tracking-widest">JWT Payload</span>
                    <span id="token-ver-badge" class="hidden mono text-xs px-2 py-0.5 rounded bg-blue-950 text-blue-400 border border-blue-900">v<span id="token-ver-val"></span></span>
                </div>
                <pre id="jwt-payload" class="mono text-xs text-gray-500 whitespace-pre-wrap">Faça login para ver o payload.</pre>
            </div>
        </div>

    </main>
</div>

<!-- TOAST -->
<div id="toast-container" class="fixed bottom-6 right-6 flex flex-col gap-2 z-50 pointer-events-none"></div>

<!-- ESTILOS INLINE (classes Tailwind usadas como utilitários) -->
<style>
    .label  { display:block; font-size:.75rem; color:#9ca3af; margin-bottom:.375rem; }
    .inp    { width:100%; background:#111827; border:1px solid #374151; border-radius:.5rem; padding:.5rem .75rem; font-size:.875rem; color:#f3f4f6; transition: border-color .15s; }
    .inp:focus { border-color:#3b82f6; }
    .btn-primary  { background:#1d4ed8; color:#fff; border-radius:.5rem; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; cursor:pointer; transition: background .15s; }
    .btn-primary:hover { background:#1e40af; }
    .btn-secondary { background:#1f2937; color:#d1d5db; border:1px solid #374151; border-radius:.5rem; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; cursor:pointer; transition: background .15s; }
    .btn-secondary:hover { background:#374151; }
    .btn-danger { background:#7f1d1d; color:#fca5a5; border-radius:.5rem; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; cursor:pointer; transition: background .15s; }
    .btn-danger:hover { background:#991b1b; }
</style>

<script>
const BASE = '/api/auth';

// ─── Estado local ─────────────────────────────────────────────────────────────
let _token = localStorage.getItem('api_token') || null;

function saveToken(t) {
    _token = t;
    if (t) localStorage.setItem('api_token', t);
    else localStorage.removeItem('api_token');
    updateHeader();
}

function clearToken() { saveToken(null); showToast('Token removido.', 'info'); }

// ─── Atualiza barra superior e painel JWT ─────────────────────────────────────
function updateHeader() {
    const dot   = document.getElementById('auth-dot');
    const label = document.getElementById('auth-label');
    const chip  = document.getElementById('user-chip');
    const ttl   = document.getElementById('token-ttl');
    const prev  = document.getElementById('token-preview');
    const jwtEl = document.getElementById('jwt-payload');
    const verBadge = document.getElementById('token-ver-badge');
    const verVal   = document.getElementById('token-ver-val');

    if (!_token) {
        dot.className   = 'w-2 h-2 rounded-full bg-red-500';
        label.textContent = 'Não autenticado';
        chip.classList.add('hidden'); chip.classList.remove('flex');
        ttl.classList.add('hidden');
        if (prev) prev.textContent = 'Nenhum token armazenado.';
        jwtEl.textContent = 'Faça login para ver o payload.';
        verBadge.classList.add('hidden');
        return;
    }

    // Decodifica payload do JWT (sem verificar assinatura — só visualização)
    try {
        const parts   = _token.split('.');
        const payload = JSON.parse(atob(parts[1].replace(/-/g,'+').replace(/_/g,'/')));
        const exp     = payload.exp ? new Date(payload.exp * 1000) : null;
        const now     = Date.now() / 1000;
        const expired = payload.exp && now > payload.exp;

        dot.className  = expired ? 'w-2 h-2 rounded-full bg-yellow-500' : 'w-2 h-2 rounded-full bg-green-500';
        label.textContent = expired ? 'Token expirado' : 'Autenticado';

        if (payload.token_version !== undefined) {
            verBadge.classList.remove('hidden');
            verVal.textContent = payload.token_version;
        }

        if (exp) {
            const diff = Math.max(0, payload.exp - now);
            const mins = Math.floor(diff / 60), secs = Math.floor(diff % 60);
            ttl.textContent = expired ? 'expirado' : `expira em ${mins}m ${secs}s`;
            ttl.classList.remove('hidden');
        }

        jwtEl.textContent = JSON.stringify(payload, null, 2);
        if (prev) prev.textContent = _token;
    } catch(e) {
        jwtEl.textContent = 'Token inválido.';
    }
}

// ─── Requisições ─────────────────────────────────────────────────────────────
async function api(method, path, body = null, useToken = false) {
    const url = BASE + path;
    document.getElementById('req-method').textContent = method;
    document.getElementById('req-url').textContent    = url;

    const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
    if (useToken && _token) headers['Authorization'] = `Bearer ${_token}`;

    const opts = { method, headers };
    if (body) opts.body = JSON.stringify(body);

    try {
        const res  = await fetch(url, opts);
        const data = await res.json().catch(() => ({}));
        renderResponse(res.status, data);
        return { status: res.status, data };
    } catch(err) {
        renderResponse(0, { error: err.message });
        return { status: 0, data: {} };
    }
}

function renderResponse(status, data) {
    const badge = document.getElementById('res-status-badge');
    const body  = document.getElementById('res-body');

    badge.textContent = status || 'ERR';
    badge.className   = 'text-xs mono px-2 py-0.5 rounded font-semibold ';
    if      (status >= 200 && status < 300) badge.className += 'bg-green-950 text-green-400 border border-green-900';
    else if (status >= 400 && status < 500) badge.className += 'bg-yellow-950 text-yellow-400 border border-yellow-900';
    else if (status >= 500)                 badge.className += 'bg-red-950 text-red-400 border border-red-900';
    else                                    badge.className += 'bg-gray-800 text-gray-500';

    body.textContent = JSON.stringify(data, null, 2);
}

function copyResponse() {
    navigator.clipboard.writeText(document.getElementById('res-body').textContent)
        .then(() => showToast('Copiado!', 'success'));
}

// ─── Handlers ────────────────────────────────────────────────────────────────
async function doRegister() {
    const r = await api('POST', '/register', {
        name: v('reg-name'), email: v('reg-email'),
        password: v('reg-pass'), password_confirmation: v('reg-pass2'),
    });
    if (r.status === 201) {
        saveToken(r.data.token);
        showToast('Registrado! Verifique o e-mail no log.', 'success');
        setChip(r.data.user?.email);
    }
}

async function doLogin() {
    const r = await api('POST', '/login', { email: v('log-email'), password: v('log-pass') });
    if (r.status === 200) {
        saveToken(r.data.access_token);
        showToast('Login realizado com sucesso.', 'success');
        // Busca e-mail do /me
        const me = await api('GET', '/me', null, true);
        if (me.status === 200) setChip(me.data?.email);
    }
}

async function doLogout() {
    const r = await api('POST', '/logout', null, true);
    if (r.status === 200) { saveToken(null); showToast('Logout realizado.', 'info'); }
}

async function doRefresh() {
    const r = await api('POST', '/refresh', null, true);
    if (r.status === 200) { saveToken(r.data.access_token); showToast('Token atualizado.', 'success'); }
}

async function doMe() {
    const r = await api('GET', '/me', null, true);
    if (r.status === 200) setChip(r.data?.email);
}

async function doProfile() {
    const r = await api('PUT', '/profile', { name: v('pro-name'), email: v('pro-email') }, true);
    if (r.status === 200) { setChip(r.data?.user?.email); showToast('Perfil atualizado.', 'success'); }
}

async function doChangePass() {
    const r = await api('PUT', '/password', {
        current_password: v('pw-current'),
        password: v('pw-new'), password_confirmation: v('pw-new2'),
    }, true);
    if (r.status === 200) { saveToken(null); showToast('Senha alterada. Faça login novamente.', 'info'); }
}

async function doResend() {
    await api('POST', '/email/resend', null, true);
    showToast('Link reenviado. Verifique storage/logs/laravel.log', 'info');
}

async function doForgot() {
    await api('POST', '/forgot-password', { email: v('fg-email') });
    showToast('Se cadastrado, o link está em storage/logs/laravel.log', 'info');
}

async function doReset() {
    const r = await api('POST', '/reset-password', {
        email: v('rs-email'), token: v('rs-token'),
        password: v('rs-pass'), password_confirmation: v('rs-pass2'),
    });
    if (r.status === 200) showToast('Senha redefinida. Tokens antigos foram revogados.', 'success');
}

// ─── Utilitários ─────────────────────────────────────────────────────────────
const v = id => document.getElementById(id)?.value?.trim() ?? '';

function setChip(email) {
    const chip = document.getElementById('user-chip');
    if (!email) return;
    document.getElementById('user-email-chip').textContent = email;
    chip.classList.remove('hidden'); chip.classList.add('flex');
}

function showToast(msg, type = 'info') {
    const colors = { success: 'bg-green-950 border-green-800 text-green-300', info: 'bg-blue-950 border-blue-800 text-blue-300', error: 'bg-red-950 border-red-800 text-red-300' };
    const el = document.createElement('div');
    el.className = `toast pointer-events-auto ${colors[type]} border rounded-lg px-4 py-2 text-sm shadow-xl`;
    el.textContent = msg;
    document.getElementById('toast-container').appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

// ─── Tabs ────────────────────────────────────────────────────────────────────
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('sec-' + btn.dataset.tab)?.classList.add('active');

        // Preenche token preview ao abrir sessão
        if (btn.dataset.tab === 'logout') {
            const prev = document.getElementById('token-preview');
            if (prev) prev.textContent = _token ?? 'Nenhum token armazenado.';
        }
    });
});

// ─── Atualiza TTL a cada segundo ─────────────────────────────────────────────
setInterval(updateHeader, 1000);
updateHeader();
</script>
</body>
</html>
