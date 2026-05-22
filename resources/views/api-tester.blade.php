<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Auth API — Tester</title>
<style>
/* ── Reset & base ─────────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --bg:       #030712;
  --surface:  #111827;
  --surface2: #1f2937;
  --border:   #374151;
  --text:     #f3f4f6;
  --muted:    #9ca3af;
  --dim:      #6b7280;
  --blue:     #3b82f6;
  --blue-dk:  #1d4ed8;
  --mono: 'Courier New', Courier, monospace;
}

html, body { height: 100%; }
body {
  background: var(--bg);
  color: var(--text);
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  font-size: 14px;
  line-height: 1.5;
}

/* ── Header ───────────────────────────────────────────────────────── */
.hdr {
  position: fixed; top: 0; left: 0; right: 0; z-index: 30;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  padding: 12px 24px;
  display: flex; align-items: center; justify-content: space-between; gap: 16px;
  height: 57px;
}
.hdr-brand  { display: flex; align-items: center; gap: 8px; }
.hdr-title  { color: var(--blue); font-weight: 700; font-size: 1.05rem; }
.hdr-sub    { color: var(--dim); font-size: .875rem; }
.hdr-right  { display: flex; align-items: center; gap: 16px; font-size: .8125rem; }

.auth-status { display: flex; align-items: center; gap: 7px; }
.dot { width: 8px; height: 8px; border-radius: 50%; background: #ef4444; flex-shrink: 0; }
.dot.green  { background: #22c55e; }
.dot.yellow { background: #eab308; }
.auth-lbl   { color: var(--muted); }

.chip {
  display: none; align-items: center;
  background: #1e1b4b; border: 1px solid #3730a3;
  border-radius: 999px; padding: 3px 12px;
}
.chip.on { display: flex; }
.chip-email { color: #a5b4fc; font-family: var(--mono); font-size: .72rem; }

.ttl { display: none; font-family: var(--mono); font-size: .72rem; color: var(--dim); }
.ttl.on { display: block; }

.btn-clear {
  font-size: .72rem; color: #f87171; border: 1px solid #7f1d1d;
  background: none; border-radius: 6px; padding: 3px 8px; cursor: pointer;
  transition: color .15s, border-color .15s;
}
.btn-clear:hover { color: #fca5a5; border-color: #991b1b; }

/* ── Sidebar ──────────────────────────────────────────────────────── */
.sidebar {
  position: fixed; top: 57px; left: 0; bottom: 0; width: 196px;
  background: var(--surface);
  border-right: 1px solid var(--border);
  padding: 12px 10px;
  overflow-y: auto;
  display: flex; flex-direction: column; gap: 3px;
}
.grp {
  font-size: .67rem; color: var(--dim); text-transform: uppercase;
  letter-spacing: .08em; padding: 4px 8px; margin-top: 10px;
}
.grp:first-child { margin-top: 0; }
.tab-btn {
  text-align: left; padding: 7px 10px; border-radius: 7px;
  font-size: .875rem; color: var(--muted);
  background: none; border: none; cursor: pointer; width: 100%;
  transition: background .1s, color .1s;
}
.tab-btn:hover  { background: var(--surface2); color: var(--text); }
.tab-btn.active { background: #1e3a8a; color: #bfdbfe; }

/* ── Main layout ──────────────────────────────────────────────────── */
.main {
  margin-left: 196px;
  padding: 81px 24px 32px 220px;
  display: flex; gap: 24px; align-items: flex-start;
}

/* ── Sections ─────────────────────────────────────────────────────── */
.section { display: none; flex: 1; min-width: 0; }
.section.active { display: block; }
.sec-title { font-size: .9375rem; font-weight: 600; color: #e5e7eb; margin-bottom: 18px; }
.sec-desc  { font-size: .8125rem; color: var(--muted); margin-bottom: 14px; }

/* ── Form controls ────────────────────────────────────────────────── */
.field  { margin-bottom: 11px; }
.label  { display: block; font-size: .72rem; color: var(--muted); margin-bottom: 5px; }
.inp {
  width: 100%; background: #0f172a; border: 1px solid var(--border);
  border-radius: 7px; padding: 7px 11px; font-size: .875rem; color: var(--text);
  outline: none; transition: border-color .15s;
}
.inp:focus { border-color: var(--blue); }

.btn-primary {
  background: var(--blue-dk); color: #fff; border: none;
  border-radius: 7px; padding: 8px 20px; font-size: .875rem; font-weight: 500;
  cursor: pointer; transition: background .15s;
}
.btn-primary:hover { background: #1e40af; }
.btn-secondary {
  background: var(--surface2); color: #d1d5db; border: 1px solid var(--border);
  border-radius: 7px; padding: 8px 20px; font-size: .875rem; font-weight: 500;
  cursor: pointer; transition: background .15s;
}
.btn-secondary:hover { background: var(--border); }
.btn-danger {
  background: #7f1d1d; color: #fca5a5; border: none;
  border-radius: 7px; padding: 8px 20px; font-size: .875rem; font-weight: 500;
  cursor: pointer; transition: background .15s;
}
.btn-danger:hover { background: #991b1b; }
.btn-row { display: flex; gap: 10px; }
.btn-row > * { flex: 1; }

/* ── Token preview box ────────────────────────────────────────────── */
.token-box {
  background: var(--surface2); border: 1px solid var(--border);
  border-radius: 8px; padding: 14px; margin-bottom: 12px;
}
.token-box-lbl { font-size: .8rem; color: var(--muted); margin-bottom: 6px; }
.token-txt {
  font-family: var(--mono); font-size: .675rem; color: var(--dim); word-break: break-all;
}

/* ── Right panel ──────────────────────────────────────────────────── */
.rpanel {
  width: 372px; flex-shrink: 0;
  display: flex; flex-direction: column; gap: 14px;
  position: sticky; top: 72px;
}
.card {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 10px; padding: 14px;
}
.card-hdr {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 10px;
}
.plbl {
  font-size: .67rem; color: var(--dim); text-transform: uppercase; letter-spacing: .08em;
}
.req-method { font-family: var(--mono); font-size: .72rem; font-weight: 600; color: var(--dim); }
.req-url    { font-family: var(--mono); font-size: .72rem; color: #60a5fa; word-break: break-all; }

.badge {
  font-family: var(--mono); font-size: .72rem; padding: 2px 8px; border-radius: 5px;
  font-weight: 600; background: var(--surface2); color: var(--dim); border: 1px solid var(--border);
}
.badge.ok   { background: #052e16; color: #86efac; border-color: #166534; }
.badge.warn { background: #422006; color: #fde047; border-color: #713f12; }
.badge.err  { background: #3b0a0a; color: #fca5a5; border-color: #7f1d1d; }

.copy-btn {
  font-size: .72rem; color: var(--dim); background: none; border: none;
  cursor: pointer; transition: color .12s;
}
.copy-btn:hover { color: var(--text); }

.res-box {
  font-family: var(--mono); font-size: .675rem; color: #d1d5db;
  white-space: pre-wrap; word-break: break-all;
  max-height: 280px; overflow-y: auto;
  background: #0f172a; border-radius: 7px; padding: 10px; margin-top: 6px;
}
.jwt-ver {
  display: none; font-family: var(--mono); font-size: .72rem;
  padding: 2px 8px; border-radius: 5px;
  background: #1e1b4b; color: #a5b4fc; border: 1px solid #3730a3;
}
.jwt-payload {
  font-family: var(--mono); font-size: .675rem; color: var(--dim);
  white-space: pre-wrap; word-break: break-all;
  background: #0f172a; border-radius: 7px; padding: 10px; margin-top: 6px;
}

/* ── Toasts ───────────────────────────────────────────────────────── */
.toast-wrap {
  position: fixed; bottom: 22px; right: 22px;
  display: flex; flex-direction: column; gap: 7px; z-index: 50; pointer-events: none;
}
.toast {
  pointer-events: auto; padding: 8px 16px; border-radius: 8px;
  font-size: .875rem; border: 1px solid;
  animation: slideIn .22s ease; box-shadow: 0 4px 20px rgba(0,0,0,.45);
}
.toast.success { background: #052e16; border-color: #166534; color: #86efac; }
.toast.info    { background: #0c1a3a; border-color: #1e3a8a; color: #93c5fd; }
.toast.error   { background: #3b0a0a; border-color: #7f1d1d; color: #fca5a5; }
@keyframes slideIn {
  from { transform: translateY(8px); opacity: 0; }
  to   { transform: none; opacity: 1; }
}

code { font-family: var(--mono); font-size: .72rem; color: #93c5fd; }
</style>
</head>
<body>

<!-- ── HEADER ──────────────────────────────────────────────────────── -->
<header class="hdr">
  <div class="hdr-brand">
    <span class="hdr-title">Auth API</span>
    <span class="hdr-sub">tester</span>
  </div>
  <div class="hdr-right">
    <div class="auth-status">
      <span id="auth-dot" class="dot"></span>
      <span id="auth-lbl" class="auth-lbl">Não autenticado</span>
    </div>
    <div id="user-chip" class="chip">
      <span id="user-email" class="chip-email">—</span>
    </div>
    <span id="token-ttl" class="ttl"></span>
    <button onclick="clearToken()" class="btn-clear">Limpar token</button>
  </div>
</header>

<!-- ── SIDEBAR ─────────────────────────────────────────────────────── -->
<nav class="sidebar">
  <span class="grp">Autenticação</span>
  <button class="tab-btn active" data-tab="register">Registrar</button>
  <button class="tab-btn"        data-tab="login">Login</button>
  <button class="tab-btn"        data-tab="logout">Logout / Refresh</button>

  <span class="grp">Conta</span>
  <button class="tab-btn" data-tab="me">Meu perfil</button>
  <button class="tab-btn" data-tab="profile">Editar perfil</button>
  <button class="tab-btn" data-tab="password">Alterar senha</button>

  <span class="grp">E-mail</span>
  <button class="tab-btn" data-tab="resend">Reenviar verificação</button>
  <button class="tab-btn" data-tab="forgot">Esqueci a senha</button>
  <button class="tab-btn" data-tab="reset">Redefinir senha</button>
</nav>

<!-- ── MAIN ────────────────────────────────────────────────────────── -->
<div class="main">

  <!-- Formulários -->
  <div style="flex:1;min-width:0">

    <!-- REGISTRAR -->
    <div class="section active" id="sec-register">
      <p class="sec-title">POST /api/auth/register</p>
      <div class="field"><label class="label">Nome</label><input id="reg-name"  class="inp" type="text"     placeholder="João Silva"></div>
      <div class="field"><label class="label">E-mail</label><input id="reg-email" class="inp" type="email"    placeholder="joao@email.com"></div>
      <div class="field"><label class="label">Senha</label><input id="reg-pass"  class="inp" type="password" placeholder="Senha@1234"></div>
      <div class="field"><label class="label">Confirmar senha</label><input id="reg-pass2" class="inp" type="password" placeholder="Senha@1234"></div>
      <button onclick="doRegister()" class="btn-primary">Registrar</button>
    </div>

    <!-- LOGIN -->
    <div class="section" id="sec-login">
      <p class="sec-title">POST /api/auth/login</p>
      <div class="field"><label class="label">E-mail</label><input id="log-email" class="inp" type="email"    placeholder="joao@email.com"></div>
      <div class="field"><label class="label">Senha</label><input id="log-pass"  class="inp" type="password" placeholder="Senha@1234"></div>
      <button onclick="doLogin()" class="btn-primary">Entrar</button>
    </div>

    <!-- SESSÃO -->
    <div class="section" id="sec-logout">
      <p class="sec-title">Sessão</p>
      <div class="token-box">
        <p class="token-box-lbl">Token atual:</p>
        <p id="token-preview" class="token-txt">Nenhum token armazenado.</p>
      </div>
      <div class="btn-row">
        <button onclick="doLogout()"  class="btn-danger">POST /logout</button>
        <button onclick="doRefresh()" class="btn-secondary">POST /refresh</button>
      </div>
    </div>

    <!-- ME -->
    <div class="section" id="sec-me">
      <p class="sec-title">GET /api/auth/me</p>
      <p class="sec-desc">Retorna os dados do usuário autenticado. Requer e-mail verificado.</p>
      <button onclick="doMe()" class="btn-primary">Buscar meu perfil</button>
    </div>

    <!-- EDITAR PERFIL -->
    <div class="section" id="sec-profile">
      <p class="sec-title">PUT /api/auth/profile</p>
      <div class="field"><label class="label">Nome</label><input id="pro-name"  class="inp" type="text"  placeholder="João Silva"></div>
      <div class="field"><label class="label">E-mail</label><input id="pro-email" class="inp" type="email" placeholder="joao@email.com"></div>
      <button onclick="doProfile()" class="btn-primary">Salvar alterações</button>
    </div>

    <!-- ALTERAR SENHA -->
    <div class="section" id="sec-password">
      <p class="sec-title">PUT /api/auth/password</p>
      <div class="field"><label class="label">Senha atual</label><input id="pw-current" class="inp" type="password" placeholder="SenhaAtual@1"></div>
      <div class="field"><label class="label">Nova senha</label><input id="pw-new"     class="inp" type="password" placeholder="NovaSenha@2"></div>
      <div class="field"><label class="label">Confirmar nova senha</label><input id="pw-new2" class="inp" type="password" placeholder="NovaSenha@2"></div>
      <button onclick="doChangePass()" class="btn-primary">Alterar senha</button>
    </div>

    <!-- REENVIAR VERIFICAÇÃO -->
    <div class="section" id="sec-resend">
      <p class="sec-title">POST /api/auth/email/resend</p>
      <p class="sec-desc">Reenvia o e-mail de verificação. O link aparece em <code>storage/logs/laravel.log</code>.</p>
      <button onclick="doResend()" class="btn-primary">Reenviar verificação</button>
    </div>

    <!-- ESQUECI SENHA -->
    <div class="section" id="sec-forgot">
      <p class="sec-title">POST /api/auth/forgot-password</p>
      <p class="sec-desc">Envia o link de redefinição. O token aparece em <code>storage/logs/laravel.log</code>.</p>
      <div class="field"><label class="label">E-mail</label><input id="fg-email" class="inp" type="email" placeholder="joao@email.com"></div>
      <button onclick="doForgot()" class="btn-primary">Enviar link de reset</button>
    </div>

    <!-- REDEFINIR SENHA -->
    <div class="section" id="sec-reset">
      <p class="sec-title">POST /api/auth/reset-password</p>
      <p class="sec-desc">Cole o token do log. Revoga todos os JWTs ativos (<code>token_version</code> é incrementado).</p>
      <div class="field"><label class="label">E-mail</label><input id="rs-email" class="inp" type="email" placeholder="joao@email.com"></div>
      <div class="field"><label class="label">Token (do log)</label><input id="rs-token" class="inp" type="text" placeholder="05c506..." style="font-family:monospace;font-size:.72rem"></div>
      <div class="field"><label class="label">Nova senha</label><input id="rs-pass"  class="inp" type="password" placeholder="NovaSenha@3"></div>
      <div class="field"><label class="label">Confirmar</label><input id="rs-pass2" class="inp" type="password" placeholder="NovaSenha@3"></div>
      <button onclick="doReset()" class="btn-primary">Redefinir senha</button>
    </div>

  </div>

  <!-- Painel direito -->
  <div class="rpanel">

    <!-- Última requisição -->
    <div class="card">
      <div class="card-hdr">
        <span class="plbl">Última requisição</span>
        <span id="req-method" class="req-method">—</span>
      </div>
      <p id="req-url" class="req-url">—</p>
    </div>

    <!-- Resposta -->
    <div class="card">
      <div class="card-hdr">
        <span class="plbl">Resposta</span>
        <div style="display:flex;align-items:center;gap:8px">
          <span id="res-badge" class="badge">—</span>
          <button onclick="copyResponse()" class="copy-btn">copiar</button>
        </div>
      </div>
      <pre id="res-body" class="res-box">Faça uma requisição para ver a resposta aqui.</pre>
    </div>

    <!-- JWT Payload -->
    <div class="card">
      <div class="card-hdr">
        <span class="plbl">JWT Payload</span>
        <span id="jwt-ver" class="jwt-ver">v<span id="jwt-ver-val"></span></span>
      </div>
      <pre id="jwt-payload" class="jwt-payload">Faça login para ver o payload.</pre>
    </div>

  </div>
</div>

<div id="toasts" class="toast-wrap"></div>

<script>
const BASE = '/api/auth';
let _token = localStorage.getItem('api_token') || null;

/* ── Token state ─────────────────────────────────────────────────── */
function saveToken(t) {
  _token = t;
  t ? localStorage.setItem('api_token', t) : localStorage.removeItem('api_token');
  updateHeader();
}
function clearToken() { saveToken(null); toast('Token removido.', 'info'); }

/* ── Header update ───────────────────────────────────────────────── */
function updateHeader() {
  const dot     = document.getElementById('auth-dot');
  const lbl     = document.getElementById('auth-lbl');
  const chip    = document.getElementById('user-chip');
  const ttl     = document.getElementById('token-ttl');
  const prev    = document.getElementById('token-preview');
  const payload = document.getElementById('jwt-payload');
  const ver     = document.getElementById('jwt-ver');
  const verVal  = document.getElementById('jwt-ver-val');

  if (!_token) {
    dot.className = 'dot';
    lbl.textContent = 'Não autenticado';
    chip.classList.remove('on');
    ttl.classList.remove('on');
    if (prev) prev.textContent = 'Nenhum token armazenado.';
    payload.textContent = 'Faça login para ver o payload.';
    ver.style.display = 'none';
    return;
  }

  try {
    const parts = _token.split('.');
    const p = JSON.parse(atob(parts[1].replace(/-/g, '+').replace(/_/g, '/')));
    const now = Date.now() / 1000;
    const expired = p.exp && now > p.exp;

    dot.className = 'dot ' + (expired ? 'yellow' : 'green');
    lbl.textContent = expired ? 'Token expirado' : 'Autenticado';

    if (p.token_version !== undefined) {
      ver.style.display = 'inline-block';
      verVal.textContent = p.token_version;
    }

    if (p.exp) {
      const diff = Math.max(0, p.exp - now);
      ttl.textContent = expired
        ? 'expirado'
        : 'expira em ' + Math.floor(diff / 60) + 'm ' + Math.floor(diff % 60) + 's';
      ttl.classList.add('on');
    }

    payload.textContent = JSON.stringify(p, null, 2);
    if (prev) prev.textContent = _token;
  } catch (e) {
    payload.textContent = 'Token inválido.';
  }
}

/* ── API helper ──────────────────────────────────────────────────── */
async function api(method, path, body, withToken) {
  const url = BASE + path;
  document.getElementById('req-method').textContent = method;
  document.getElementById('req-url').textContent    = url;

  const headers = { 'Content-Type': 'application/json', Accept: 'application/json' };
  if (withToken && _token) headers['Authorization'] = 'Bearer ' + _token;

  const opts = { method, headers };
  if (body) opts.body = JSON.stringify(body);

  try {
    const res  = await fetch(url, opts);
    const data = await res.json().catch(function() { return {}; });
    renderRes(res.status, data);
    return { status: res.status, data: data };
  } catch (err) {
    renderRes(0, { error: err.message });
    return { status: 0, data: {} };
  }
}

function renderRes(status, data) {
  const badge = document.getElementById('res-badge');
  badge.textContent = status || 'ERR';
  badge.className   = 'badge';
  if      (status >= 200 && status < 300) badge.classList.add('ok');
  else if (status >= 400 && status < 500) badge.classList.add('warn');
  else if (status >= 500)                 badge.classList.add('err');
  document.getElementById('res-body').textContent = JSON.stringify(data, null, 2);
}

function copyResponse() {
  navigator.clipboard.writeText(document.getElementById('res-body').textContent)
    .then(function() { toast('Copiado!', 'success'); });
}

/* ── Handlers ────────────────────────────────────────────────────── */
async function doRegister() {
  var r = await api('POST', '/register', {
    name: v('reg-name'), email: v('reg-email'),
    password: v('reg-pass'), password_confirmation: v('reg-pass2'),
  });
  if (r.status === 201) {
    saveToken(r.data.token);
    setChip(r.data.user && r.data.user.email);
    toast('Registrado! Verifique o e-mail no log.', 'success');
  }
}

async function doLogin() {
  var r = await api('POST', '/login', { email: v('log-email'), password: v('log-pass') });
  if (r.status === 200) {
    saveToken(r.data.access_token);
    toast('Login realizado.', 'success');
    var me = await api('GET', '/me', null, true);
    if (me.status === 200) setChip(me.data && me.data.email);
  }
}

async function doLogout() {
  var r = await api('POST', '/logout', null, true);
  if (r.status === 200) { saveToken(null); toast('Logout realizado.', 'info'); }
}

async function doRefresh() {
  var r = await api('POST', '/refresh', null, true);
  if (r.status === 200) { saveToken(r.data.access_token); toast('Token renovado.', 'success'); }
}

async function doMe() {
  var r = await api('GET', '/me', null, true);
  if (r.status === 200) setChip(r.data && r.data.email);
}

async function doProfile() {
  var r = await api('PUT', '/profile', { name: v('pro-name'), email: v('pro-email') }, true);
  if (r.status === 200) {
    setChip(r.data.user && r.data.user.email);
    toast('Perfil atualizado.', 'success');
  }
}

async function doChangePass() {
  var r = await api('PUT', '/password', {
    current_password: v('pw-current'),
    password: v('pw-new'), password_confirmation: v('pw-new2'),
  }, true);
  if (r.status === 200) { saveToken(null); toast('Senha alterada. Faça login novamente.', 'info'); }
}

async function doResend() {
  await api('POST', '/email/resend', null, true);
  toast('Reenviado. Veja storage/logs/laravel.log', 'info');
}

async function doForgot() {
  await api('POST', '/forgot-password', { email: v('fg-email') });
  toast('Se cadastrado, o link está em storage/logs/laravel.log', 'info');
}

async function doReset() {
  var r = await api('POST', '/reset-password', {
    email: v('rs-email'), token: v('rs-token'),
    password: v('rs-pass'), password_confirmation: v('rs-pass2'),
  });
  if (r.status === 200) toast('Senha redefinida. Tokens antigos foram revogados.', 'success');
}

/* ── Tabs ────────────────────────────────────────────────────────── */
document.querySelectorAll('.tab-btn').forEach(function(btn) {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
    document.querySelectorAll('.section').forEach(function(s) { s.classList.remove('active'); });
    btn.classList.add('active');
    var sec = document.getElementById('sec-' + btn.dataset.tab);
    if (sec) sec.classList.add('active');
    if (btn.dataset.tab === 'logout') {
      var prev = document.getElementById('token-preview');
      if (prev) prev.textContent = _token || 'Nenhum token armazenado.';
    }
  });
});

/* ── Helpers ─────────────────────────────────────────────────────── */
function v(id) {
  var el = document.getElementById(id);
  return el ? el.value.trim() : '';
}

function setChip(email) {
  if (!email) return;
  document.getElementById('user-email').textContent = email;
  document.getElementById('user-chip').classList.add('on');
}

function toast(msg, type) {
  var el = document.createElement('div');
  el.className = 'toast ' + (type || 'info');
  el.textContent = msg;
  document.getElementById('toasts').appendChild(el);
  setTimeout(function() { el.remove(); }, 3500);
}

setInterval(updateHeader, 1000);
updateHeader();
</script>
</body>
</html>
