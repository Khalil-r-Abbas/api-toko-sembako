<?php
/**
 * @var array $products
 */

helper('url');

// Rp 15.000 (desimal hanya muncul bila memang ada)
$rupiah = static function ($n): string {
    $n = (float) $n;
    return 'Rp ' . number_format($n, fmod($n, 1) === 0.0 ? 0 : 2, ',', '.');
};
$total = count($products);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <title>Produk</title>
    <style>
        :root {
            --bg: #f5f5f7;
            --surface: #ffffff;
            --text: #1d1d1f;
            --text-2: #6e6e73;
            --line: rgba(0, 0, 0, .08);
            --accent: #0071e3;
            --chrome: rgba(245, 245, 247, .72);
            --radius: 1.125rem;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #000000;
                --surface: #1c1c1e;
                --text: #f5f5f7;
                --text-2: #98989d;
                --line: rgba(255, 255, 255, .12);
                --accent: #2997ff;
                --chrome: rgba(0, 0, 0, .6);
            }
        }

        *, *::before, *::after { box-sizing: border-box; }
        [hidden] { display: none !important; }

        html { -webkit-text-size-adjust: 100%; }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font: 100%/1.5 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            font-optical-sizing: auto;
            -webkit-font-smoothing: antialiased;
        }

        /* Header: material translusen, konten menggulung di bawahnya */
        .bar {
            position: sticky;
            top: 0;
            z-index: 10;
            padding: max(1rem, env(safe-area-inset-top)) 1.25rem 1rem;
            background: var(--chrome);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid var(--line);
        }
        .bar-inner { max-width: 72rem; margin: 0 auto; }

        .top { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; }
        .btn {
            flex: none;
            margin-top: .375rem;
            padding: .5rem 1rem;
            border-radius: 999px;
            background: var(--accent);
            color: #fff;
            font-size: .9375rem;
            font-weight: 600;
            text-decoration: none;
            transition: transform 100ms ease-out;
        }
        .btn:active { transform: scale(.97); }
        .btn:focus-visible { outline: .125rem solid var(--accent); outline-offset: .125rem; }
        @media (prefers-reduced-motion: reduce) { .btn { transition: none; } .btn:active { transform: none; } }

        h1 {
            margin: 0;
            font-size: clamp(2rem, 5vw, 3rem);
            line-height: 1.05;
            letter-spacing: -0.02em;
            font-weight: 700;
        }
        .count {
            margin: .25rem 0 0;
            color: var(--text-2);
            font-size: .9375rem;
            font-variant-numeric: tabular-nums;
        }

        .search {
            display: block;
            width: 100%;
            margin-top: 1rem;
            padding: .75rem 1rem;
            font: inherit;
            color: var(--text);
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: .75rem;
            outline: none;
            -webkit-appearance: none;
            appearance: none;
        }
        .search::placeholder { color: var(--text-2); }
        .search:focus-visible {
            border-color: var(--accent);
            box-shadow: 0 0 0 .1875rem color-mix(in srgb, var(--accent) 30%, transparent);
        }

        main {
            max-width: 72rem;
            margin: 0 auto;
            padding: 1.5rem 1.25rem max(3rem, env(safe-area-inset-bottom));
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(14rem, 1fr));
            gap: 1rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .card {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            justify-content: space-between;
            min-height: 8.5rem;
            padding: 1.25rem;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius);
        }
        .name {
            margin: 0;
            font-size: 1.0625rem;
            line-height: 1.25;
            letter-spacing: -0.01em;
            font-weight: 600;
            overflow-wrap: anywhere;
        }
        .barcode {
            margin: .25rem 0 0;
            color: var(--text-2);
            font-size: .8125rem;
            letter-spacing: .01em;
            font-variant-numeric: tabular-nums;
        }
        .price {
            margin: 0;
            font-size: 1.375rem;
            line-height: 1.1;
            letter-spacing: -0.015em;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }

        .empty {
            padding: 4rem 1rem;
            text-align: center;
            color: var(--text-2);
        }
        .empty strong {
            display: block;
            margin-bottom: .25rem;
            color: var(--text);
            font-size: 1.25rem;
            letter-spacing: -0.01em;
        }

        @media (prefers-reduced-transparency: reduce) {
            .bar { background: var(--bg); -webkit-backdrop-filter: none; backdrop-filter: none; }
        }
        @media (prefers-contrast: more) {
            :root { --line: currentColor; --text-2: var(--text); }
        }
    </style>
</head>
<body>

<header class="bar">
    <div class="bar-inner">
        <div class="top">
            <div>
                <h1>Produk</h1>
                <p class="count" id="count" aria-live="polite"><?= $total ?> produk</p>
            </div>
            <a class="btn" href="<?= esc(base_url('bikin-makan'), 'attr') ?>">Bikin makanan</a>
        </div>
        <?php if ($total > 0): ?>
            <input class="search" id="q" type="search" placeholder="Cari nama atau barcode"
                   aria-label="Cari produk" autocomplete="off" enterkeyhint="search">
        <?php endif; ?>
    </div>
</header>

<main>
    <?php if ($total === 0): ?>
        <div class="empty">
            <strong>Belum ada produk</strong>
            Produk yang ditambahkan lewat API akan muncul di sini.
        </div>
    <?php else: ?>
        <ul class="grid" id="grid">
            <?php foreach ($products as $p): ?>
                <li class="card"
                    data-search="<?= esc(mb_strtolower(($p['name'] ?? '') . ' ' . ($p['barcode'] ?? '')), 'attr') ?>">
                    <div>
                        <h2 class="name"><?= esc($p['name']) ?></h2>
                        <?php if (! empty($p['barcode'])): ?>
                            <p class="barcode"><?= esc($p['barcode']) ?></p>
                        <?php endif; ?>
                    </div>
                    <p class="price"><?= esc($rupiah($p['price'] ?? 0)) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="empty" id="no-result" hidden>
            <strong>Tidak ditemukan</strong>
            <span id="no-result-q"></span>
        </div>
    <?php endif; ?>
</main>

<?php if ($total > 0): ?>
<script>
    // Filter langsung di sisi klien: tanpa debounce, tanpa request, hasil muncul saat mengetik.
    (() => {
        const input = document.getElementById('q');
        const cards = [...document.querySelectorAll('.card')];
        const count = document.getElementById('count');
        const none  = document.getElementById('no-result');
        const noneQ = document.getElementById('no-result-q');
        const total = cards.length;

        const apply = () => {
            const raw = input.value.trim();
            const q = raw.toLowerCase();
            let shown = 0;

            for (const el of cards) {
                const hit = !q || el.dataset.search.includes(q);
                el.hidden = !hit;
                if (hit) shown++;
            }

            count.textContent = q ? `${shown} dari ${total} produk` : `${total} produk`;
            none.hidden = shown !== 0;
            noneQ.textContent = shown === 0 ? `Tidak ada produk yang cocok dengan “${raw}”.` : '';
        };

        input.addEventListener('input', apply);
        apply(); // sinkron jika browser memulihkan isi kolom pencarian
    })();
</script>
<?php endif; ?>

</body>
</html>
