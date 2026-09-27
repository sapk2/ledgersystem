<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Ledgerbook · Home</title>
<meta name="description" content="A running record of what came in, what went out, and what's left — kept the way a good bookkeeper would keep it." />

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          paper: '#EEEEE4', paperCard: '#FBFAF4',
          ink: '#1E2B22', inkSoft: '#4A5A4E',
          ledger: '#2F6F4E', ledgerDeep: '#1F4E36',
          gold: '#AD8A32', brick: '#A23E30',
        },
        fontFamily: {
          display: ['"Fraunces"', 'serif'],
          body: ['"Inter"', 'sans-serif'],
          mono: ['"IBM Plex Mono"', 'monospace'],
        },
        boxShadow: {
          card: '0 1px 2px rgba(30,43,34,0.05), 0 12px 32px -18px rgba(30,43,34,0.35)',
          lift: '0 2px 4px rgba(30,43,34,0.06), 0 22px 40px -22px rgba(30,43,34,0.45)',
        },
      },
    },
  }
</script>

<style>
  /* =====================================================================
     MOTION SYSTEM — timing, easing, and orchestration tokens
     A senior approach: define the vocabulary once, reuse everywhere.
  ===================================================================== */
  :root {
    /* Easing — the signature of good motion design */
    --ease-out-expo:  cubic-bezier(0.16, 1, 0.3, 1);
    --ease-out-quart: cubic-bezier(0.25, 1, 0.5, 1);
    --ease-spring:    cubic-bezier(0.34, 1.56, 0.64, 1);
    --ease-in-out:    cubic-bezier(0.65, 0, 0.35, 1);
    --ease-anticipate:cubic-bezier(0.6, -0.28, 0.735, 0.045);

    /* Durations */
    --dur-xs: 180ms;
    --dur-sm: 320ms;
    --dur-md: 560ms;
    --dur-lg: 840ms;
    --dur-xl: 1200ms;

    /* Stagger step */
    --step: 70ms;
  }

  /* =====================================================================
     BASE
  ===================================================================== */
  html { -webkit-tap-highlight-color: transparent; }
  body { background-color: #EEEEE4; overflow-x: hidden; }
  [id] { scroll-margin-top: 6.5rem; }

  ::selection { background: #AD8A32; color: #FBFAF4; }

  :focus-visible {
    outline: 2px solid #2F6F4E;
    outline-offset: 3px;
    border-radius: 2px;
  }

  /* =====================================================================
     PAGE BACKGROUND — animated ruling that draws itself on load
  ===================================================================== */
  .ruled-bg {
    background-image:
      linear-gradient(to bottom, rgba(47,111,78,0.065) 1px, transparent 1px);
    background-size: 100% 2.35rem;
    background-position: 0 0;
    animation: ruling-fade var(--dur-xl) var(--ease-out-expo) both;
  }
  @keyframes ruling-fade {
    from { background-position: 0 -2.35rem; opacity: 0; }
    to   { background-position: 0 0;        opacity: 1; }
  }

  /* =====================================================================
     SPINE — stitched binding draws in from top to bottom on load
  ===================================================================== */
  .spine { position: relative; }
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
    border-radius: 6px 0 0 6px;
    transform-origin: top center;
    animation: stitch-draw var(--dur-xl) var(--ease-out-expo) 200ms both;
  }
  .spine::after {
    content: "";
    position: absolute;
    left: 14px; top: 0; bottom: 0;
    width: 1px;
    background: rgba(30,43,34,0.10);
    transform-origin: top center;
    animation: stitch-draw var(--dur-xl) var(--ease-out-expo) 320ms both;
  }
  @keyframes stitch-draw {
    from { transform: scaleY(0); opacity: 0; }
    to   { transform: scaleY(1); opacity: 1; }
  }

  /* =====================================================================
     PAPER GRAIN — subtle texture on the ledger card
  ===================================================================== */
  .grain::after {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    border-radius: inherit;
    opacity: .32;
    mix-blend-mode: multiply;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='140' height='140' filter='url(%23n)' opacity='0.42'/%3E%3C/svg%3E");
  }

  /* =====================================================================
     ENTRANCE CHOREOGRAPHY
     Each element has a delay set via --d, and the whole system composes.
  ===================================================================== */
  @keyframes rise {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  @keyframes rise-soft {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  @keyframes scale-in {
    from { opacity: 0; transform: scale(0.92); }
    to   { opacity: 1; transform: scale(1); }
  }

  .rise      { animation: rise      var(--dur-lg) var(--ease-out-expo) both; animation-delay: var(--d, 0ms); }
  .rise-soft { animation: rise-soft var(--dur-md) var(--ease-out-expo) both; animation-delay: var(--d, 0ms); }

  /* =====================================================================
     HERO HEADING — clip-path wipe from bottom, per-line
  ===================================================================== */
  .hero-line {
    display: block;
    overflow: hidden;
    padding-bottom: 0.08em;
    margin-bottom: -0.08em;
  }
  .hero-line > span {
    display: block;
    transform: translateY(110%);
    animation: line-up var(--dur-xl) var(--ease-out-expo) both;
    animation-delay: var(--d, 0ms);
  }
  @keyframes line-up {
    to { transform: translateY(0); }
  }

  /* Dot on the heading — subtle pulse after entry */
  .hero-dot {
    display: inline-block;
    animation: dot-pop var(--dur-md) var(--ease-spring) 900ms both;
  }
  @keyframes dot-pop {
    from { transform: scale(0); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
  }

  /* =====================================================================
     BUTTONS — layered micro-interactions
  ===================================================================== */
  .btn {
    position: relative;
    overflow: hidden;
    isolation: isolate;
    transition:
      background-color var(--dur-sm) var(--ease-out-quart),
      color            var(--dur-sm) var(--ease-out-quart),
      border-color     var(--dur-sm) var(--ease-out-quart),
      box-shadow       var(--dur-sm) var(--ease-out-quart),
      transform        var(--dur-xs) var(--ease-out-quart);
  }
  .btn:active { transform: translateY(1px) scale(0.99); }

  /* Shine that sweeps across on hover */
  .btn::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(
      105deg,
      transparent 30%,
      rgba(255,255,255,0.22) 50%,
      transparent 70%
    );
    transform: translateX(-120%);
    transition: transform var(--dur-lg) var(--ease-out-expo);
    pointer-events: none;
  }
  .btn:hover::before { transform: translateX(120%); }

  /* Arrow nudge */
  .btn .arrow {
    display: inline-block;
    transition: transform var(--dur-sm) var(--ease-spring);
  }
  .btn:hover .arrow { transform: translateX(4px); }

  /* Underline draw for ghost buttons */
  .btn-ghost { position: relative; }
  .btn-ghost::after {
    content: "";
    position: absolute;
    left: 1.5rem; right: 1.5rem; bottom: 0.6rem;
    height: 1px;
    background: currentColor;
    transform: scaleX(0);
    transform-origin: left center;
    transition: transform var(--dur-md) var(--ease-out-expo);
  }
  .btn-ghost:hover::after { transform: scaleX(1); }

  /* =====================================================================
     STAMP SEAL — spring entrance + idle breathing + hover wobble
  ===================================================================== */
  .stamp {
    position: relative;
    display: grid;
    place-items: center;
    width: 11.5rem; height: 11.5rem;
    border: 2.5px solid currentColor;
    border-radius: 9999px;
    transform: rotate(-6deg) scale(0.6);
    opacity: 0;
    animation:
      stamp-in var(--dur-lg) var(--ease-spring) 400ms forwards,
      stamp-breathe 5s ease-in-out 1800ms infinite;
    transition: transform var(--dur-md) var(--ease-spring);
  }
  .stamp::before {
    content: "";
    position: absolute; inset: 7px;
    border: 1px solid currentColor;
    border-radius: 9999px;
    opacity: .4;
    animation: stamp-ring var(--dur-lg) var(--ease-out-expo) 620ms both;
  }
  @keyframes stamp-in {
    from { opacity: 0; transform: rotate(-22deg) scale(0.55); }
    to   { opacity: 1; transform: rotate(-6deg) scale(1); }
  }
  @keyframes stamp-ring {
    from { transform: scale(0.85); opacity: 0; }
    to   { transform: scale(1);    opacity: .4; }
  }
  @keyframes stamp-breathe {
    0%, 100% { transform: rotate(-6deg) scale(1); }
    50%      { transform: rotate(-6deg) scale(1.015); }
  }
  .stamp:hover {
    animation-play-state: paused;
    transform: rotate(-2.5deg) scale(1.045);
  }

  /* =====================================================================
     STATS — staggered card entry
  ===================================================================== */
  .stat {
    opacity: 0;
    animation: rise-soft var(--dur-md) var(--ease-out-expo) forwards;
    animation-delay: calc(600ms + var(--i, 0) * var(--step));
  }

  /* =====================================================================
     FORM — floating focus underline + label lift
  ===================================================================== */
  .field { position: relative; }
  .field input {
    transition:
      border-color var(--dur-sm) var(--ease-out-quart),
      box-shadow   var(--dur-sm) var(--ease-out-quart),
      background   var(--dur-sm) var(--ease-out-quart);
  }
  .field::after {
    content: "";
    position: absolute;
    left: 0; right: 0; bottom: 0;
    height: 2px;
    background: currentColor;
    transform: scaleX(0);
    transform-origin: center;
    transition: transform var(--dur-md) var(--ease-out-expo);
    pointer-events: none;
    border-radius: 0 0 2px 2px;
  }
  .field:focus-within::after { transform: scaleX(1); }
  .field.field-debit  { color: #A23E30; }
  .field.field-credit { color: #2F6F4E; }
  .field.field-neutral{ color: #1F4E36; }

  /* =====================================================================
     TABLE ROWS — cascade in with per-row stagger
  ===================================================================== */
  .row-line {
    transition: background-color var(--dur-xs) var(--ease-out-quart);
    animation: row-in var(--dur-md) var(--ease-out-expo) both;
    animation-delay: calc(var(--i, 0) * 45ms);
  }
  @keyframes row-in {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .row-line td { border-bottom: 1px solid rgba(30,43,34,0.075); }
  .row-line:nth-child(even) { background: rgba(47,111,78,0.032); }
  .row-line:hover { background: rgba(47,111,78,0.075); }
  .row-line:last-child td { border-bottom: 0; }

  /* Row flash on new entry — a scanning highlight */
  @keyframes row-scan {
    0%   { background-color: rgba(173,138,50,0.28); box-shadow: inset 3px 0 0 #AD8A32; }
    60%  { background-color: rgba(173,138,50,0.10); box-shadow: inset 3px 0 0 #AD8A32; }
    100% { background-color: transparent;           box-shadow: inset 3px 0 0 transparent; }
  }
  .flash { animation: row-scan 2s var(--ease-out-expo) both; }

  /* =====================================================================
     FILTER TABS
  ===================================================================== */
  .tab {
    position: relative;
    overflow: hidden;
    transition:
      background-color var(--dur-sm) var(--ease-out-quart),
      color            var(--dur-sm) var(--ease-out-quart),
      border-color     var(--dur-sm) var(--ease-out-quart),
      transform        var(--dur-xs) var(--ease-out-quart);
  }
  .tab:active { transform: scale(0.97); }
  .tab[aria-selected="true"] {
    background: #1F4E36;
    color: #FBFAF4;
    border-color: #1F4E36;
    box-shadow: 0 6px 16px -10px rgba(31,78,54,0.9);
  }
  .tab .num {
    display: inline-block;
    transition: transform var(--dur-sm) var(--ease-spring);
  }
  .tab[aria-selected="true"] .num { transform: scale(1.08); }

  /* =====================================================================
     TOAST — spring entrance + auto-dismiss progress bar
  ===================================================================== */
  #toast {
    opacity: 0;
    transform: translate(-50%, 1.5rem) scale(0.96);
    pointer-events: none;
    transition:
      opacity   var(--dur-sm) var(--ease-out-quart),
      transform var(--dur-md) var(--ease-spring);
  }
  #toast.show {
    opacity: 1;
    transform: translate(-50%, 0) scale(1);
  }
  #toastProgress {
    position: absolute;
    left: 0; bottom: 0; height: 2px;
    background: #2F6F4E;
    width: 100%;
    transform-origin: left center;
    border-radius: 0 0 2px 2px;
  }
  #toast.show #toastProgress {
    animation: toast-countdown 2.6s linear forwards;
  }
  @keyframes toast-countdown {
    from { transform: scaleX(1); }
    to   { transform: scaleX(0); }
  }

  /* =====================================================================
     SEARCH — expanding field with smooth width transition
  ===================================================================== */
  #search {
    transition:
      width            var(--dur-md) var(--ease-out-expo),
      border-color     var(--dur-sm) var(--ease-out-quart),
      box-shadow       var(--dur-sm) var(--ease-out-quart),
      padding          var(--dur-md) var(--ease-out-expo);
  }

  /* =====================================================================
     SCROLL REVEAL — IntersectionObserver-driven, staggered
  ===================================================================== */
  .reveal {
    opacity: 0;
    transform: translateY(24px);
    transition:
      opacity   var(--dur-lg) var(--ease-out-expo),
      transform var(--dur-lg) var(--ease-out-expo);
    transition-delay: var(--rd, 0ms);
  }
  .reveal.in { opacity: 1; transform: none; }

  /* =====================================================================
     CURSOR GLOW — a soft radial highlight following the pointer on the card
  ===================================================================== */
  .cursor-glow {
    position: relative;
  }
  .cursor-glow::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    pointer-events: none;
    opacity: 0;
    transition: opacity var(--dur-md) var(--ease-out-quart);
    background: radial-gradient(
      320px circle at var(--mx, 50%) var(--my, 50%),
      rgba(47,111,78,0.10),
      transparent 65%
    );
    z-index: 1;
  }
  .cursor-glow:hover::before { opacity: 1; }

  /* =====================================================================
     HEADER — smooth scrolled state
  ===================================================================== */
  #siteHeader {
    transition:
      background-color var(--dur-md) var(--ease-out-quart),
      border-color     var(--dur-md) var(--ease-out-quart),
      box-shadow       var(--dur-md) var(--ease-out-quart),
      backdrop-filter  var(--dur-md) var(--ease-out-quart);
  }
  #siteHeader.scrolled {
    border-color: rgba(30,43,34,0.10);
    box-shadow: 0 1px 2px rgba(30,43,34,0.04), 0 10px 30px -24px rgba(30,43,34,0.5);
  }

  /* Logo mark — idle tilt on hover */
  .logo-mark {
    transition: transform var(--dur-md) var(--ease-spring),
                background-color var(--dur-sm) var(--ease-out-quart);
  }
  .logo-mark:hover { transform: rotate(-8deg) scale(1.05); }

  /* =====================================================================
     LIVE DOT — pulsing status indicator
  ===================================================================== */
  .live-dot { position: relative; }
  .live-dot::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    background: #2F6F4E;
    animation: live-ping 1.8s var(--ease-out-expo) infinite;
  }
  @keyframes live-ping {
    0%   { transform: scale(1);   opacity: 0.55; }
    80%  { transform: scale(2.6); opacity: 0; }
    100% { transform: scale(2.6); opacity: 0; }
  }

  /* =====================================================================
     EMPTY STATE — gentle float
  ===================================================================== */
  @keyframes float-soft {
    0%, 100% { transform: translateY(0); }
    50%      { transform: translateY(-4px); }
  }
  .float-soft { animation: float-soft 3.5s ease-in-out infinite; }

  /* =====================================================================
     NUMBER FIGURES — tabular, monospaced digits
  ===================================================================== */
  .figure { font-variant-numeric: tabular-nums; }

  .eyebrow {
    font-family: 'IBM Plex Mono', monospace;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    font-size: 0.7rem;
  }

  /* =====================================================================
     REDUCED MOTION — respect the user's preference
     A senior dev always ships this.
  ===================================================================== */
  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
      animation-duration: 0.001ms !important;
      animation-iteration-count: 1 !important;
      transition-duration: 0.001ms !important;
      scroll-behavior: auto !important;
    }
    .rise, .rise-soft, .stat, .row-line,
    .hero-line > span, .stamp, .stamp::before {
      opacity: 1 !important;
      transform: none !important;
    }
    .reveal { opacity: 1; transform: none; }
  }
