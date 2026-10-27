<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#0d4a43">
<title>Trip Expense Calculator</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,800&display=swap" rel="stylesheet">
<style>
:root{--bg:#eef2f3;--surface:#fff;--ink:#10282d;--mute:#5d7378;--line:#d5e0e2;--pine:#0d4a43;--gold:#f0b429;--red:#b3261e;--r:12px}
*{box-sizing:border-box}
html{-webkit-text-size-adjust:100%}
body{margin:0;background:var(--bg);color:var(--ink);font:15px/1.5 "Bricolage Grotesque",system-ui,-apple-system,"Segoe UI",sans-serif;padding-bottom:env(safe-area-inset-bottom)}
button,input,select,textarea{font:inherit;color:inherit}
:focus-visible{outline:2px solid var(--pine);outline-offset:2px}

/* top bar */
.top{position:sticky;top:0;z-index:20;background:var(--surface);border-bottom:1px solid var(--line);padding-top:env(safe-area-inset-top)}
.top-in{max-width:1120px;margin:0 auto;padding:10px 16px;display:flex;align-items:center;gap:12px}
.brand{display:flex;align-items:center;gap:10px;min-width:0;flex:1}
.brand svg{flex:none}
.brand h1{margin:0;font-size:18px;font-weight:800;letter-spacing:-.01em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.brand p{margin:0;font-size:12px;color:var(--mute);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

/* buttons */
.btn{min-height:44px;padding:0 16px;border-radius:10px;border:1px solid var(--line);background:var(--surface);cursor:pointer;font-weight:600;display:inline-flex;align-items:center;justify-content:center;gap:6px;transition:background .15s,border-color .15s}
.btn:hover{border-color:#aebfc2;background:#f6f9f9}
.btn.primary{background:var(--pine);border-color:var(--pine);color:#fff}
.btn.primary:hover{background:#0a3a35}
.btn.sm{min-height:36px;padding:0 12px;font-size:14px}
.btn.danger{color:var(--red)}
.btn.add{width:100%;border-style:dashed;color:var(--pine);margin-top:4px}

/* layout */
.page{max-width:1120px;margin:0 auto;padding:20px 16px 40px;display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:20px;align-items:start}
.card{background:var(--surface);border:1px solid var(--line);border-radius:var(--r);padding:20px;margin-bottom:16px}
.card-h{margin-bottom:14px}
.card-h h2{margin:0;font-size:17px;font-weight:700}
.card-h p{margin:2px 0 0;color:var(--mute);font-size:13px}

/* fields */
.grid{display:grid;gap:14px}
.g2{grid-template-columns:1fr 1fr}
.g4{grid-template-columns:repeat(4,1fr)}
.span{grid-column:1/-1}
.fld label,.lbl{display:block;font-size:13px;font-weight:600;color:var(--mute);margin-bottom:4px}
input,select,textarea{width:100%;min-height:44px;padding:9px 12px;border:1px solid var(--line);border-radius:10px;background:#fff;font-size:16px}
textarea{resize:vertical;min-height:84px}
input:disabled{background:#f1f5f5;color:var(--mute)}
input::placeholder,textarea::placeholder{color:#9db0b4}

/* fuel helper */
details.fuel{padding:0}
details.fuel summary{list-style:none;cursor:pointer;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;gap:12px}
details.fuel summary::-webkit-details-marker{display:none}
details.fuel summary::after{content:"+";font-size:22px;color:var(--mute);line-height:1}
details.fuel[open] summary::after{content:"–"}
details.fuel .body{padding:0 20px 20px}

/* expense rows */
.row{display:grid;grid-template-columns:150px minmax(0,1fr) 84px 120px 120px 40px;gap:10px;align-items:center;margin-bottom:10px}
.row.head{margin-bottom:6px;font-size:12px;font-weight:600;color:var(--mute)}
.row.head .r{text-align:right}
.lbl{display:none}
.amt{text-align:right;font-weight:700;font-variant-numeric:tabular-nums;white-space:nowrap}
.x{width:40px;height:40px;border:0;border-radius:8px;background:none;color:var(--red);font-size:22px;cursor:pointer}
.x:hover{background:#fdecea}

/* summary ticket */
.ticket{background:var(--pine);color:#eaf4f2;border-radius:var(--r);padding:22px;position:sticky;top:76px;box-shadow:0 12px 32px rgba(13,74,67,.28)}
.ticket .k{color:#a9cdc7;font-size:13px}
.ticket .big{font-size:40px;font-weight:800;letter-spacing:-.02em;line-height:1.1;margin:2px 0 16px;word-break:break-word}
.perf{border-top:2px dashed #3f7a72;margin:0 -22px 16px;position:relative}
.perf::before,.perf::after{content:"";position:absolute;top:-11px;width:20px;height:20px;border-radius:50%;background:var(--bg)}
.perf::before{left:-10px}.perf::after{right:-10px}
.ticket dl{margin:0;display:grid;grid-template-columns:1fr auto;gap:8px 12px}
.ticket dt{color:#a9cdc7}
.ticket dd{margin:0;text-align:right;font-weight:600;font-variant-numeric:tabular-nums}
.ticket .pp{color:var(--gold);font-size:18px}
.shares{margin-top:16px;padding-top:14px;border-top:2px dashed #3f7a72}
.shares .k{margin-bottom:8px}
.note{font-size:12px;color:#a9cdc7;margin:10px 0 0}

/* history */
.hist{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:4px 12px;padding:14px 0;border-top:1px solid var(--line)}
.hist:first-of-type{border-top:0;padding-top:0}
.hist b{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.hist small{color:var(--mute)}
.hist .tot{text-align:right;font-weight:700;font-variant-numeric:tabular-nums}
.hist .acts{grid-column:1/-1;display:flex;gap:8px;margin-top:6px}
.tag{font-size:11px;color:#7a5a00;background:#fff3cd;border-radius:4px;padding:1px 6px;margin-left:6px;font-weight:600}
.empty{color:var(--mute);margin:0}

/* toast + mobile bar */
#status{position:fixed;left:50%;bottom:24px;transform:translate(-50%,16px);max-width:min(92vw,460px);background:var(--ink);color:#fff;padding:12px 16px;border-radius:10px;opacity:0;pointer-events:none;transition:opacity .2s,transform .2s;z-index:40;font-size:14px}
#status.on{opacity:1;transform:translate(-50%,0)}
.mbar{display:none}

@media(max-width:980px){
  .page{grid-template-columns:1fr;padding-bottom:96px}
  .ticket{position:static}
  .mbar{display:flex;position:fixed;left:0;right:0;bottom:0;z-index:30;background:var(--pine);color:#fff;padding:10px 16px calc(10px + env(safe-area-inset-bottom));gap:18px;align-items:center}
  .mbar div{min-width:0}
  .mbar small{display:block;color:#a9cdc7;font-size:12px;line-height:1.2}
  .mbar b{font-size:18px;font-variant-numeric:tabular-nums;white-space:nowrap}
  .mbar a{margin-left:auto;color:var(--gold);font-weight:600;text-decoration:none;padding:10px 0}
  #status{bottom:84px}
}
@media(max-width:700px){
  .brand p{display:none}
  .card{padding:16px}
  .g4{grid-template-columns:1fr 1fr}
  details.fuel summary{padding:14px 16px}details.fuel .body{padding:0 16px 16px}
  .row.head{display:none}
  .lbl{display:block}
  .row{grid-template-columns:1fr 1fr;grid-template-areas:"cat x" "desc desc" "qty unit" "amt amt";gap:10px;padding:14px;border:1px solid var(--line);border-radius:10px;background:#f8fbfb}
  .c-cat{grid-area:cat}.c-desc{grid-area:desc}.c-qty{grid-area:qty}.c-unit{grid-area:unit}.c-amt{grid-area:amt;display:flex;justify-content:space-between;align-items:baseline;border-top:1px solid var(--line);padding-top:10px}
  .c-amt .lbl{margin:0}.c-amt .amt{font-size:18px}
  .row .x{grid-area:x;justify-self:end}
  .ticket .big{font-size:34px}
}
@media(max-width:380px){.g2{grid-template-columns:1fr}.btn .t{display:none}}
@media(prefers-reduced-motion:reduce){*{transition:none!important}}
</style>
</head>
<body>
<header class="top"><div class="top-in">
  <div class="brand">
    <svg width="32" height="32" viewBox="0 0 32 32" aria-hidden="true"><rect width="32" height="32" rx="8" fill="#0d4a43"/><circle cx="9" cy="22" r="3" fill="#f0b429"/><circle cx="23" cy="10" r="3" fill="none" stroke="#fff" stroke-width="2"/><path d="M11.5 20.5C16 17 14 14 20.5 11.5" stroke="#fff" stroke-width="2" stroke-dasharray="2 3" fill="none" stroke-linecap="round"/></svg>
    <div style="min-width:0"><h1>Trip expenses</h1><p>Calculate, split equally, keep history</p></div>
  </div>
  <button class="btn" id="new" type="button">New<span class="t">&nbsp;trip</span></button>
  <button class="btn primary" id="save" type="button">Save<span class="t">&nbsp;trip</span></button>
</div></header>

<div class="page">
  <main>
    <section class="card">
      <div class="card-h"><h2>Trip details</h2><p>Name the trip and say who is going.</p></div>
      <div class="grid g2">
        <div class="fld span"><label for="name">Trip name</label><input id="name" placeholder="Dhaka to Cox's Bazar" autocomplete="off"></div>
        <div class="fld"><label for="from">From</label><input id="from" autocomplete="off"></div>
        <div class="fld"><label for="to">To</label><input id="to" autocomplete="off"></div>
        <div class="fld"><label for="trav">Travelers</label><input id="trav" type="number" inputmode="numeric" min="1" value="1"></div>
        <div class="fld"><label for="days">Days</label><input id="days" type="number" inputmode="numeric" min="1" value="1"></div>
        <div class="fld span"><label for="members">Team members <span style="font-weight:400">(optional, one name per line)</span></label><textarea id="members" rows="3" placeholder="Jahid&#10;Rafi&#10;Sumon"></textarea></div>
      </div>
    </section>

    <section class="card">
      <div class="card-h"><h2>Expenses</h2><p>Amount = quantity × unit cost. For a single item, keep quantity at 1.</p></div>
      <div class="row head" aria-hidden="true"><span>Category</span><span>Description</span><span>Qty</span><span>Unit cost</span><span class="r">Amount</span><span></span></div>
      <div id="rows"></div>
      <button class="btn add" id="add" type="button">+ Add expense</button>
    </section>

    <details class="card fuel">
      <summary><span><b>Fuel cost helper</b><br><small style="color:var(--mute)">Work out fuel from distance and mileage</small></span></summary>
      <div class="body"><div class="grid g4">
        <div class="fld"><label for="f-dist">Distance (km)</label><input id="f-dist" type="number" inputmode="decimal" min="0" step="any"></div>
        <div class="fld"><label for="f-mpl">Mileage (km/l)</label><input id="f-mpl" type="number" inputmode="decimal" min="0" step="any" value="12"></div>
        <div class="fld"><label for="f-price">Price per litre</label><input id="f-price" type="number" inputmode="decimal" min="0" step="any"></div>
        <div class="fld"><label for="f-rt">Trip type</label><select id="f-rt"><option value="2">Round trip</option><option value="1">One way</option></select></div>
      </div>
      <button class="btn" id="f-add" type="button" style="margin-top:14px">Add fuel cost to expenses</button></div>
    </details>
  </main>

  <aside id="summary">
    <div class="ticket" aria-live="polite">
      <div class="k">Total cost</div>
      <div class="big" id="t-total">৳0.00</div>
      <div class="perf"></div>
      <dl>
        <dt>Per person</dt><dd class="pp" id="t-pp">৳0.00</dd>
        <dt>Per day</dt><dd id="t-pd">৳0.00</dd>
        <dt>Per person per day</dt><dd id="t-ppd">৳0.00</dd>
      </dl>
      <div class="shares" id="shares" hidden>
        <div class="k">Equal share for each member</div>
        <dl id="share-list"></dl>
        <p class="note" id="share-note"></p>
      </div>
    </div>
    <section class="card" style="margin-top:16px">
      <div class="card-h"><h2>History</h2><p>Saved trips. Open one to edit it.</p></div>
      <div id="hist"><p class="empty">No saved trips yet. Fill in the form and tap Save trip.</p></div>
    </section>
  </aside>
</div>

<div class="mbar">
  <div><small>Total</small><b id="m-total">৳0.00</b></div>
  <div><small>Per person</small><b id="m-pp">৳0.00</b></div>
  <a href="#summary">Details</a>
</div>
<div id="status" role="status" aria-live="polite"></div>

<script>
const $ = s => document.querySelector(s);
const CATS = ['Transport','Fuel','Accommodation','Food','Tickets & tolls','Other'];
const KEY_H = 'tec:history', KEY_P = 'tec:pending';
const fmt = n => '৳' + Number(n || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const newUid = () => (crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2, 10));
const store = {
  get(k, d) { try { return JSON.parse(localStorage.getItem(k)) ?? d; } catch { return d; } },
  set(k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch {} }
};
let uid = newUid(), tt;
const status = m => { const s = $('#status'); s.textContent = m; s.classList.toggle('on', !!m); clearTimeout(tt); if (m) tt = setTimeout(() => s.classList.remove('on'), 5000); };

/* ---------- form ---------- */
function addRow(it = {}) {
  const d = document.createElement('div');
  d.className = 'row';
  d.innerHTML =
    `<div class="c-cat"><span class="lbl">Category</span><select aria-label="Category">${CATS.map(c => `<option${c === (it.category || 'Transport') ? ' selected' : ''}>${esc(c)}</option>`).join('')}</select></div>
     <div class="c-desc"><span class="lbl">Description</span><input class="desc" aria-label="Description" placeholder="e.g. Bus tickets" value="${esc(it.description)}"></div>
     <div class="c-qty"><span class="lbl">Quantity</span><input type="number" inputmode="decimal" min="0" step="any" aria-label="Quantity" value="${it.qty ?? 1}"></div>
     <div class="c-unit"><span class="lbl">Unit cost</span><input type="number" inputmode="decimal" min="0" step="any" aria-label="Unit cost" placeholder="0" value="${it.unit_cost ?? ''}"></div>
     <div class="c-amt"><span class="lbl">Amount</span><div class="amt">৳0.00</div></div>
     <button class="x" aria-label="Remove expense" type="button">×</button>`;
  $('#rows').appendChild(d);
}
function readTrip() {
  const members = $('#members').value.split('\n').map(s => s.trim()).filter(Boolean).slice(0, 50);
  const items = [...document.querySelectorAll('#rows .row')].map(r => {
    const [cat, desc, qty, unit] = r.querySelectorAll('select,input');
    return {category: cat.value, description: desc.value.trim(), qty: +qty.value || 0, unit_cost: +unit.value || 0};
  }).filter(i => i.qty * i.unit_cost > 0 || i.description);
  return {client_uid: uid, name: $('#name').value.trim(), route_from: $('#from').value.trim(), route_to: $('#to').value.trim(),
          travelers: members.length || Math.max(1, +$('#trav').value || 1), days: Math.max(1, +$('#days').value || 1), members, items};
}
const totalOf = t => t.items.reduce((s, i) => s + Math.round(i.qty * i.unit_cost * 100) / 100, 0);
// Equal split in paisa so the shares always add up to the exact total.
function split(total, n) {
  const c = Math.round(total * 100), b = Math.floor(c / n), r = c - b * n;
  return Array.from({length: n}, (_, i) => (b + (i < r ? 1 : 0)) / 100);
}
function calc() {
  const t = readTrip(), total = totalOf(t);
  document.querySelectorAll('#rows .row').forEach(r => {
    const [, , q, u] = r.querySelectorAll('select,input');
    r.querySelector('.amt').textContent = fmt((+q.value || 0) * (+u.value || 0));
  });
  $('#t-total').textContent = $('#m-total').textContent = fmt(total);
  $('#t-pp').textContent = $('#m-pp').textContent = fmt(total / t.travelers);
  $('#t-pd').textContent = fmt(total / t.days);
  $('#t-ppd').textContent = fmt(total / t.travelers / t.days);
  const n = t.members.length;
  $('#trav').disabled = n > 0;
  if (n) $('#trav').value = n;
  $('#shares').hidden = !n;
  if (n) {
    const parts = split(total, n);
    $('#share-list').innerHTML = t.members.map((m, i) => `<dt>${esc(m)}</dt><dd>${fmt(parts[i])}</dd>`).join('');
    $('#share-note').textContent = new Set(parts).size > 1 ? 'Leftover paisa goes to the first names so the shares add up to the total.' : '';
  }
}
function fill(t) {
  uid = t.client_uid;
  $('#name').value = t.name; $('#from').value = t.route_from || ''; $('#to').value = t.route_to || '';
  $('#trav').value = t.travelers; $('#days').value = t.days; $('#members').value = (t.members || []).join('\n');
  $('#rows').innerHTML = '';
  (t.items.length ? t.items : [{}]).forEach(addRow);
  calc();
}

/* ---------- history cache ---------- */
// cache = {version, trips}; shown instantly, then revalidated against the server version hash.
function renderHistory() {
  const cache = store.get(KEY_H, {trips: []}), pending = store.get(KEY_P, []);
  const pendUids = new Set(pending.map(p => p.client_uid));
  const list = [...pending.map(p => ({...p, total: totalOf(p), _pending: true})),
                ...cache.trips.filter(t => !pendUids.has(t.client_uid))];
  const box = $('#hist');
  if (!list.length) { box.innerHTML = '<p class="empty">No saved trips yet. Fill in the form and tap Save trip.</p>'; return; }
  box.innerHTML = list.map(t => `
    <div class="hist">
      <div style="min-width:0"><b>${esc(t.name)}${t._pending ? '<span class="tag">not synced</span>' : ''}</b>
        <small>${esc([t.route_from, t.route_to].filter(Boolean).join(' → ') || 'No route')}</small></div>
      <div class="tot">${fmt(t.total)}</div>
      <small>${t.travelers} traveler${t.travelers > 1 ? 's' : ''} · ${t.days} day${t.days > 1 ? 's' : ''}</small>
      <small style="text-align:right">${fmt(t.total / t.travelers)} each</small>
      <div class="acts"><button class="btn sm" data-open="${esc(t.client_uid)}">Open</button><button class="btn sm danger" data-del="${esc(t.client_uid)}">Delete</button></div>
    </div>`).join('');
}
async function refreshHistory() {
  renderHistory();                                   // 1. instant, from cache
  const cache = store.get(KEY_H, {});
  try {                                              // 2. revalidate
    const r = await fetch('api.php?a=list&v=' + encodeURIComponent(cache.version || ''));
    const d = await r.json();
    if (d.trips) { store.set(KEY_H, {version: d.version, trips: d.trips}); renderHistory(); }
  } catch { status('Offline: showing saved history.'); }
}
const api = (a, body) => fetch('api.php?a=' + a, {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(body)})
  .then(async r => { const d = await r.json(); if (!r.ok) throw Object.assign(new Error(d.error || 'Error'), {server: true}); return d; });

async function syncPending() {
  let p = store.get(KEY_P, []);
  for (const t of [...p]) {
    try { await api('save', t); p = p.filter(x => x.client_uid !== t.client_uid); store.set(KEY_P, p); } catch (e) { if (!e.server) break; }
  }
}

/* ---------- actions ---------- */
$('#save').onclick = async () => {
  const t = readTrip();
  if (!t.name) { status('Enter a trip name first.'); $('#name').focus(); return; }
  if (!t.items.length) { status('Add at least one expense.'); return; }
  try { await api('save', t); status('Trip saved.'); }
  catch (e) {
    if (e.server) { status(e.message); return; }
    const p = store.get(KEY_P, []).filter(x => x.client_uid !== t.client_uid); p.push(t); store.set(KEY_P, p);
    status('You are offline. Saved on this device and will sync when you are back online.');
  }
  await syncPending(); refreshHistory();
};
$('#new').onclick = () => { uid = newUid(); $('#name').value = $('#from').value = $('#to').value = ''; $('#trav').value = $('#days').value = 1; $('#members').value = ''; $('#rows').innerHTML = ''; addRow(); calc(); status(''); scrollTo({top: 0, behavior: 'smooth'}); };
$('#add').onclick = () => { addRow(); calc(); const r = $('#rows').lastElementChild; r.querySelector('.desc').focus(); };
$('#f-add').onclick = () => {
  const dist = +$('#f-dist').value, mpl = +$('#f-mpl').value, price = +$('#f-price').value, k = +$('#f-rt').value;
  if (!(dist > 0 && mpl > 0 && price > 0)) { status('Enter distance, mileage and fuel price.'); return; }
  const litres = Math.round(dist * k / mpl * 100) / 100;
  addRow({category: 'Fuel', description: `Fuel for ${dist * k} km`, qty: litres, unit_cost: price}); calc(); status('Fuel cost added to expenses.');
};
$('#rows').addEventListener('click', e => { if (e.target.classList.contains('x')) { e.target.closest('.row').remove(); calc(); } });
document.addEventListener('input', calc);
$('#hist').addEventListener('click', async e => {
  const b = e.target.closest('button'); if (!b) return;
  const o = b.dataset.open, d = b.dataset.del;
  if (o) {
    const t = [...store.get(KEY_P, []), ...(store.get(KEY_H, {}).trips || [])].find(x => x.client_uid === o);
    if (t) { fill({...t, items: t.items.map(i => ({...i, qty: +i.qty, unit_cost: +i.unit_cost}))}); status('Trip loaded. Saving will update it.'); scrollTo({top: 0, behavior: 'smooth'}); }
  }
  if (d && confirm('Delete this trip?')) {
    store.set(KEY_P, store.get(KEY_P, []).filter(x => x.client_uid !== d));
    try { await api('delete', {client_uid: d}); } catch { status('Could not reach the server. Try deleting again when online.'); }
    refreshHistory();
  }
});
addEventListener('online', async () => { await syncPending(); refreshHistory(); });

addRow(); calc(); syncPending().then(refreshHistory);
</script>
</body>
</html>
