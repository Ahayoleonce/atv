<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Abasobanuzi · AGASOBANUYE TV</title>
  <link rel="shortcut icon" href="../assets/agasobanuye.svg" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#08090b;
      --bg-alt:#0c0e12;
      --surface:#121419;
      --surface-2:#181c22;
      --border:rgba(255,255,255,.09);
      --border-soft:rgba(255,255,255,.06);
      --text:#f4f5f6;
      --dim:#a3a7b0;
      --dim-2:#63676f;
      --accent:#e9eaec;
      --accent-dim:rgba(233,234,236,.12);
      --white-accent:#f5f6f8;
      --white-accent-dim:rgba(245,246,248,.10);
    }
    *{margin:0;padding:0;box-sizing:border-box;}
    html{scroll-behavior:smooth;}
    ::selection{background:var(--accent);color:#0a0a0a;}
    body{
      background:
        radial-gradient(ellipse 80% 50% at 50% -10%, rgba(60,64,74,.35), transparent 60%),
        radial-gradient(ellipse 60% 40% at 100% 100%, rgba(40,36,30,.28), transparent 60%),
        linear-gradient(180deg, #0a0b0e 0%, #08090b 45%, #08090b 100%);
      color:var(--text);
      font-family:'Inter',sans-serif;
      -webkit-font-smoothing:antialiased;
      min-height:100vh;
      overflow-x:hidden;
    }

    /* ---------- ambient backdrop (same as index) ---------- */
    .bg-field{position:fixed; inset:0; z-index:0; overflow:hidden; pointer-events:none;}
    .bg-field::before{
      content:""; position:absolute; inset:0;
      background-image:radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px);
      background-size:34px 34px;
      mask-image:radial-gradient(ellipse 70% 50% at 50% 0%, black 20%, transparent 85%);
      opacity:.4;
    }
    .bg-canvas{position:absolute; inset:0; width:100%; height:100%; opacity:.9;}
    .orb{
      position:absolute;border-radius:50%;filter:blur(90px);opacity:.16;will-change:transform;
      background:radial-gradient(circle,var(--accent),transparent 70%);
    }
    .orb.o1{width:640px;height:640px;top:-260px;left:-160px;animation:drift1 26s ease-in-out infinite;}
    .orb.o2{width:520px;height:520px;top:280px;right:-200px;animation:drift2 30s ease-in-out infinite;opacity:.10;background:radial-gradient(circle,var(--white-accent),transparent 70%);}
    .orb.o3{width:460px;height:460px;bottom:-220px;left:30%;animation:drift3 34s ease-in-out infinite;opacity:.13;}
    .aurora{
      position:absolute; top:-30%; left:50%; width:1400px; height:1400px; margin-left:-700px;
      background:conic-gradient(from 0deg, transparent 0deg, rgba(245,246,248,.06) 40deg, transparent 90deg, transparent 220deg, rgba(245,246,248,.05) 270deg, transparent 320deg);
      animation:rotateAurora 60s linear infinite; filter:blur(20px); opacity:.8;
    }
    @keyframes rotateAurora{from{transform:rotate(0deg);}to{transform:rotate(360deg);}}
    @keyframes drift1{0%,100%{margin:0;}50%{margin:70px 0 0 60px;}}
    @keyframes drift2{0%,100%{margin:0;}50%{margin:50px 0 0 -70px;}}
    @keyframes drift3{0%,100%{margin:0;}50%{margin:-50px 0 0 50px;}}
    .grain{
      position:fixed; inset:0; z-index:1; pointer-events:none; opacity:.035; mix-blend-mode:overlay;
      background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    }

    header{
      position:sticky; top:0; z-index:90;
      display:flex; align-items:center; justify-content:space-between; gap:20px;
      padding:14px 4vw;
      background:rgba(7,8,10,.78);
      backdrop-filter:blur(18px) saturate(140%);
      border-bottom:1px solid var(--border-soft);
      transition:padding .3s ease, background .3s ease;
    }
    header.scrolled{padding:10px 4vw; background:rgba(7,8,10,.94);}
    .logo{display:flex; align-items:center; gap:10px; flex-shrink:0; cursor:pointer;}
    .logo-text-wrap{display:flex;flex-direction:column;line-height:1;}
    .logo-main{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:2px;color:var(--text);}
    .logo-sub{font-family:'Bebas Neue',sans-serif;font-size:12px;letter-spacing:5px;color:var(--accent);margin-top:2px;}

    .right-group{display:flex; align-items:center; gap:22px; flex-shrink:0;}

    .topnav{display:flex;align-items:center;gap:1.6vw;flex-shrink:0;}
    .topnav a{
      color:var(--dim); text-decoration:none; font-weight:500; font-size:13px;
      letter-spacing:.2px; position:relative; padding:8px 1px; white-space:nowrap; transition:color .25s;
    }
    .topnav a::after{content:"";position:absolute;left:0;bottom:2px;width:0;height:1px;background:var(--accent);transition:width .3s;}
    .topnav a:hover{color:var(--text);}
    .topnav a:hover::after{width:100%;}
    .topnav a.active{color:var(--text);}
    .topnav a.active::after{width:100%;}

    .icon-btn{
      width:38px;height:38px;border-radius:9px; display:flex; align-items:center; justify-content:center;
      border:1px solid var(--border); background:var(--surface); color:var(--dim); cursor:pointer;
      transition:.25s; flex-shrink:0; position:relative;
    }
    .icon-btn:hover{border-color:var(--accent); color:var(--accent);}

    .mobile-only{display:none;}

    /* Mobile Navigation Panel (same as index) */
    .mobile-nav-panel{
      position:fixed; top:62px; left:0; right:0; z-index:9999;
      background:rgba(12,14,18,.985); border-bottom:1px solid var(--border);
      padding:0 4vw; display:block; max-height:0; overflow:hidden;
      opacity:0; visibility:hidden; pointer-events:none; transform:translateY(-8px);
      transition:max-height .28s ease, opacity .2s ease, transform .28s ease, visibility .2s;
      box-shadow:0 22px 50px rgba(0,0,0,.55);
    }
    .mobile-nav-panel.open{
      max-height:calc(100vh - 62px); max-height:calc(100dvh - 62px);
      overflow-y:auto; -webkit-overflow-scrolling:touch; padding:14px 4vw 16px;
      opacity:1; visibility:visible; pointer-events:auto; transform:translateY(0);
    }
    .mobile-nav-panel a{
      display:flex !important; width:100% !important; min-height:48px; margin:0 0 9px 0 !important;
      align-items:center; justify-content:flex-start; text-align:left; font-size:14px;
      font-weight:600; color:var(--dim); text-decoration:none; background:var(--surface);
      border:1px solid var(--border-soft); border-radius:10px; padding:14px 16px; transition:.2s ease;
    }
    .mobile-nav-panel a:last-child{margin-bottom:0 !important;}
    .mobile-nav-panel a:hover, .mobile-nav-panel a.active{color:var(--text); border-color:var(--accent);}
    .mobile-nav-label{
      font-size:10px; font-weight:700; letter-spacing:1.8px; text-transform:uppercase;
      color:var(--dim-2); padding:2px 2px 9px;
    }
    .mobile-nav-divider{height:1px; background:var(--border-soft); margin:4px 0 13px;}

    main{position:relative;z-index:2;padding:20px 4vw 60px; max-width:1440px; margin:0 auto;}
    .page-header{
      display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap;
      padding:16px 0 28px; border-bottom:1px solid var(--border-soft); margin-bottom:32px;
    }
    .page-header h1{
      font-family:'Bebas Neue',sans-serif; font-weight:400; font-size:40px; letter-spacing:1px;
      color:var(--text);
    }
    .page-header .count-badge{
      font-size:13px; color:var(--dim); background:var(--surface); border:1px solid var(--border);
      border-radius:40px; padding:6px 18px;
    }

    .back-link{
      display:inline-flex; align-items:center; gap:8px; color:var(--dim);
      text-decoration:none; font-size:13px; font-weight:600; margin-bottom:24px;
      border:1px solid var(--border); padding:8px 16px; border-radius:40px;
      transition:.2s; background:var(--surface);
    }
    .back-link:hover{color:var(--text); border-color:var(--accent); background:var(--surface-2);}

    /* ---------- Translator Grid (Movie Card Style) ---------- */
    .translator-grid{
      display:grid; grid-template-columns:repeat(auto-fill, minmax(180px,1fr));
      gap:16px;
    }

    .translator-card{
      position:relative;
      border-radius:10px;
      overflow:hidden;
      cursor:pointer;
      background:var(--surface);
      border:1px solid var(--border-soft);
      opacity:0;
      transform:translateY(14px);
      animation:rise .3s cubic-bezier(0,0,0.2,1) forwards;
      transition:border-color .2s, box-shadow .2s, transform .2s;
      text-decoration:none;
      color:inherit;
      display:block;
    }
    .translator-card:hover{
      border-color:rgba(255,255,255,.18);
      box-shadow:0 18px 34px -14px rgba(0,0,0,.7);
      transform:translateY(-4px);
    }
    @keyframes rise{to{opacity:1;transform:translateY(0);}}

    .translator-poster{
      aspect-ratio:2/2.9;
      position:relative;
      overflow:hidden;
      background:#000;
    }
    .translator-poster-bg{
      position:absolute; inset:0;
      display:flex; align-items:center; justify-content:center;
      background:linear-gradient(150deg,#1a1d23,#0a0b0d);
      transition:opacity .2s ease;
    }
    .translator-poster-bg::before{
      content:""; position:absolute; inset:0;
      background-image:repeating-linear-gradient(120deg, rgba(255,255,255,.035) 0 2px, transparent 2px 28px);
    }
    .translator-poster-bg img{
      width:46%; height:auto; opacity:.55; filter:grayscale(.15);
    }
    .translator-poster.is-loaded .translator-poster-bg{opacity:0;}
    .translator-poster-img{
      position:absolute; inset:0; width:100%; height:100%; object-fit:cover;
      transition:transform .3s cubic-bezier(.2,.8,.2,1), opacity .2s ease;
      opacity:0;
    }
    .translator-poster-img.loaded{opacity:1;}
    .translator-card:hover .translator-poster-img{transform:scale(1.06);}
    .translator-poster::after{
      content:""; position:absolute; inset:0; z-index:2; pointer-events:none;
      background:linear-gradient(180deg, transparent 40%, rgba(0,0,0,.55) 72%, rgba(0,0,0,.92) 100%);
    }

    .translator-badge{
      position:absolute; top:8px; right:8px; z-index:3;
      font-size:8.5px; font-weight:700; text-transform:uppercase; letter-spacing:.6px;
      color:#fff; border:1px solid rgba(255,255,255,.2); background:#000;
      padding:3px 8px; border-radius:4px;
      backdrop-filter:blur(4px);
    }

    .translator-text{
      position:absolute; left:0; right:0; bottom:0; z-index:3;
      padding:10px 10px 12px;
    }
    .translator-text h3{
      font-size:12px; font-weight:700; line-height:1.3; color:#fff;
      margin-bottom:2px;
      display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
      word-break:break-word; text-shadow:0 2px 8px rgba(0,0,0,.6);
    }
    .translator-text .sub-line{
      font-size:10px; font-weight:600; color:var(--accent); display:block;
      text-shadow:0 1px 6px rgba(0,0,0,.5);
    }

    .empty-state{
      padding:60px 20px; text-align:center; color:var(--dim-2); grid-column:1/-1;
    }
    .empty-state .emoji{font-size:40px; display:block; margin-bottom:12px;}

    .loading-spinner{
      grid-column:1/-1; display:flex; justify-content:center; padding:60px 0;
    }
    .loading-spinner .spinner{
      width:40px; height:40px; border:3px solid var(--border); border-top-color:var(--accent);
      border-radius:50%; animation:spin .6s linear infinite;
    }
    @keyframes spin{to{transform:rotate(360deg);}}

    /* Mobile Responsive */
    @media(max-width:900px){
      header .topnav{display:none !important;}
      header{padding:11px 4vw; min-height:62px;}
      .right-group{display:flex; align-items:center; gap:8px; margin-left:auto;}
      header .mobile-only{display:flex !important; visibility:visible; opacity:1; pointer-events:auto;}
      #menuToggle{position:relative; z-index:140;}
      .logo-main{font-size:20px;}
      .logo-sub{font-size:11px;}
    }
    @media(min-width:901px){
      .mobile-nav-panel{display:none !important; visibility:hidden !important;}
    }

    @media(max-width:500px){
      .translator-grid{grid-template-columns:repeat(3,1fr); gap:10px;}
    }
    @media(max-width:400px){
      .translator-grid{grid-template-columns:repeat(2,1fr); gap:8px;}
    }
  </style>
</head>
<body>

<!-- ambient background -->
<div class="bg-field">
  <canvas class="bg-canvas" id="bgCanvas"></canvas>
  <div class="aurora"></div>
  <div class="orb o1"></div>
  <div class="orb o2"></div>
  <div class="orb o3"></div>
</div>
<div class="grain"></div>

<script>
  // Background particle system (same as index)
  (function(){
    const canvas = document.getElementById('bgCanvas');
    const ctx = canvas.getContext('2d');
    let w, h, dpr;
    let particles = [];
    const MAX_DIST = 130;
    const MOUSE_RADIUS = 160;
    const mouse = { x: -9999, y: -9999 };

    function resize(){
      dpr = Math.min(window.devicePixelRatio || 1, 2);
      w = canvas.offsetWidth = window.innerWidth;
      h = canvas.offsetHeight = window.innerHeight;
      canvas.width = w * dpr;
      canvas.height = h * dpr;
      ctx.setTransform(dpr,0,0,dpr,0,0);
      const count = Math.min(90, Math.round((w * h) / 16000));
      particles = Array.from({length: count}, () => ({
        x: Math.random() * w,
        y: Math.random() * h,
        vx: (Math.random() - 0.5) * 0.28,
        vy: (Math.random() - 0.5) * 0.28,
        r: Math.random() * 1.6 + 0.6
      }));
    }

    function step(){
      ctx.clearRect(0, 0, w, h);
      for (const p of particles){
        p.x += p.vx; p.y += p.vy;
        if (p.x < 0 || p.x > w) p.vx *= -1;
        if (p.y < 0 || p.y > h) p.vy *= -1;
        const dxm = p.x - mouse.x, dym = p.y - mouse.y;
        const dm = Math.sqrt(dxm*dxm + dym*dym);
        if (dm < MOUSE_RADIUS){
          const f = (1 - dm / MOUSE_RADIUS) * 0.6;
          p.x += (dxm / (dm || 1)) * f;
          p.y += (dym / (dm || 1)) * f;
        }
      }
      for (let i = 0; i < particles.length; i++){
        const a = particles[i];
        ctx.beginPath();
        ctx.arc(a.x, a.y, a.r, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(245,246,248,.5)';
        ctx.fill();
        for (let j = i + 1; j < particles.length; j++){
          const b = particles[j];
          const dx = a.x - b.x, dy = a.y - b.y;
          const dist = Math.sqrt(dx*dx + dy*dy);
          if (dist < MAX_DIST){
            ctx.beginPath();
            ctx.moveTo(a.x, a.y);
            ctx.lineTo(b.x, b.y);
            ctx.strokeStyle = `rgba(245,246,248,${(1 - dist / MAX_DIST) * 0.14})`;
            ctx.lineWidth = 1;
            ctx.stroke();
          }
        }
      }
      requestAnimationFrame(step);
    }

    window.addEventListener('resize', resize, { passive: true });
    window.addEventListener('mousemove', (e) => { mouse.x = e.clientX; mouse.y = e.clientY; }, { passive: true });
    window.addEventListener('mouseleave', () => { mouse.x = -9999; mouse.y = -9999; });
    window.addEventListener('touchmove', (e) => {
      if (e.touches && e.touches[0]) { mouse.x = e.touches[0].clientX; mouse.y = e.touches[0].clientY; }
    }, { passive: true });

    resize();
    requestAnimationFrame(step);

    const orbEls = document.querySelectorAll('.orb');
    window.addEventListener('mousemove', (e) => {
      const px = (e.clientX / window.innerWidth) - 0.5;
      const py = (e.clientY / window.innerHeight) - 0.5;
      orbEls.forEach((el, i) => {
        const depth = (i + 1) * 10;
        el.style.setProperty('--parallax-x', `${px * depth}px`);
        el.style.setProperty('--parallax-y', `${py * depth}px`);
      });
    }, { passive: true });
  })();
</script>

<header id="siteHeader">
  <div class="logo" id="homeLogoBtn">
    <div class="logo-text-wrap">
      <span class="logo-main">AGASOBANUYE</span>
      <span class="logo-sub">TV</span>
    </div>
  </div>

  <div class="right-group">
    <nav class="topnav">
      <a href="../">Home</a>
      <a href="./" class="active">Abasobanuzi</a>
      <a href="#">Rwanda</a>
    </nav>

    <button type="button" class="icon-btn mobile-only" id="menuToggle" aria-label="Open menu" aria-expanded="false">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 7h16" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/><path d="M4 15h10" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
    </button>
  </div>
</header>

<!-- Mobile Navigation Panel (same as index) -->
<div class="mobile-nav-panel" id="mobileNavPanel">
  <div class="mobile-nav-label">Navigation</div>
  <a href="../">Home</a>
  <a href="./" class="active">Abasobanuzi</a>
  <a href="#">Rwanda</a>
</div>

<main>
  <a href="../" class="back-link">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Back to Browse
  </a>

  <div class="page-header">
    <h1>Abasobanuzi</h1>
    <span class="count-badge" id="translatorCount">loading…</span>
  </div>

  <div class="translator-grid" id="translatorGrid">
    <div class="loading-spinner"><div class="spinner"></div></div>
  </div>
</main>

<script>
  // Mobile menu toggle (same as index)
  const menuToggle = document.getElementById('menuToggle');
  const mobileNavPanel = document.getElementById('mobileNavPanel');

  menuToggle.addEventListener('click', () => {
    const isOpen = mobileNavPanel.classList.toggle('open');
    menuToggle.setAttribute('aria-expanded', isOpen);
  });

  // Close mobile menu when a link is clicked
  mobileNavPanel.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      mobileNavPanel.classList.remove('open');
      menuToggle.setAttribute('aria-expanded', 'false');
    });
  });

  // Close mobile menu when clicking outside
  document.addEventListener('click', (e) => {
    if (!e.target.closest('header') && !e.target.closest('.mobile-nav-panel')) {
      mobileNavPanel.classList.remove('open');
      menuToggle.setAttribute('aria-expanded', 'false');
    }
  });

  // Scroll header effect
  const header = document.getElementById('siteHeader');
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 30);
  });

  // Load translators
  (async function(){
    const grid = document.getElementById('translatorGrid');
    const countEl = document.getElementById('translatorCount');

    try {
      const [abasobanuziRes, moviesRes] = await Promise.all([
        fetch('../portal/assets/json/abasobanuzi.json'),
        fetch('../portal/assets/json/movies-details.json')
      ]);

      if (!abasobanuziRes.ok) throw new Error('Failed to load abasobanuzi.json');
      if (!moviesRes.ok) throw new Error('Failed to load movies-details.json');

      const abasobanuziList = await abasobanuziRes.json();
      const movies = await moviesRes.json();

      const countMap = {};
      movies.forEach(m => {
        const uid = m.umusobanuzi_id || m.abasobanuzi_id || m.translator_id;
        if (uid) {
          countMap[uid] = (countMap[uid] || 0) + 1;
        }
      });

      const list = Array.isArray(abasobanuziList) ? abasobanuziList : [];
      list.sort((a,b) => (a.name || '').localeCompare(b.name || ''));

      countEl.textContent = `${list.length} translator${list.length !== 1 ? 's' : ''}`;

      if (list.length === 0) {
        grid.innerHTML = `<div class="empty-state"><span class="emoji">📭</span>No translators found.</div>`;
        return;
      }

      grid.innerHTML = '';
      list.forEach((translator, index) => {
        const id = translator.id;
        const name = translator.name || 'Unnamed';
        const count = countMap[id] || 0;
        // Use umusobanuzi.png as fallback if poster-url is missing or empty
        const poster = (translator['poster-url'] && translator['poster-url'].trim() !== '')
          ? translator['poster-url']
          : '../assets/img/umusobanuzi.png';

        const card = document.createElement('a');
        card.className = 'translator-card';
        card.href = `../?umusobanuzi=${encodeURIComponent(id)}`;
        card.style.animationDelay = `${Math.min(index * 25, 400)}ms`;

        card.innerHTML = `
          <div class="translator-poster">
            <div class="translator-poster-bg">
              <img src="../assets/img/umusobanuzi.png" alt="" loading="lazy">
            </div>
            <img class="translator-poster-img" src="${poster}" alt="${name}" loading="lazy"
                 onload="this.classList.add('loaded');this.closest('.translator-poster').classList.add('is-loaded');"
                 onerror="this.onerror=null;this.src='../assets/img/umusobanuzi.png';this.classList.add('loaded');this.closest('.translator-poster').classList.add('is-loaded');">
            <span class="translator-badge">${count} film${count !== 1 ? 's' : ''}</span>
            <div class="translator-text">
              <h3>${name}</h3>
              <span class="sub-line">Umusobanuzi</span>
            </div>
          </div>
        `;
        grid.appendChild(card);
      });

    } catch (err) {
      console.error('Abasobanuzi page error:', err);
      grid.innerHTML = `
        <div class="empty-state">
          <span class="emoji">⚠️</span>
          Could not load translators.<br>
          <span style="font-size:12px;color:var(--dim-2);margin-top:6px;display:block;">${err.message}</span>
        </div>
      `;
      countEl.textContent = 'error';
    }
  })();

  // Home logo click
  document.getElementById('homeLogoBtn').addEventListener('click', () => {
    window.location.href = '../';
  });
</script>
</body>
</html>