</style>
</head>

<body class="font-body text-ink antialiased ruled-bg">

  <!-- ====== HEADER / NAV ====== -->
  <header id="siteHeader" class="sticky top-0 z-40 bg-paper/80 backdrop-blur-md border-b border-transparent">
    <div class="max-w-6xl mx-auto px-6 lg:px-8 py-4 flex items-center justify-between">

      <a href="#" class="flex items-center gap-3 group">
        <div class="logo-mark w-9 h-9 rounded-sm bg-ledgerDeep flex items-center justify-center text-paperCard font-display font-semibold text-lg">
          L
        </div>
        <div>
          <div class="font-display font-semibold text-lg leading-none">Ledgerbook</div>
          <div class="eyebrow text-inkSoft leading-none mt-1">Double-entry, made plain</div>
        </div>
      </a>

      @if (Route::has('login'))
        <nav class="flex items-center gap-1 sm:gap-2">
          @auth
            <a href="{{ url('/dashboard') }}"
               class="btn px-4 py-2 text-sm font-medium rounded-sm bg-ledgerDeep text-paperCard hover:bg-ledger">
              Dashboard
            </a>
          @else
            <a href="{{ route('login') }}"
               class="px-4 py-2 text-sm font-medium text-inkSoft hover:text-ink transition-colors duration-200">
              Log in
            </a>
            @if (Route::has('register'))
              <a href="{{ route('register') }}"
                 class="btn px-4 py-2 text-sm font-medium rounded-sm bg-ledgerDeep text-paperCard hover:bg-ledger">
                Open a ledger
              </a>
            @endif
          @endauth
        </nav>
      @endif

    </div>
  </header>

  <!-- ====== HERO ====== -->
  <section class="max-w-6xl mx-auto px-6 lg:px-8 pt-12 sm:pt-16 pb-16">
    <div class="grid lg:grid-cols-5 gap-12 lg:gap-10 items-center">

      <div class="lg:col-span-3">
        <p class="eyebrow text-gold mb-4 rise" style="--d:0ms">Folio No. 0042 &middot; August 2026</p>

        <h1 class="font-display font-semibold text-[2.6rem] sm:text-6xl lg:text-[4.25rem] leading-[1.03] tracking-tight">
          <span class="hero-line"><span style="--d:120ms">Every debit finds</span></span>
          <span class="hero-line"><span style="--d:240ms">its credit<span class="text-ledger hero-dot">.</span></span></span>
        </h1>

        <p class="mt-6 text-base sm:text-lg text-inkSoft max-w-md leading-relaxed rise" style="--d:480ms">
          A running record of what came in, what went out, and what's left &mdash;
          kept the way a good bookkeeper would keep it. No spreadsheets to fight,
          no balance to chase.
        </p>

        <div class="mt-9 flex flex-wrap items-center gap-4 rise" style="--d:600ms">
          <a href="#new-entry"
             class="btn group inline-flex items-center gap-2 px-6 py-3 bg-brick text-paperCard rounded-sm font-medium text-sm shadow-sm hover:bg-brick/90 hover:shadow-md">
            Post an entry
            <span class="arrow">→</span>
          </a>
          <a href="#ledger"
             class="btn btn-ghost group inline-flex items-center gap-2 px-6 py-3 border border-ink/20 rounded-sm font-medium text-sm hover:bg-ink/5 hover:border-ink/35">
            View the book
            <span class="arrow">→</span>
          </a>
        </div>

        <div class="mt-10 flex items-center gap-3 text-xs text-inkSoft rise" style="--d:720ms">
          <span class="live-dot relative flex h-1.5 w-1.5">
            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-ledger"></span>
          </span>
          <span class="font-mono">Ledger reconciled &middot; <span id="lastSync">moments ago</span></span>
        </div>
      </div>

      <!-- net balance seal -->
      <div class="lg:col-span-2 flex justify-center lg:justify-end">
        <div class="stamp text-ledgerDeep shrink-0">
          <div class="flex flex-col items-center justify-center text-center px-6">
            <span class="eyebrow opacity-70">Net position</span>
            <span id="stampValue" class="font-display font-semibold text-[2rem] mt-1.5 figure leading-none">$0</span>
            <span class="eyebrow mt-2 opacity-70">In balance</span>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- ====== MAIN LEDGER CARD ====== -->
  <section class="max-w-6xl mx-auto px-6 lg:px-8 pb-20">
    <div class="spine grain cursor-glow relative bg-paperCard rounded-md border border-ink/10 shadow-card pl-8 pr-6 sm:pl-12 sm:pr-9 py-9">

      <!-- Stats row -->
      <div class="relative z-10 grid grid-cols-2 lg:grid-cols-4 gap-px bg-ink/[0.08] rounded-sm overflow-hidden mb-10 border border-ink/[0.08]">
        <div class="stat bg-paperCard px-5 py-4" style="--i:0">
          <div class="eyebrow text-inkSoft">Total debit</div>
          <div id="statDebit" class="font-mono text-2xl font-medium text-brick mt-1.5 figure">$0.00</div>
        </div>
        <div class="stat bg-paperCard px-5 py-4" style="--i:1">
          <div class="eyebrow text-inkSoft">Total credit</div>
          <div id="statCredit" class="font-mono text-2xl font-medium text-ledger mt-1.5 figure">$0.00</div>
        </div>
        <div class="stat bg-paperCard px-5 py-4" style="--i:2">
          <div class="eyebrow text-inkSoft">Net balance</div>
          <div id="statNet" class="font-mono text-2xl font-medium text-ledgerDeep mt-1.5 figure">$0.00</div>
        </div>
        <div class="stat bg-paperCard px-5 py-4" style="--i:3">
          <div class="eyebrow text-inkSoft">Entries</div>
          <div id="statCount" class="font-mono text-2xl font-medium text-gold mt-1.5 figure">0</div>
        </div>
      </div>

      <!-- Quick Add Form -->
      <div id="new-entry" class="relative z-10 mb-12">
        <div class="flex items-baseline justify-between gap-4 mb-4">
          <h2 class="font-display font-semibold text-lg flex items-center gap-2">
            <span class="text-gold text-xl leading-none">＋</span> New entry
          </h2>
          <span class="eyebrow text-inkSoft/70 hidden sm:block">Fill one side &middot; the other stays empty</span>
        </div>

        <form id="entryForm" novalidate class="grid sm:grid-cols-12 gap-3 items-end">
          <div class="sm:col-span-2 field field-neutral">
            <label for="f-date" class="eyebrow text-inkSoft block mb-1.5">Date</label>
            <input id="f-date" name="date" type="date" required
              class="w-full rounded-sm border border-ink/15 bg-white px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-ledger/30 focus:border-ledger outline-none" />
          </div>

          <div class="sm:col-span-4 field field-neutral">
            <label for="f-desc" class="eyebrow text-inkSoft block mb-1.5">Description</label>
            <input id="f-desc" name="desc" type="text" required placeholder="Invoice #1234" autocomplete="off"
              class="w-full rounded-sm border border-ink/15 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-ledger/30 focus:border-ledger outline-none placeholder:text-ink/25" />
          </div>

          <div class="sm:col-span-2 field field-debit">
            <label for="f-debit" class="eyebrow text-inkSoft block mb-1.5">Debit</label>
            <input id="f-debit" name="debit" type="number" step="0.01" min="0" placeholder="0.00"
              class="w-full rounded-sm border border-ink/15 bg-white px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-brick/30 focus:border-brick outline-none placeholder:text-ink/25" />
          </div>

          <div class="sm:col-span-2 field field-credit">
            <label for="f-credit" class="eyebrow text-inkSoft block mb-1.5">Credit</label>
            <input id="f-credit" name="credit" type="number" step="0.01" min="0" placeholder="0.00"
              class="w-full rounded-sm border border-ink/15 bg-white px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-ledger/30 focus:border-ledger outline-none placeholder:text-ink/25" />
          </div>

          <div class="sm:col-span-2">
            <button type="submit"
              class="btn group w-full inline-flex items-center justify-center gap-1.5 px-5 py-2 bg-ledgerDeep text-paperCard rounded-sm text-sm font-medium hover:bg-ledger">
              Post
              <span class="opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-200">↵</span>
            </button>
          </div>
        </form>

        <p id="formHint" class="mt-2.5 text-xs text-brick h-4 font-mono" role="alert"></p>
      </div>

      <!-- Ledger Table -->
      <div id="ledger" class="relative z-10">
        <div class="flex flex-wrap items-end justify-between gap-4 mb-5">
          <div>
            <h2 class="font-display font-semibold text-lg">The book</h2>
            <p class="text-xs text-inkSoft mt-1">Newest first &middot; running balance recalculated on every post</p>
          </div>

          <div class="flex items-center gap-2">
            <div class="relative">
              <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-ink/35" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="9" cy="9" r="6"/><path d="M13.5 13.5 17 17" stroke-linecap="round"/>
              </svg>
              <input id="search" type="search" placeholder="Search the book…" aria-label="Search entries"
                class="w-40 sm:w-56 rounded-sm border border-ink/15 bg-white pl-8 pr-3 py-1.5 text-xs focus:w-52 sm:focus:w-64 focus:ring-2 focus:ring-ledger/30 focus:border-ledger outline-none placeholder:text-ink/30" />
            </div>

            <button id="exportBtn" type="button"
              class="btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-ink/15 rounded-sm hover:bg-ink/5">
              <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 3v9m0 0 3.5-3.5M10 12 6.5 8.5M4 15.5h12"/>
              </svg>
              Export
            </button>
          </div>
        </div>

        <!-- Filters -->
        <div class="flex items-center gap-1.5 mb-4" role="tablist" aria-label="Filter entries">
          <button class="tab px-3 py-1.5 text-xs font-medium border border-ink/15 rounded-sm text-inkSoft hover:border-ink/30" data-filter="all" role="tab" aria-selected="true">
            All <span class="num opacity-50 font-mono ml-0.5" data-count="all">0</span>
          </button>
          <button class="tab px-3 py-1.5 text-xs font-medium border border-ink/15 rounded-sm text-inkSoft hover:border-ink/30" data-filter="debit" role="tab" aria-selected="false">
            Debits <span class="num opacity-50 font-mono ml-0.5" data-count="debit">0</span>
          </button>
          <button class="tab px-3 py-1.5 text-xs font-medium border border-ink/15 rounded-sm text-inkSoft hover:border-ink/30" data-filter="credit" role="tab" aria-selected="false">
            Credits <span class="num opacity-50 font-mono ml-0.5" data-count="credit">0</span>
          </button>
        </div>

        <div class="rounded-sm border border-ink/10 overflow-hidden bg-paperCard">

          <!-- Desktop / tablet table -->
          <div class="hidden md:block overflow-x-auto">
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
              <tbody id="ledgerBody"></tbody>
            </table>
          </div>

          <!-- Mobile card list -->
          <div id="mobileList" class="md:hidden divide-y divide-ink/[0.08]"></div>

          <!-- Empty state -->
          <div id="emptyState" class="hidden px-6 py-16 text-center">
            <div class="float-soft w-12 h-12 mx-auto rounded-full border border-dashed border-ink/25 flex items-center justify-center text-ink/30 text-xl">∅</div>
            <p class="mt-4 font-display font-semibold text-ink">Nothing on this page</p>
            <p class="text-sm text-inkSoft mt-1">Try a different filter, or post a new entry above.</p>
          </div>

        </div>
      </div>

    </div>
  </section>

  <!-- ====== FEATURES ====== -->
  <section class="max-w-6xl mx-auto px-6 lg:px-8 pb-24 grid sm:grid-cols-3 gap-8">
    <div class="reveal group border-t-2 border-ledgerDeep pt-5 transition-transform duration-500 hover:-translate-y-1" style="--rd:0ms">
      <div class="eyebrow text-gold mb-2">Balance</div>
      <h3 class="font-display font-semibold text-lg mb-1.5">Real-time position</h3>
      <p class="text-sm text-inkSoft leading-relaxed">Every entry recalculates the running balance the moment it's posted.</p>
    </div>
    <div class="reveal group border-t-2 border-ledgerDeep pt-5 transition-transform duration-500 hover:-translate-y-1" style="--rd:120ms">
      <div class="eyebrow text-gold mb-2">History</div>
      <h3 class="font-display font-semibold text-lg mb-1.5">A full paper trail</h3>
      <p class="text-sm text-inkSoft leading-relaxed">Nothing is overwritten. Every date, description, and amount stays on the record.</p>
    </div>
    <div class="reveal group border-t-2 border-ledgerDeep pt-5 transition-transform duration-500 hover:-translate-y-1" style="--rd:240ms">
      <div class="eyebrow text-gold mb-2">Insight</div>
      <h3 class="font-display font-semibold text-lg mb-1.5">Where the money moved</h3>
      <p class="text-sm text-inkSoft leading-relaxed">Spot the pattern in your debits and credits before it becomes a problem.</p>
    </div>
  </section>

  <!-- ====== FOOTER ====== -->
  <footer class="max-w-6xl mx-auto px-6 lg:px-8 py-8 border-t border-ink/10 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-inkSoft">
    <span>&copy; 2026 Ledgerbook</span>
    <span class="font-mono">Kept honestly, balanced daily.</span>
  </footer>

  <!-- ====== TOAST ====== -->
  <div id="toast" role="status" aria-live="polite"
       class="fixed bottom-6 left-1/2 z-50 flex items-center gap-2.5 px-4 py-3 pr-6 rounded-sm bg-ink text-paperCard text-sm shadow-lift overflow-hidden">
    <span id="toastIcon" class="text-ledger">✓</span>
    <span id="toastMsg">Entry posted</span>
    <span id="toastProgress"></span>
  </div>

