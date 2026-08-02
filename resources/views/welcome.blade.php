<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Ledgerbook · Home</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          paper: '#EEEEE4',
          paperCard: '#FBFAF4',
          ink: '#1E2B22',
          inkSoft: '#4A5A4E',
          ledger: '#2F6F4E',
          ledgerDeep: '#1F4E36',
          gold: '#AD8A32',
          brick: '#A23E30',
        },
        fontFamily: {
          display: ['"Fraunces"', 'serif'],
          body: ['"Inter"', 'sans-serif'],
          mono: ['"IBM Plex Mono"', 'monospace'],
        },
      },
    },
  }
</script>

<style>
  body { background-color: #EEEEE4; }

  /* faint columnar ruling on the page background, like accounting paper */
  .ruled-bg {
    background-image:
      linear-gradient(to bottom, rgba(47,111,78,0.07) 1px, transparent 1px);
    background-size: 100% 2.35rem;
  }

  /* stitched spine along the left of the main ledger card */
  .spine {
    position: relative;
  }
  .spine::before {
    content: "";
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 14px;
    background: repeating-linear-gradient(
      to bottom,
      #1F4E36 0px, #1F4E36 8px,
      transparent 8px, transparent 16px
    );
    opacity: 0.85;
  }
  .spine::after {
    content: "";
    position: absolute;
    left: 14px; top: 0; bottom: 0;
    width: 1px;
    background: rgba(30,43,34,0.12);
  }

  /* rubber-stamp seal for the net balance */
  .stamp {
    border: 2.5px solid currentColor;
    border-radius: 9999px;
    transform: rotate(-6deg);
    letter-spacing: 0.14em;
  }

  .figure { font-variant-numeric: tabular-nums; }

  .row-line td {
    border-bottom: 1px solid rgba(30,43,34,0.09);
  }
  .row-line:nth-child(even) {
    background: rgba(47,111,78,0.035);
  }

  .eyebrow {
    font-family: 'IBM Plex Mono', monospace;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    font-size: 0.7rem;
  }

  ::selection { background: #AD8A32; color: #FBFAF4; }
</style>
</head>

<body class="font-body text-ink antialiased ruled-bg">

  <!-- ====== HEADER / NAV ====== -->
  <header class="max-w-6xl mx-auto px-6 lg:px-8 py-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-sm bg-ledgerDeep flex items-center justify-center text-paperCard font-display font-semibold text-lg">L</div>
      <div>
        <div class="font-display font-semibold text-lg leading-none">Ledgerbook</div>
        <div class="eyebrow text-inkSoft leading-none mt-1">Double-entry, made plain</div>
      </div>
    </div>
    @if (Route::has('login'))
        <nav class="flex items-center gap-3">
            @auth
                <a
                    href="{{ url('/dashboard') }}"
                    class="px-4 py-2 text-sm font-medium rounded-sm bg-ledgerDeep text-paperCard hover:bg-ledger transition"
                >
                    Dashboard
                </a>
            @else
                <a
                    href="{{ route('login') }}"
                    class="px-4 py-2 text-sm font-medium text-inkSoft hover:text-ink transition"
                >
                    Log in
                </a>

                @if (Route::has('register'))
                    <a
                        href="{{ route('register') }}"
                        class="px-4 py-2 text-sm font-medium rounded-sm bg-ledgerDeep text-paperCard hover:bg-ledger transition"
                    >
                        Open a ledger
                    </a>
                @endif
            @endauth
        </nav>
    @endif
  </header>

  <!-- ====== HERO ====== -->
  <section class="max-w-6xl mx-auto px-6 lg:px-8 pt-10 pb-14">
    <div class="grid lg:grid-cols-5 gap-10 items-end">
      <div class="lg:col-span-3">
        <p class="eyebrow text-gold mb-4">Folio No. 0042 &middot; Aug 2026</p>
        <h1 class="font-display font-semibold text-[2.75rem] sm:text-6xl leading-[1.05] tracking-tight">
          Every debit finds<br class="hidden sm:block" />
          its credit<span class="text-ledger">.</span>
        </h1>
        <p class="mt-5 text-base sm:text-lg text-inkSoft max-w-md leading-relaxed">
          A running record of what came in, what went out, and what's left &mdash;
          kept the way a good bookkeeper would keep it. No spreadsheets to fight, no balance to chase.
        </p>
        <div class="mt-8 flex flex-wrap items-center gap-4">
          <a href="#new-entry" class="px-6 py-3 bg-brick text-paperCard rounded-sm font-medium text-sm hover:bg-brick/90 transition shadow-sm">
            Post an entry
          </a>
          <a href="#ledger" class="px-6 py-3 border border-ink/20 rounded-sm font-medium text-sm hover:bg-ink/5 transition">
            View the book &rarr;
          </a>
        </div>
      </div>

      <!-- net balance seal -->
      <div class="lg:col-span-2 flex justify-center lg:justify-end">
        <div class="stamp text-ledgerDeep w-44 h-44 flex flex-col items-center justify-center text-center shrink-0">
          <span class="eyebrow">Net position</span>
          <span class="font-display font-semibold text-3xl mt-1 figure">$4,220</span>
          <span class="eyebrow mt-1">In balance</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ====== MAIN LEDGER CARD ====== -->
  <section class="max-w-6xl mx-auto px-6 lg:px-8 pb-20">
    <div class="spine bg-paperCard rounded-md border border-ink/10 shadow-[0_1px_2px_rgba(30,43,34,0.06)] pl-8 pr-6 sm:pl-10 sm:pr-8 py-8">

      <!-- Stats row -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-ink/8 rounded-sm overflow-hidden mb-9 border border-ink/8">
        <div class="bg-paperCard px-5 py-4">
          <div class="eyebrow text-inkSoft">Total debit</div>
          <div class="font-mono text-2xl font-medium text-brick mt-1 figure">$12,450.00</div>
        </div>
        <div class="bg-paperCard px-5 py-4">
          <div class="eyebrow text-inkSoft">Total credit</div>
          <div class="font-mono text-2xl font-medium text-ledger mt-1 figure">$8,230.00</div>
        </div>
        <div class="bg-paperCard px-5 py-4">
          <div class="eyebrow text-inkSoft">Net balance</div>
          <div class="font-mono text-2xl font-medium text-ledgerDeep mt-1 figure">$4,220.00</div>
        </div>
        <div class="bg-paperCard px-5 py-4">
          <div class="eyebrow text-inkSoft">Entries</div>
          <div class="font-mono text-2xl font-medium text-gold mt-1 figure">24</div>
        </div>
      </div>

      <!-- Quick Add Form -->
      <div id="new-entry" class="mb-10">
        <h2 class="font-display font-semibold text-lg mb-4 flex items-center gap-2">
          <span class="text-gold">＋</span> New entry
        </h2>
        <form class="grid sm:grid-cols-12 gap-3 items-end">
          <div class="sm:col-span-2">
            <label class="eyebrow text-inkSoft block mb-1.5">Date</label>
            <input type="date" value="2026-08-02"
              class="w-full rounded-sm border border-ink/15 bg-white px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-ledger/40 focus:border-ledger outline-none" />
          </div>
          <div class="sm:col-span-4">
            <label class="eyebrow text-inkSoft block mb-1.5">Description</label>
            <input type="text" placeholder="Invoice #1234"
              class="w-full rounded-sm border border-ink/15 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-ledger/40 focus:border-ledger outline-none" />
          </div>
          <div class="sm:col-span-2">
            <label class="eyebrow text-inkSoft block mb-1.5">Debit</label>
            <input type="number" step="0.01" placeholder="0.00"
              class="w-full rounded-sm border border-ink/15 bg-white px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-brick/40 focus:border-brick outline-none" />
          </div>
          <div class="sm:col-span-2">
            <label class="eyebrow text-inkSoft block mb-1.5">Credit</label>
            <input type="number" step="0.01" placeholder="0.00"
              class="w-full rounded-sm border border-ink/15 bg-white px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-ledger/40 focus:border-ledger outline-none" />
          </div>
          <div class="sm:col-span-2">
            <button type="submit"
              class="w-full px-5 py-2 bg-ledgerDeep text-paperCard rounded-sm text-sm font-medium hover:bg-ledger transition">
              Post
            </button>
          </div>
        </form>
      </div>

      <!-- Ledger Table -->
      <div id="ledger">
        <h2 class="font-display font-semibold text-lg mb-4">The book</h2>
        <div class="overflow-x-auto rounded-sm border border-ink/10">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-ledgerDeep text-paperCard/90">
                <th class="text-left font-medium eyebrow !tracking-wider py-3 px-4">Date</th>
                <th class="text-left font-medium eyebrow !tracking-wider py-3 px-4">Description</th>
                <th class="text-right font-medium eyebrow !tracking-wider py-3 px-4">Debit</th>
                <th class="text-right font-medium eyebrow !tracking-wider py-3 px-4">Credit</th>
                <th class="text-right font-medium eyebrow !tracking-wider py-3 px-4">Balance</th>
                <th class="text-center font-medium eyebrow !tracking-wider py-3 px-4">Type</th>
              </tr>
            </thead>
            <tbody>
              <tr class="row-line">
                <td class="py-3 px-4 font-mono text-inkSoft">2026-08-02</td>
                <td class="py-3 px-4">Client payment &ndash; Acme Corp</td>
                <td class="py-3 px-4 text-right font-mono text-ink/30">&mdash;</td>
                <td class="py-3 px-4 text-right font-mono text-ledger figure">$1,200.00</td>
                <td class="py-3 px-4 text-right font-mono font-medium figure">$4,220.00</td>
                <td class="py-3 px-4 text-center">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-ledger/10 text-ledgerDeep">Credit</span>
                </td>
              </tr>
              <tr class="row-line">
                <td class="py-3 px-4 font-mono text-inkSoft">2026-08-01</td>
                <td class="py-3 px-4">Office supplies &ndash; Staples</td>
                <td class="py-3 px-4 text-right font-mono text-brick figure">$340.50</td>
                <td class="py-3 px-4 text-right font-mono text-ink/30">&mdash;</td>
                <td class="py-3 px-4 text-right font-mono font-medium figure">$3,020.00</td>
                <td class="py-3 px-4 text-center">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brick/10 text-brick">Debit</span>
                </td>
              </tr>
              <tr class="row-line">
                <td class="py-3 px-4 font-mono text-inkSoft">2026-07-31</td>
                <td class="py-3 px-4">Consulting fee &ndash; MegaCorp</td>
                <td class="py-3 px-4 text-right font-mono text-ink/30">&mdash;</td>
                <td class="py-3 px-4 text-right font-mono text-ledger figure">$2,500.00</td>
                <td class="py-3 px-4 text-right font-mono font-medium figure">$3,360.50</td>
                <td class="py-3 px-4 text-center">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-ledger/10 text-ledgerDeep">Credit</span>
                </td>
              </tr>
              <tr class="row-line">
                <td class="py-3 px-4 font-mono text-inkSoft">2026-07-30</td>
                <td class="py-3 px-4">Server hosting &ndash; AWS</td>
                <td class="py-3 px-4 text-right font-mono text-brick figure">$890.00</td>
                <td class="py-3 px-4 text-right font-mono text-ink/30">&mdash;</td>
                <td class="py-3 px-4 text-right font-mono font-medium figure">$860.50</td>
                <td class="py-3 px-4 text-center">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brick/10 text-brick">Debit</span>
                </td>
              </tr>
              <tr class="row-line">
                <td class="py-3 px-4 font-mono text-inkSoft">2026-07-29</td>
                <td class="py-3 px-4">Freelance project &ndash; DesignStudio</td>
                <td class="py-3 px-4 text-right font-mono text-ink/30">&mdash;</td>
                <td class="py-3 px-4 text-right font-mono text-ledger figure">$1,750.50</td>
                <td class="py-3 px-4 text-right font-mono font-medium figure">$1,750.50</td>
                <td class="py-3 px-4 text-center">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-ledger/10 text-ledgerDeep">Credit</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </section>

  <!-- ====== FEATURES ====== -->
  <section class="max-w-6xl mx-auto px-6 lg:px-8 pb-20 grid sm:grid-cols-3 gap-6">
    <div class="border-t-2 border-ledgerDeep pt-4">
      <div class="eyebrow text-gold mb-2">Balance</div>
      <h3 class="font-display font-semibold text-lg mb-1">Real-time position</h3>
      <p class="text-sm text-inkSoft leading-relaxed">Every entry recalculates the running balance the moment it's posted.</p>
    </div>
    <div class="border-t-2 border-ledgerDeep pt-4">
      <div class="eyebrow text-gold mb-2">History</div>
      <h3 class="font-display font-semibold text-lg mb-1">A full paper trail</h3>
      <p class="text-sm text-inkSoft leading-relaxed">Nothing is overwritten. Every date, description, and amount stays on the record.</p>
    </div>
    <div class="border-t-2 border-ledgerDeep pt-4">
      <div class="eyebrow text-gold mb-2">Insight</div>
      <h3 class="font-display font-semibold text-lg mb-1">Where the money moved</h3>
      <p class="text-sm text-inkSoft leading-relaxed">Spot the pattern in your debits and credits before it becomes a problem.</p>
    </div>
  </section>

  <!-- ====== FOOTER ====== -->
  <footer class="max-w-6xl mx-auto px-6 lg:px-8 py-8 border-t border-ink/10 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-inkSoft">
    <span>&copy; 2026 Ledgerbook</span>
    <span class="font-mono">Kept honestly, balanced daily.</span>
  </footer>

</body>
</html>