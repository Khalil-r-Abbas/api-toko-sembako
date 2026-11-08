<?php
/** @var array $products */
helper('url');

$names = array_values(array_unique(array_column($products, 'name')));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <title>Bikin Makanan</title>
    <style>
        :root {
            --bg: #fff; --surface: #f2f2f4; --text: #1d1d1f; --text-2: #6e6e73;
            --line: rgba(0,0,0,.08); --accent: #0071e3; --warn: #b25000; --warn-bg: rgba(255,149,0,.14);
            --radius: 1.5rem;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #000; --surface: #1c1c1e; --text: #f5f5f7; --text-2: #98989d;
                --line: rgba(255,255,255,.12); --accent: #2997ff; --warn: #ffb340; --warn-bg: rgba(255,159,10,.18);
            }
        }
        *, *::before, *::after { box-sizing: border-box; }
        [hidden] { display: none !important; }
        body {
            margin: 0; background: var(--bg); color: var(--text);
            font: 100%/1.5 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        button { font: inherit; color: inherit; cursor: pointer; }
        :focus-visible { outline: .125rem solid var(--accent); outline-offset: .125rem; }

        /* Layout: sidebar gelap kiri, resep kanan */
        .layout {
            display: grid; grid-template-columns: clamp(18rem, 30vw, 25rem) 1fr; gap: 1.5rem;
            max-width: 96rem; margin: 0 auto; align-items: start;
            padding: max(1rem, env(safe-area-inset-top)) max(1rem, env(safe-area-inset-right))
                     max(1rem, env(safe-area-inset-bottom)) max(1rem, env(safe-area-inset-left));
        }

        .side {
            position: sticky; top: 1rem; height: calc(100dvh - 2rem);
            display: flex; flex-direction: column; gap: 1rem; padding: 1.25rem; overflow: hidden;
            background: #1e1e1e; color: #f5f5f7; border-radius: var(--radius);
            --on-line: rgba(255,255,255,.18); --on-fill: rgba(255,255,255,.12); --on-text-2: #98989d; --on-accent: #2997ff;
        }
        .back { color: var(--on-accent); font-size: .875rem; text-decoration: none; }
        .search {
            width: 100%; padding: .7rem 1rem; font: inherit; color: inherit; outline: none;
            background: var(--on-fill); border: 1px solid transparent; border-radius: .75rem;
            -webkit-appearance: none; appearance: none;
        }
        .search::placeholder { color: var(--on-text-2); }
        .search:focus-visible { border-color: var(--on-accent); }
        .side-scroll { flex: 1; min-height: 0; overflow-y: auto; overscroll-behavior: contain; margin-right: -.5rem; padding-right: .5rem; }

        .sec { display: flex; justify-content: space-between; align-items: baseline; margin: 0 0 .75rem; padding-bottom: .5rem; border-bottom: 1px solid var(--on-line); font-size: .8125rem; font-weight: 600; letter-spacing: .02em; }
        .sec button { background: none; border: 0; padding: 0; color: var(--on-accent); font-weight: 400; }
        .tags { display: flex; flex-wrap: wrap; gap: .5rem; margin: 0 0 1.5rem; padding: 0; list-style: none; }
        .hint { color: var(--on-text-2); font-size: .875rem; }

        .tag, .pill { border: 0; border-radius: .5rem; padding: .4rem .75rem; font-size: .875rem; transition: transform 100ms ease-out; }
        .tag { display: inline-flex; gap: .5rem; align-items: center; background: var(--on-accent); color: #000; font-weight: 600; }
        .pill { border-radius: 999px; background: var(--on-fill); }
        .tag:active, .pill:active { transform: scale(.96); }
        .side :focus-visible { outline-color: var(--on-accent); }

        /* Resep */
        .results { min-width: 0; }
        .sub { margin: .25rem 0 1rem; color: var(--text-2); font-size: .9375rem; font-variant-numeric: tabular-nums; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(16rem, 1fr)); gap: 1.25rem; margin: 0; padding: 0; list-style: none; }
        .card { display: flex; flex-direction: column; gap: .75rem; min-height: 17rem; padding: 1.5rem; background: var(--surface); border-radius: var(--radius); }
        .card h3 { margin: 0; font-size: 1.25rem; line-height: 1.2; letter-spacing: -.015em; }
        .meta { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; color: var(--text-2); font-size: .8125rem; }
        .badge { padding: .125rem .5rem; border-radius: 999px; background: var(--warn-bg); color: var(--warn); font-weight: 600; }
        .ing { margin: 0; padding: 0; list-style: none; font-size: .9375rem; }
        .ing li { display: flex; justify-content: space-between; gap: 1rem; padding: .3rem 0; border-top: 1px solid var(--line); color: var(--text-2); }
        .ing li:first-child { border-top: 0; }
        .ing li.on { color: var(--text); }
        .ing li.on span:first-child::before { content: "✓"; margin-right: .5rem; color: var(--accent); font-weight: 700; }
        .ing .qty { font-variant-numeric: tabular-nums; white-space: nowrap; }
        .more { margin-top: auto; align-self: flex-start; background: none; border: 0; padding: .25rem 0; color: var(--accent); font-weight: 600; }
        .empty { padding: 5rem 1rem; text-align: center; color: var(--text-2); }
        .empty strong { display: block; margin-bottom: .25rem; color: var(--text); font-size: 1.25rem; }

        /* Modal detail */
        dialog {
            width: min(36rem, calc(100% - 2rem)); max-height: min(85vh, 44rem); padding: 0; overflow: hidden;
            color: var(--text); background: var(--bg); border: 1px solid var(--line); border-radius: var(--radius);
            opacity: 0; transform: scale(.96);
            transition: opacity 200ms ease, transform 200ms ease, overlay 200ms allow-discrete, display 200ms allow-discrete;
        }
        dialog[open] { opacity: 1; transform: none; display: flex; flex-direction: column; }
        @starting-style { dialog[open] { opacity: 0; transform: scale(.96); } }
        dialog::backdrop { background: rgba(0,0,0,.4); -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); }
        body:has(dialog[open]) { overflow: hidden; }
        .dh { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; padding: 1.25rem 1.25rem .75rem; }
        .dh h3 { margin: 0; font-size: 1.5rem; line-height: 1.1; letter-spacing: -.02em; }
        .x { flex: none; width: 2rem; height: 2rem; border: 0; border-radius: 50%; background: var(--surface); color: var(--text-2); line-height: 1; }
        .db { overflow-y: auto; padding: 0 1.25rem 1.5rem; }
        .db h4 { margin: 1.25rem 0 .5rem; font-size: 1rem; }
        .db ol { margin: 0; padding-left: 1.25rem; }
        .db ol li { padding: .25rem 0; }

        /* Layar sempit: sidebar di atas, resep di bawah */
        @media (max-width: 52rem) {
            .layout { grid-template-columns: 1fr; }
            .side { position: static; height: auto; max-height: 32rem; }
            dialog { margin: auto auto 0; width: 100%; max-height: 90vh; border-radius: var(--radius) var(--radius) 0 0; }
        }
        @media (prefers-reduced-motion: reduce) {
            dialog { transform: none !important; }
            .tag, .pill { transition: none; }
            .tag:active, .pill:active { transform: none; }
        }
        @media (prefers-reduced-transparency: reduce) {
            dialog::backdrop { -webkit-backdrop-filter: none; backdrop-filter: none; background: rgba(0,0,0,.6); }
        }
    </style>