<script>
(function () {
  'use strict';

  /* ------------------------------------------------------------------
     Data
  ------------------------------------------------------------------ */
  let uid = 0;
  const seed = [
    { date: '2026-07-22', desc: 'Retainer — Northwind Co.',        debit: 0,      credit: 3200.00 },
    { date: '2026-07-24', desc: 'Domain & SSL renewal',            debit: 84.00,  credit: 0 },
    { date: '2026-07-27', desc: 'Client payment — Acme Corp',      debit: 0,      credit: 1200.00 },
    { date: '2026-07-29', desc: 'Freelance project — DesignStudio',debit: 0,      credit: 1750.50 },
    { date: '2026-07-30', desc: 'Server hosting — AWS',            debit: 890.00, credit: 0 },
    { date: '2026-07-31', desc: 'Consulting fee — MegaCorp',       debit: 0,      credit: 2500.00 },
    { date: '2026-08-01', desc: 'Office supplies — Staples',       debit: 340.50, credit: 0 },
    { date: '2026-08-02', desc: 'Client payment — Acme Corp',      debit: 0,      credit: 1200.00 },
    { date: '2026-08-03', desc: 'Studio rent — August',            debit: 1400.00,credit: 0 },
  ];

  let entries = seed.map(e => ({ id: ++uid, ...e }));
  let flashId = null;

  /* ------------------------------------------------------------------
     Helpers
  ------------------------------------------------------------------ */
  const $ = sel => document.querySelector(sel);
  const $$ = sel => Array.from(document.querySelectorAll(sel));

  const money = new Intl.NumberFormat('en-US', {
    style: 'currency', currency: 'USD',
    minimumFractionDigits: 2, maximumFractionDigits: 2,
  });
  const moneyCompact = new Intl.NumberFormat('en-US', {
    style: 'currency', currency: 'USD',
    minimumFractionDigits: 0, maximumFractionDigits: 0,
  });

  const fmtDate = iso => {
    const [, m, d] = iso.split('-').map(Number);
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return `${months[m - 1]} ${String(d).padStart(2, '0')}`;
  };

  function withBalances() {
    let bal = 0;
    return entries.map(e => {
      bal += (e.credit || 0) - (e.debit || 0);
      return { ...e, balance: bal };
    });
  }

  /* ------------------------------------------------------------------
     Animated counters — cubic ease-out, RAF driven
  ------------------------------------------------------------------ */
  function animateNumber(el, to, formatter) {
    const from = Number(el.dataset.value || 0);
    if (from === to) { el.textContent = formatter(to); return; }
    el.dataset.value = String(to);

    const start = performance.now();
    const dur = 720;
    const ease = t => 1 - Math.pow(1 - t, 4); // easeOutQuart

    function step(now) {
      const t = Math.min(1, (now - start) / dur);
      el.textContent = formatter(from + (to - from) * ease(t));
      if (t < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  /* ------------------------------------------------------------------
     Toast
  ------------------------------------------------------------------ */
  let toastTimer;
  function toast(msg, tone) {
    const el = $('#toast');
    $('#toastMsg').textContent = msg;
    const icon = $('#toastIcon');
    icon.textContent = tone === 'error' ? '!' : '✓';
    icon.className = tone === 'error' ? 'text-brick' : 'text-ledger';

    // reset the progress bar animation
    const bar = $('#toastProgress');
    bar.style.animation = 'none';
    void bar.offsetWidth;
    bar.style.animation = '';

    el.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => el.classList.remove('show'), 2600);
  }

  /* ------------------------------------------------------------------
     State
  ------------------------------------------------------------------ */
  let filter = 'all';
  let query  = '';

  function visibleRows(data) {
    const q = query.trim().toLowerCase();
    return data
      .slice()
      .reverse()
      .filter(e => {
        if (filter === 'debit'  && !(e.debit  > 0)) return false;
        if (filter === 'credit' && !(e.credit > 0)) return false;
        if (q && !e.desc.toLowerCase().includes(q) && !e.date.includes(q)) return false;
        return true;
      });
  }

  /* ------------------------------------------------------------------
     Render
  ------------------------------------------------------------------ */
  const body       = $('#ledgerBody');
  const mobileList = $('#mobileList');
  const emptyState = $('#emptyState');

  function badge(type) {
    return type === 'credit'
      ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-ledger/10 text-ledgerDeep">Credit</span>'
      : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brick/10 text-brick">Debit</span>';
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, c => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[c]));
  }

  function render() {
    const data = withBalances();
    const rows = visibleRows(data);

    /* ---- stats ---- */
    const totalDebit  = entries.reduce((s, e) => s + (e.debit  || 0), 0);
    const totalCredit = entries.reduce((s, e) => s + (e.credit || 0), 0);
    const net         = totalCredit - totalDebit;

    animateNumber($('#statDebit'),  totalDebit,  v => money.format(v));
    animateNumber($('#statCredit'), totalCredit, v => money.format(v));
    animateNumber($('#statNet'),    net,         v => money.format(v));
    animateNumber($('#statCount'),  entries.length, v => String(Math.round(v)));
    animateNumber($('#stampValue'), net, v => moneyCompact.format(Math.round(v)));

    /* ---- tab counts ---- */
    const counts = {
      all:    entries.length,
      debit:  entries.filter(e => e.debit  > 0).length,
      credit: entries.filter(e => e.credit > 0).length,
    };
    $$('[data-count]').forEach(el => {
      el.textContent = counts[el.dataset.count];
    });

    /* ---- empty ---- */
    const isEmpty = rows.length === 0;
    emptyState.classList.toggle('hidden', !isEmpty);
    const tableWrap = body.closest('.overflow-x-auto');
    if (tableWrap) tableWrap.classList.toggle('hidden', isEmpty);

    /* ---- desktop rows (staggered via --i) ---- */
    body.innerHTML = rows.map((e, i) => {
      const isCredit = e.credit > 0;
      const flash = e.id === flashId ? ' flash' : '';
      return `
        <tr class="row-line${flash}" style="--i:${i}">
          <td class="py-3 px-4 font-mono text-inkSoft text-xs whitespace-nowrap">${fmtDate(e.date)}<span class="sr-only"> ${e.date}</span></td>
          <td class="py-3 px-4">${escapeHtml(e.desc)}</td>
          <td class="py-3 px-4 text-right font-mono ${isCredit ? 'text-ink/25' : 'text-brick'} figure">${isCredit ? '—' : money.format(e.debit)}</td>
          <td class="py-3 px-4 text-right font-mono ${isCredit ? 'text-ledger' : 'text-ink/25'} figure">${isCredit ? money.format(e.credit) : '—'}</td>
          <td class="py-3 px-4 text-right font-mono font-medium figure">${money.format(e.balance)}</td>
          <td class="py-3 px-4 text-center">${badge(isCredit ? 'credit' : 'debit')}</td>
        </tr>`;
    }).join('');

    /* ---- mobile cards (staggered via --i) ---- */
    mobileList.innerHTML = rows.map((e, i) => {
      const isCredit = e.credit > 0;
      const flash = e.id === flashId ? ' flash' : '';
      return `
        <div class="p-4${flash}" style="--i:${i}; animation: row-in var(--dur-md) var(--ease-out-expo) both; animation-delay: calc(${i} * 45ms);">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <div class="text-sm font-medium truncate">${escapeHtml(e.desc)}</div>
              <div class="font-mono text-xs text-inkSoft mt-0.5">${fmtDate(e.date)}</div>
            </div>
            ${badge(isCredit ? 'credit' : 'debit')}
          </div>
          <div class="mt-3 flex items-end justify-between">
            <div class="font-mono text-lg font-medium ${isCredit ? 'text-ledger' : 'text-brick'} figure">
              ${isCredit ? '+' : '−'}${money.format(isCredit ? e.credit : e.debit)}
            </div>
            <div class="text-right">
              <div class="eyebrow text-inkSoft/70">Balance</div>
              <div class="font-mono text-sm font-medium figure">${money.format(e.balance)}</div>
            </div>
          </div>
        </div>`;
    }).join('');

    flashId = null;
  }

  /* ------------------------------------------------------------------
     Form
  ------------------------------------------------------------------ */
  const form  = $('#entryForm');
  const hint  = $('#formHint');

  (function setToday() {
    const d = new Date();
    const iso = new Date(d.getTime() - d.getTimezoneOffset() * 60000)
      .toISOString().slice(0, 10);
    $('#f-date').value = iso;
  })();

  // Clear the counterpart field when one side is filled
  $('#f-debit').addEventListener('input', e => {
    if (e.target.value) $('#f-credit').value = '';
  });
  $('#f-credit').addEventListener('input', e => {
    if (e.target.value) $('#f-debit').value = '';
  });

  form.addEventListener('submit', ev => {
    ev.preventDefault();
    hint.textContent = '';

    const date   = $('#f-date').value;
    const desc   = $('#f-desc').value.trim();
    const debit  = parseFloat($('#f-debit').value)  || 0;
    const credit = parseFloat($('#f-credit').value) || 0;

    if (!date || !desc) {
      hint.textContent = 'A date and a description are required.';
      form.animate(
        [{ transform: 'translateX(0)' }, { transform: 'translateX(-4px)' },
         { transform: 'translateX(4px)' }, { transform: 'translateX(-3px)' },
         { transform: 'translateX(3px)' }, { transform: 'translateX(0)' }],
        { duration: 420, easing: 'ease-in-out' }
      );
      return;
    }
    if (debit <= 0 && credit <= 0) {
      hint.textContent = 'Enter an amount on the debit or the credit side.';
      form.animate(
        [{ transform: 'translateX(0)' }, { transform: 'translateX(-4px)' },
         { transform: 'translateX(4px)' }, { transform: 'translateX(-3px)' },
         { transform: 'translateX(3px)' }, { transform: 'translateX(0)' }],
        { duration: 420, easing: 'ease-in-out' }
      );
      return;
    }

    const entry = {
      id: ++uid,
      date,
      desc,
      debit:  Math.round(debit  * 100) / 100,
      credit: Math.round(credit * 100) / 100,
    };

    entries.push(entry);
    entries.sort((a, b) => a.date.localeCompare(b.date) || a.id - b.id);

    flashId = entry.id;
    render();

    form.reset();
    $('#f-date').value = date;
    $('#f-desc').focus();

    toast(`Posted ${money.format(entry.credit || entry.debit)} · ${entry.desc}`, 'ok');
  });

  /* ------------------------------------------------------------------
     Filters + search
  ------------------------------------------------------------------ */
  $$('.tab').forEach(btn => {
    btn.addEventListener('click', () => {
      filter = btn.dataset.filter;
      $$('.tab').forEach(b => b.setAttribute('aria-selected', String(b === btn)));
      render();
    });
  });

  let searchTimer;
  $('#search').addEventListener('input', e => {
    clearTimeout(searchTimer);
    const val = e.target.value;
    searchTimer = setTimeout(() => { query = val; render(); }, 120);
  });

  /* ------------------------------------------------------------------
     CSV export
  ------------------------------------------------------------------ */
  $('#exportBtn').addEventListener('click', () => {
    const rows = [['Date', 'Description', 'Debit', 'Credit', 'Balance']];
    withBalances().forEach(e => rows.push([
      e.date, e.desc,
      e.debit ? e.debit.toFixed(2) : '',
      e.credit ? e.credit.toFixed(2) : '',
      e.balance.toFixed(2),
    ]));
    const csv = rows
      .map(r => r.map(c => `"${String(c).replace(/"/g, '""')}"`).join(','))
      .join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'ledgerbook.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    toast('Ledger exported as CSV', 'ok');
  });

  /* ------------------------------------------------------------------
     Header scroll state — passive listener
  ------------------------------------------------------------------ */
  const header = $('#siteHeader');
  const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 8);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ------------------------------------------------------------------
     Scroll reveal — IntersectionObserver, unobserve after triggering
  ------------------------------------------------------------------ */
  const io = new IntersectionObserver((obs) => {
    obs.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('in');
        io.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
  $$('.reveal').forEach(el => io.observe(el));

  /* ------------------------------------------------------------------
     Cursor glow — track pointer over the ledger card
  ------------------------------------------------------------------ */
  const glow = document.querySelector('.cursor-glow');
  if (glow && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    glow.addEventListener('pointermove', e => {
      const r = glow.getBoundingClientRect();
      glow.style.setProperty('--mx', `${e.clientX - r.left}px`);
      glow.style.setProperty('--my', `${e.clientY - r.top}px`);
    });
  }

  /* ------------------------------------------------------------------
     Relative "last synced" ticker
  ------------------------------------------------------------------ */
  let secs = 4;
  setInterval(() => {
    secs += 1;
    const el = $('#lastSync');
    if (!el) return;
    el.textContent = secs < 60
      ? `${secs}s ago`
      : `${Math.floor(secs / 60)}m ago`;
  }, 1000);

  /* ------------------------------------------------------------------
     Go
  ------------------------------------------------------------------ */
  render();
})();
</script>

</body>
</html>