</head>
<body>

<div class="layout">
    <aside class="side" aria-label="Pilih produk">
        <a class="back" href="<?= esc(base_url('produk'), 'attr') ?>">‹ Produk</a>
        <input class="search" id="q" type="search" placeholder="Cari produk" aria-label="Cari produk" autocomplete="off">

        <div class="side-scroll">
            <h2 class="sec"><span>Terpilih · <span id="sel-count">0</span></span>
                <button id="reset" type="button" hidden>Hapus semua</button></h2>
            <ul class="tags" id="selected"></ul>

            <h2 class="sec"><span>Produk</span></h2>
            <ul class="tags" id="products"></ul>
        </div>
    </aside>

    <main class="results">
        <p class="sub" id="res-count" aria-live="polite" hidden></p>
        <div id="results"></div>
    </main>
</div>

<dialog id="modal" aria-labelledby="m-title">
    <div class="dh">
        <div>
            <h3 id="m-title"></h3>
            <div class="meta" id="m-meta"></div>
        </div>
        <button class="x" type="button" id="m-close" aria-label="Tutup">✕</button>
    </div>
    <div class="db" id="m-body"></div>
</dialog>

<script>
(() => {
    const API = <?= json_encode(base_url('bikin-makan/cari')) ?>;
    const NAMES = <?= json_encode($names, JSON_UNESCAPED_UNICODE) ?>;
    const LIMIT = 4; // bahan yang tampil di kartu sebelum muncul "Lihat detail"

    const $ = id => document.getElementById(id);
    const q = $('q'), selEl = $('selected'), prodEl = $('products'), selCount = $('sel-count'),
          resetBtn = $('reset'), results = $('results'), resCount = $('res-count'), modal = $('modal');

    const sel = new Set();
    let recipes = [], seq = 0;

    const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const hint = (t, d) => `<div class="empty"><strong>${esc(t)}</strong>${esc(d)}</div>`;

    function renderLists() {
        selCount.textContent = sel.size;
        resetBtn.hidden = !sel.size;
        selEl.innerHTML = sel.size
            ? [...sel].map(n => `<li><button class="tag" type="button" data-n="${esc(n)}" aria-label="Hapus ${esc(n)}"><span>${esc(n)}</span><span aria-hidden="true">✕</span></button></li>`).join('')
            : '<li class="hint">Belum ada produk dipilih</li>';

        const t = q.value.trim().toLowerCase();
        const list = NAMES.filter(n => !sel.has(n) && (!t || n.toLowerCase().includes(t)));
        prodEl.innerHTML = list.length
            ? list.map(n => `<li><button class="pill" type="button" data-n="${esc(n)}">${esc(n)}</button></li>`).join('')
            : `<li class="hint">${NAMES.length ? 'Tidak ada produk yang cocok' : 'Belum ada produk'}</li>`;
    }

    // Setelah item pindah list, fokus tetap di posisi yang sama (aman untuk keyboard).
    function toggle(btn, container, sel_) {
        const idx = [...container.querySelectorAll('button')].indexOf(btn);
        const n = btn.dataset.n;
        sel.has(n) ? sel.delete(n) : sel.add(n);
        renderLists();
        const next = container.querySelectorAll('button')[idx] || q;
        next.focus({ preventScroll: true });
        update();
    }

    selEl.addEventListener('click', e => { const b = e.target.closest('.tag'); if (b) toggle(b, selEl); });
    prodEl.addEventListener('click', e => { const b = e.target.closest('.pill'); if (b) toggle(b, prodEl); });
    resetBtn.addEventListener('click', () => { sel.clear(); renderLists(); q.focus(); update(); });
    q.addEventListener('input', renderLists);

    function update() {
        if (!sel.size) {
            seq++; recipes = []; resCount.hidden = true;
            results.innerHTML = hint('Pilih produk', 'Resep yang memuat semua produk pilihanmu akan muncul di sini.');
            return;
        }
        load();
    }

    // Hasil lama tetap tampil sampai hasil baru tiba; respons usang diabaikan.
    async function load() {
        const id = ++seq;
        const qs = new URLSearchParams();
        sel.forEach(n => qs.append('names[]', n));
        try {
            const r = await fetch(API + '?' + qs);
            if (!r.ok) throw new Error(r.status);
            const d = await r.json();
            if (id !== seq) return;
            recipes = d.data;
            render();
        } catch {
            if (id === seq) results.innerHTML = hint('Gagal memuat resep', 'Periksa koneksi lalu coba lagi.');
        }
    }

    const ingLi = i => `<li class="${i.selected ? 'on' : ''}"><span>${esc(i.name)}</span><span class="qty">${esc(i.qty || '')}</span></li>`;
    const badge = r => r.complete ? '' : '<span class="badge">Kurang bahan</span>';

    function render() {
        resCount.hidden = !recipes.length;
        resCount.textContent = `${recipes.length} resep`;
        if (!recipes.length) {
            results.innerHTML = hint('Belum ada resep yang cocok', 'Coba kurangi produk yang dipilih.');
            return;
        }
        results.innerHTML = '<ul class="grid">' + recipes.map(r => `<li class="card">
            <h3>${esc(r.name)}</h3>
            <div class="meta"><span>${esc(r.minutes)} menit</span>${badge(r)}</div>
            <ul class="ing">${r.ingredients.slice(0, LIMIT).map(ingLi).join('')}</ul>
            ${r.ingredients.length > LIMIT ? `<button class="more" type="button" data-id="${esc(r.id)}">Lihat detail · ${r.ingredients.length - LIMIT} bahan lagi</button>` : ''}
        </li>`).join('') + '</ul>';
    }

    // Modal detail
    results.addEventListener('click', e => {
        const b = e.target.closest('.more');
        const r = b && recipes.find(x => String(x.id) === b.dataset.id);
        if (!r) return;
        $('m-title').textContent = r.name;
        $('m-meta').innerHTML = `<span>${esc(r.minutes)} menit</span>${badge(r)}`;
        $('m-body').innerHTML =
            `<h4>Bahan</h4><ul class="ing">${r.ingredients.map(ingLi).join('')}</ul>` +
            (r.missing.length ? `<p class="sub">Belum dipilih: ${esc(r.missing.join(', '))}</p>` : '') +
            `<h4>Langkah</h4><ol>${r.steps.map(s => `<li>${esc(s)}</li>`).join('')}</ol>`;
        modal.showModal();
    });
    $('m-close').addEventListener('click', () => modal.close());
    modal.addEventListener('click', e => { if (e.target === modal) modal.close(); });

    renderLists();
    update();
})();
</script>
</body>
</html>
