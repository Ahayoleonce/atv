// Security helper: Prevent XSS by escaping unsafe dynamic text injected into HTML templates
function escapeHTML(str) {
  if (!str) return '';
  return str.toString()
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

// Background Canvas and Particle FX
(function(){
  const canvas = document.getElementById('bgCanvas');
  if (!canvas) return;
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

// Main App Script
document.addEventListener("DOMContentLoaded", async () => {
  let allMovies = [];
  let allPartsEpisodes = [];
  let abasobanuziMap = {};
  let genreMap = {};

  const genreNavContainer = document.getElementById('genreNavContainer');
  const genreDropdownTrigger = document.getElementById('genreDropdownTrigger');
  const genreDropdownPanel = document.getElementById('genreDropdownPanel');
  const menuToggle = document.getElementById('menuToggle');
  const mobileNavPanel = document.getElementById('mobileNavPanel');

  const goToIndex = () => {
    window.location.href = window.location.pathname;
  };
  document.getElementById('homeLogoBtn').addEventListener('click', goToIndex);
  document.getElementById('footerLogoBtn').addEventListener('click', goToIndex);

  genreDropdownTrigger.addEventListener('click', (e) => {
    e.stopPropagation();
    genreNavContainer.classList.toggle('open');
    genreDropdownPanel.classList.toggle('open');
  });

  document.addEventListener('click', (e) => {
    if (!genreNavContainer.contains(e.target)) {
      genreNavContainer.classList.remove('open');
      genreDropdownPanel.classList.remove('open');
    }
  });

  const mobileSearchToggle = document.getElementById('mobileSearchToggle');
  const mobileSearchOverlay = document.getElementById('mobileSearchOverlay');
  const mobileSearchClose = document.getElementById('mobileSearchClose');
  const mobileSearchInput = document.getElementById('mobileSearchInput');
  const mobileSuggestPanel = document.getElementById('mobileSuggestPanel');

  menuToggle.addEventListener('click', () => {
    const isOpen = mobileNavPanel.classList.toggle('open');
    menuToggle.setAttribute('aria-expanded', isOpen);
    if (isOpen) {
      mobileSearchOverlay.classList.remove('open');
      mobileSuggestPanel.classList.remove('open');
      mobileSearchInput.value = '';
    }
  });

  mobileSearchToggle.addEventListener('click', () => {
    mobileNavPanel.classList.remove('open');
    menuToggle.setAttribute('aria-expanded', 'false');
    mobileSearchOverlay.classList.add('open');
    mobileSearchInput.focus();
  });
  mobileSearchClose.addEventListener('click', () => {
    mobileSearchOverlay.classList.remove('open');
    mobileSuggestPanel.classList.remove('open');
    mobileSearchInput.value = '';
  });

  try {
    const [moviesRes, partsRes, abasobanuziRes, genreRes] = await Promise.all([
      fetch('portal/assets/json/movies-details.json'),
      fetch('portal/assets/json/parts-epsode.json').catch(() => null),
      fetch('portal/assets/json/abasobanuzi.json').catch(() => null),
      fetch('portal/assets/json/genre.json').catch(() => null)
    ]);

    if (!moviesRes.ok) throw new Error('Failed to load movies JSON');
    allMovies = await moviesRes.json();
    allMovies.forEach((m, i) => {
      m.__uid = (m.id ?? m.ID ?? m.movie_id ?? m.Id ?? m._id ?? (slugify(m.Name) + '-' + i)).toString();
    });

    if (partsRes && partsRes.ok) {
      allPartsEpisodes = await partsRes.json();
    }

    if (abasobanuziRes && abasobanuziRes.ok) {
      const abasobanuziList = await abasobanuziRes.json();
      abasobanuziList.forEach(a => {
        abasobanuziMap[a.id] = { id: a.id, name: a.name, poster: a['poster-url'] || a.poster_url || a.poster || '' };
      });
    }

    if (genreRes && genreRes.ok) {
      const genreList = await genreRes.json();
      genreList.forEach(g => {
        genreMap[g.id] = g.name;
      });
    }

    allMovies.sort((a, b) => {
      const dateA = new Date(a['posteda-at'] || 0);
      const dateB = new Date(b['posteda-at'] || 0);
      return dateB - dateA;
    });

    const genreCounts = {};
    allMovies.forEach(m => {
      const genreId = m.Genre_id || m.genre_id;
      const genre = genreMap[genreId] || m.genre || m.category ? (genreMap[genreId] || m.genre || m.category).trim() : 'Uncategorized';
      genreCounts[genre] = (genreCounts[genre] || 0) + 1;
    });

    const sortedGenres = Object.keys(genreCounts).sort();
    const topGenresByCount = Object.keys(genreCounts)
      .sort((a, b) => genreCounts[b] - genreCounts[a])
      .slice(0, 3);

    const epsCount = allMovies.filter(m => (m.category || '').toLowerCase().trim() === 'epsodes').length;

    const genreListContainer = document.getElementById('genreListContainer');
    const mobileGenreListContainer = document.getElementById('mobileGenreListContainer');
    const footerGenreListContainer = document.getElementById('footerGenreListContainer');
    
    genreListContainer.innerHTML = '';
    mobileGenreListContainer.innerHTML = '';
    footerGenreListContainer.innerHTML = '';

    if (epsCount > 0) {
      const dEps = document.createElement('a');
      dEps.href = '#';
      dEps.innerHTML = `
        <svg class="cat-icon" viewBox="0 0 24 24" fill="none"><path d="M4 4h16v12H4z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 20h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        <span>Seasons</span>
      `;
      dEps.addEventListener('click', (e) => {
        e.preventDefault();
        closeMovieDetail(false);
        filterByCategory('Epsodes', 'Seasons');
        genreNavContainer.classList.remove('open');
        genreDropdownPanel.classList.remove('open');
      });
      genreListContainer.appendChild(dEps);

      const mEps = document.createElement('a');
      mEps.href = '#';
      mEps.textContent = `Seasons`;
      mEps.addEventListener('click', (e) => {
        e.preventDefault();
        closeMovieDetail(false);
        filterByCategory('Epsodes', 'Seasons');
        mobileNavPanel.classList.remove('open');
        menuToggle.setAttribute('aria-expanded', 'false');
      });
      mobileGenreListContainer.appendChild(mEps);
    }

    sortedGenres.forEach(genre => {
      const dItem = document.createElement('a');
      dItem.href = '#';
      dItem.innerHTML = `
        <svg class="cat-icon" viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        <span>${escapeHTML(genre)}</span>
      `;
      dItem.addEventListener('click', (e) => {
        e.preventDefault();
        closeMovieDetail(false);
        filterByGenre(genre);
        genreNavContainer.classList.remove('open');
        genreDropdownPanel.classList.remove('open');
      });
      genreListContainer.appendChild(dItem);

      const mItem = document.createElement('a');
      mItem.href = '#';
      mItem.textContent = genre;
      mItem.addEventListener('click', (e) => {
        e.preventDefault();
        closeMovieDetail(false);
        filterByGenre(genre);
        mobileNavPanel.classList.remove('open');
        menuToggle.setAttribute('aria-expanded', 'false');
      });
      mobileGenreListContainer.appendChild(mItem);
    });

    topGenresByCount.forEach(genre => {
      const fItem = document.createElement('a');
      fItem.href = '#';
      fItem.textContent = genre;
      fItem.addEventListener('click', (e) => {
        e.preventDefault();
        closeMovieDetail(false);
        filterByGenre(genre);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
      footerGenreListContainer.appendChild(fItem);
    });

    renderGrid(allMovies, 'latestMoviesGrid');

    const initialParams = new URLSearchParams(window.location.search);
    const initialUid = initialParams.get('t');
    const initialEpId = initialParams.get('ep');
    const initialUmusobanuziId = initialParams.get('umusobanuzi');

    if (initialUid) {
      const initialMovie = findMovieByUid(initialUid);
      if (initialMovie) {
        let targetEp = null;
        if (initialEpId) {
          targetEp = getEpisodesForMovie(initialMovie).find(ep => ep.id === initialEpId);
        }
        history.replaceState({ movieUid: initialMovie.__uid, epId: initialEpId }, '', buildMovieUrl(initialMovie, initialEpId));
        openMovieDetail(initialMovie, false, targetEp);
      }
    } else if (initialUmusobanuziId && abasobanuziMap[initialUmusobanuziId]) {
      filterByUmusobanuzi(initialUmusobanuziId);
    }
  } catch (error) {
    console.error('Error fetching data:', error);
    document.getElementById('latestMoviesGrid').innerHTML = '<p style="padding:0 4vw; color:var(--dim);">Unable to load movies at this moment.</p>';
  }

  function renderGrid(moviesArray, gridId) {
    const grid = document.getElementById(gridId);
    grid.innerHTML = '';
    if (moviesArray.length === 0) {
      grid.innerHTML = '<p style="padding:0 4vw; color:var(--dim);">No movies found matching this filter.</p>';
      return;
    }

    moviesArray.forEach(movie => {
      const title = escapeHTML(movie.Name || 'Untitled');
      const genreId = movie.Genre_id || movie.genre_id;
      const genre = escapeHTML(genreMap[genreId] || movie.genre || 'Action');
      const category = escapeHTML(movie.category || 'Film');
      const abasobanuziId = movie.umusobanuzi_id || movie.abasobanuzi_id || movie.translator_id;
      const abasobanuziEntry = abasobanuziMap[abasobanuziId];
      const abasobanuziName = escapeHTML((abasobanuziEntry && abasobanuziEntry.name) || movie.abasobanuzi || movie.translator || 'Unknown');
      const umusobanuziAvatarUrl = escapeHTML((abasobanuziEntry && abasobanuziEntry.poster && abasobanuziEntry.poster.trim() !== '') 
        ? abasobanuziEntry.poster 
        : 'assets/img/umusobanuzi.png');
      
      const posterUrl = escapeHTML((movie['poster-url'] && movie['poster-url'].trim() !== '') 
        ? movie['poster-url'] 
        : 'assets/img/agasobanuye.png');
      const movieUrl = buildMovieUrl(movie);

      const card = document.createElement('div');
      card.className = 'card';
      card.innerHTML = `
        <a href="${movieUrl}" class="card-link" style="text-decoration:none; color:inherit; display:block;">
          <div class="poster">
            <div class="poster-bg"><img class="poster-placeholder-img" src="assets/img/agasobanuye.png" alt="" loading="lazy"></div>
            <img class="poster-img" src="${posterUrl}" alt="${title}" loading="lazy"
                 onload="this.classList.add('loaded');this.closest('.poster').classList.add('is-loaded');" 
                 onerror="this.onerror=null;this.src='assets/img/agasobanuye.png';this.classList.add('loaded');this.closest('.poster').classList.add('is-loaded');">
            
            <img class="card-top-left-logo" src="assets/agasobanuye.svg" alt="Agasobanuye" />
            <span class="tag">${genre}</span>
            <div class="card-umusobanuzi-avatar" title="${abasobanuziName}">
              <img src="${umusobanuziAvatarUrl}" alt="${abasobanuziName}" onerror="this.onerror=null;this.src='assets/img/umusobanuzi.png';">
            </div>
            <div class="play-btn">
              <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
            <div class="poster-text">
              <h3>${title}</h3>
              <span class="sub-line1">${category} &bull; ${abasobanuziName}</span>
            </div>
          </div>
        </a>
      `;
      card.querySelector('.card-link').addEventListener('click', (e) => {
        e.preventDefault();
        openMovieDetail(movie, true);
      });
      grid.appendChild(card);
    });
  }

  function filterByGenre(genreName) {
    closeMovieDetail(false);
    const filtered = allMovies.filter(m => {
      const genreId = m.Genre_id || m.genre_id;
      const genre = genreMap[genreId] || m.genre || m.category ? (genreMap[genreId] || m.genre || m.category).trim() : 'Uncategorized';
      return genre === genreName;
    });
    document.getElementById('filteredRowTitle').textContent = `Genre: ${genreName}`;
    renderGrid(filtered, 'filteredGrid');
    
    document.getElementById('rowFiltered').style.display = 'block';
    document.getElementById('rowLatestMovies').style.display = 'none';
    document.getElementById('profileHeaderContainer').innerHTML = '';

    const filterBanner = document.getElementById('filterBanner');
    const filterLabel = document.getElementById('filterLabel');
    filterLabel.innerHTML = `Showing titles for genre: <strong>${escapeHTML(genreName)}</strong> (${filtered.length} results)`;
    filterBanner.style.display = 'flex';
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function filterByCategory(categoryKeyword, label) {
    closeMovieDetail(false);
    const filtered = allMovies.filter(m => (m.category || '').toLowerCase().trim() === categoryKeyword.toLowerCase());
    document.getElementById('filteredRowTitle').textContent = label;
    renderGrid(filtered, 'filteredGrid');

    document.getElementById('rowFiltered').style.display = 'block';
    document.getElementById('rowLatestMovies').style.display = 'none';
    document.getElementById('profileHeaderContainer').innerHTML = '';

    const filterBanner = document.getElementById('filterBanner');
    const filterLabel = document.getElementById('filterLabel');
    filterLabel.innerHTML = `Showing titles for: <strong>${escapeHTML(label)}</strong> (${filtered.length} results)`;
    filterBanner.style.display = 'flex';
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function filterByUmusobanuzi(umusobanuziId, pushState = false) {
    closeMovieDetail(false);
    const umusobanuzi = abasobanuziMap[umusobanuziId];
    if (!umusobanuzi) return;

    const filtered = allMovies.filter(m => {
      const aId = m.umusobanuzi_id || m.abasobanuzi_id || m.translator_id;
      return aId === umusobanuziId;
    });

    const profileContainer = document.getElementById('profileHeaderContainer');
    const avatarUrl = escapeHTML((umusobanuzi.poster && umusobanuzi.poster.trim() !== '') ? umusobanuzi.poster : 'assets/img/umusobanuzi.png');
    
    profileContainer.innerHTML = `
      <div class="profile-header-card">
        <div class="profile-header-avatar">
          <img src="${avatarUrl}" alt="${escapeHTML(umusobanuzi.name)}" onerror="this.onerror=null;this.src='assets/img/umusobanuzi.png';">
        </div>
        <div class="profile-header-info">
          <h2>${escapeHTML(umusobanuzi.name)}</h2>
          <p>${filtered.length} translated title${filtered.length === 1 ? '' : 's'} available</p>
        </div>
      </div>
    `;

    document.getElementById('filteredRowTitle').textContent = `Movies translated by ${umusobanuzi.name}`;
    renderGrid(filtered, 'filteredGrid');

    document.getElementById('rowFiltered').style.display = 'block';
    document.getElementById('rowLatestMovies').style.display = 'none';

    const filterBanner = document.getElementById('filterBanner');
    const filterLabel = document.getElementById('filterLabel');
    filterLabel.innerHTML = `Showing translator: <strong>${escapeHTML(umusobanuzi.name)}</strong> (${filtered.length} results)`;
    filterBanner.style.display = 'flex';

    if (pushState) {
      history.pushState({ umusobanuziId: umusobanuziId }, '', `?umusobanuzi=${encodeURIComponent(umusobanuziId)}`);
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  document.getElementById('clearFilter').addEventListener('click', () => {
    document.getElementById('rowFiltered').style.display = 'none';
    document.getElementById('rowLatestMovies').style.display = 'block';
    document.getElementById('filterBanner').style.display = 'none';
    document.getElementById('profileHeaderContainer').innerHTML = '';
    history.pushState({}, '', window.location.pathname);
  });

  function movieMeta(m) {
    const genreId = m.Genre_id || m.genre_id;
    const genre = genreMap[genreId] || m.genre || m.category || 'Uncategorized';
    const category = m.category || 'Film';
    const abasobanuziId = m.umusobanuzi_id || m.abasobanuzi_id || m.translator_id;
    const abasobanuziEntry = abasobanuziMap[abasobanuziId];
    const translator = (abasobanuziEntry && abasobanuziEntry.name) || m.abasobanuzi || m.umusobanuzi || m.translator || '';
    const translatorId = abasobanuziEntry ? abasobanuziEntry.id : '';
    const translatorPoster = (abasobanuziEntry && abasobanuziEntry.poster) || '';
    return { genre, category, translator, translatorId, translatorPoster };
  }

  function slugify(str) {
    return (str || '').toString().toLowerCase().trim()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/(^-|-$)/g, '') || 'title';
  }

  function findMovieByUid(uid) {
    return allMovies.find(m => m.__uid === uid);
  }

  function buildMovieUrl(m, epId) {
    let url = `?movie=${slugify(m.Name)}&t=${encodeURIComponent(m.__uid)}`;
    if (epId) url += `&ep=${encodeURIComponent(epId)}`;
    return url;
  }

  function getYouTubeId(url) {
    if (!url) return null;
    const patterns = [/youtu\.be\/([^?&/]+)/, /[?&]v=([^?&]+)/, /embed\/([^?&/]+)/, /shorts\/([^?&/]+)/];
    for (const p of patterns) {
      const match = url.match(p);
      if (match) return match[1];
    }
    return null;
  }

  function getEpisodesForMovie(m) {
    return allPartsEpisodes.filter(part => part.movie_id === m.id || part.movie_id === m.__uid);
  }

  function renderDetailVideoFrame(m, videoUrlOverride) {
    const frame = document.getElementById('detailVideoFrame');
    const targetUrl = videoUrlOverride || m['trailer-url'] || m['trailer_url'] || m.trailer || '';
    const ytId = getYouTubeId(targetUrl);
    const posterUrl = escapeHTML((m['poster-url'] && m['poster-url'].trim() !== '') ? m['poster-url'] : 'assets/img/agasobanuye.png');

    if (ytId) {
      const thumb = `https://img.youtube.com/vi/${ytId}/hqdefault.jpg`;
      frame.innerHTML = `
        <div class="no-trailer" style="background-image:url('${thumb}'), url('${posterUrl}');">
          <div class="no-trailer-btn yt-facade" id="playTrailerBtn">
            <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
          </div>
        </div>
      `;
      const playBtn = document.getElementById('playTrailerBtn');
      if (playBtn) {
        playBtn.addEventListener('click', () => {
          frame.innerHTML = `
            <div class="video-loading-overlay" id="videoLoadingOverlay">
              <div class="video-loading-spinner"></div>
              <div class="video-loading-text">Loading trailer...</div>
            </div>
            <iframe id="activeYouTubeIframe" src="https://www.youtube.com/embed/${ytId}?rel=0&autoplay=1" title="${escapeHTML(m.Name || 'Video')}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          `;
          const iframeEl = document.getElementById('activeYouTubeIframe');
          const loaderEl = document.getElementById('videoLoadingOverlay');
          if (iframeEl && loaderEl) {
            iframeEl.addEventListener('load', () => {
              loaderEl.style.opacity = '0';
              setTimeout(() => loaderEl.remove(), 200);
            });
          }
        });
      }
    } else {
      frame.innerHTML = `
        <div class="no-trailer" style="background-image:url('${posterUrl}');">
          <div class="no-trailer-btn" id="noTrailerPlayBtn">
            <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
          </div>
          <span class="no-trailer-label">No trailer available</span>
        </div>
      `;
    }
  }

  function renderDetailSide(m, activeEpisodeId, onEpisodeSelect) {
    const layout = document.getElementById('detailLayout');
    const sideTitle = document.getElementById('detailSideTitle');
    const sideList = document.getElementById('detailSideList');
    sideList.innerHTML = '';

    const episodes = getEpisodesForMovie(m);
    const catLower = (m.category || '').toLowerCase().trim();
    
    let unitLabel = 'Part / Episode';
    let sectionHeading = 'Episodes / Parts';

    if (catLower.includes('epsode') || catLower.includes('episode')) {
      sectionHeading = 'Episodes';
      unitLabel = 'Episode';
    } else if (catLower.includes('part')) {
      sectionHeading = 'Parts';
      unitLabel = 'Part';
    }

    if (episodes.length > 0) {
      layout.classList.remove('no-side');
      sideTitle.textContent = `${sectionHeading} (${episodes.length})`;
      
      episodes.forEach(ep => {
        const epName = escapeHTML(ep.name || ep.Name || unitLabel);
        const epUrl = escapeHTML(ep['watch url'] || ep['watch-url'] || ep['Download url'] || '#');
        const isCurrent = ep.id === activeEpisodeId;
        const epThumb = escapeHTML(m['poster-url'] && m['poster-url'].trim() !== '' ? m['poster-url'] : 'assets/img/agasobanuye.png');

        const row = document.createElement('div');
        row.className = 'side-part-row' + (isCurrent ? ' is-current' : '');
        row.innerHTML = `
          <div class="side-thumb"><img src="${epThumb}" alt="" loading="lazy" onerror="this.onerror=null;this.src='assets/img/agasobanuye.png';"></div>
          <div class="side-part-text">
            <h4>${epName}</h4>
            <span>${unitLabel}</span>
          </div>
          <a class="side-download-btn" href="${epUrl}" target="_blank" rel="noopener" title="Open Stream" aria-label="Open Stream">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M12 3v13m0 0l-4.5-4.5M12 16l4.5-4.5M4 20h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
        `;
        row.addEventListener('click', (e) => {
          if (e.target.closest('.side-download-btn')) return;
          if (onEpisodeSelect) onEpisodeSelect(ep);
        });
        sideList.appendChild(row);
      });
    } else {
      layout.classList.add('no-side');
    }
  }

  function renderRelatedMovies(currentMovie) {
    const currentGenreId = currentMovie.Genre_id || currentMovie.genre_id;
    const relatedGrid = document.getElementById('relatedMoviesGrid');
    relatedGrid.innerHTML = '';

    const related = allMovies.filter(m => {
      const gId = m.Genre_id || m.genre_id;
      return gId === currentGenreId && m.__uid !== currentMovie.__uid;
    }).slice(0, 4);

    if (related.length === 0) {
      document.getElementById('relatedSection').style.display = 'none';
      return;
    } else {
      document.getElementById('relatedSection').style.display = '';
    }

    related.forEach(movie => {
      const title = escapeHTML(movie.Name || 'Untitled');
      const genreId = movie.Genre_id || movie.genre_id;
      const genre = escapeHTML(genreMap[genreId] || movie.genre || 'Action');
      const category = escapeHTML(movie.category || 'Film');
      const abasobanuziId = movie.umusobanuzi_id || movie.abasobanuzi_id || movie.translator_id;
      const abasobanuziEntry = abasobanuziMap[abasobanuziId];
      const abasobanuziName = escapeHTML((abasobanuziEntry && abasobanuziEntry.name) || movie.abasobanuzi || movie.translator || 'Unknown');
      const umusobanuziAvatarUrl = escapeHTML((abasobanuziEntry && abasobanuziEntry.poster && abasobanuziEntry.poster.trim() !== '') 
        ? abasobanuziEntry.poster 
        : 'assets/img/umusobanuzi.png');
      
      const posterUrl = escapeHTML((movie['poster-url'] && movie['poster-url'].trim() !== '') ? movie['poster-url'] : 'assets/img/agasobanuye.png');
      const movieUrl = buildMovieUrl(movie);

      const card = document.createElement('div');
      card.className = 'card';
      card.innerHTML = `
        <a href="${movieUrl}" class="card-link" style="text-decoration:none; color:inherit; display:block;">
          <div class="poster">
            <div class="poster-bg"><img class="poster-placeholder-img" src="assets/img/agasobanuye.png" alt="" loading="lazy"></div>
            <img class="poster-img" src="${posterUrl}" alt="${title}" loading="lazy"
                 onload="this.classList.add('loaded');this.closest('.poster').classList.add('is-loaded');" 
                 onerror="this.onerror=null;this.src='assets/img/agasobanuye.png';this.classList.add('loaded');this.closest('.poster').classList.add('is-loaded');">
            
            <img class="card-top-left-logo" src="assets/agasobanuye.svg" alt="Agasobanuye" />
            <span class="tag">${genre}</span>
            <div class="card-umusobanuzi-avatar" title="${abasobanuziName}">
              <img src="${umusobanuziAvatarUrl}" alt="${abasobanuziName}" onerror="this.onerror=null;this.src='assets/img/umusobanuzi.png';">
            </div>
            <div class="play-btn">
              <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
            <div class="poster-text">
              <h3>${title}</h3>
              <span class="sub-line1">${category} &bull; ${abasobanuziName}</span>
            </div>
          </div>
        </a>
      `;
      card.querySelector('.card-link').addEventListener('click', (e) => {
        e.preventDefault();
        openMovieDetail(movie, true);
      });
      relatedGrid.appendChild(card);
    });
  }

  function openMovieDetail(m, pushState, customEpisode) {
    if (!m) return;
    const { genre, category, translator, translatorId, translatorPoster } = movieMeta(m);

    const titleDisplay = escapeHTML(customEpisode ? `${m.Name} — ${customEpisode.name || customEpisode.Name}` : (m.Name || 'Untitled'));
    document.getElementById('detailTitletextContent') = titleDisplay;
    document.getElementById('detailTitle').textContent = customEpisode ? `${m.Name} — ${customEpisode.name || customEpisode.Name}` : (m.Name || 'Untitled');

    const uploaderWrap = document.getElementById('detailUploader');
    if (translator) {
      const avatarUrl = escapeHTML((translatorPoster && translatorPoster.trim() !== '') ? translatorPoster : 'assets/img/umusobanuzi.png');
      document.getElementById('uploaderAvatar').innerHTML =
        `<img src="${avatarUrl}" alt="${escapeHTML(translator)}" onerror="this.onerror=null;this.src='assets/img/umusobanuzi.png';">`;
      document.getElementById('uploaderName').innerHTML = `${escapeHTML(translator)}<span>Umusobanuzi</span>`;
      uploaderWrap.style.display = 'flex';
      
      uploaderWrap.onclick = () => {
        if (translatorId) {
          closeMovieDetail(false);
          filterByUmusobanuzi(translatorId, true);
        }
      };
    } else {
      uploaderWrap.style.display = 'none';
      uploaderWrap.onclick = null;
    }

    document.getElementById('detailMetaRow').innerHTML = `
      <span class="dpill">${escapeHTML(category)}</span>
      <span class="dpill">${escapeHTML(genre)}</span>
    `;
    const bio = m.bio || m.description || m.desc || m.synopsis || '';
    document.getElementById('detailDesc').textContent = bio;
    document.getElementById('detailDesc').style.display = bio ? 'block' : 'none';
    
    const watchUrl = escapeHTML(customEpisode ? (customEpisode['watch url'] || customEpisode['watch-url'] || m['watch-url']) : (m['watch-url'] || '#'));
    document.getElementById('detailWatchBtn').href = watchUrl;

    const downloadBtn = document.getElementById('detailDownloadBtn');
    const downloadUrl = escapeHTML(customEpisode ? (customEpisode['Download url'] || customEpisode['download-url']) : (m['download-url'] || m['watch-url'] || ''));
    if (downloadUrl && downloadUrl.trim() !== '') {
      downloadBtn.href = downloadUrl;
      downloadBtn.style.display = 'inline-flex';
    } else {
      downloadBtn.style.display = 'none';
    }

    renderDetailVideoFrame(m);
    renderDetailSide(m, customEpisode ? customEpisode.id : null, (selectedEp) => {
      openMovieDetail(m, true, selectedEp);
    });
    renderRelatedMovies(m);

    document.getElementById('movieDetailView').style.display = 'block';
    document.getElementById('filterBanner').style.display = 'none';
    document.getElementById('rowFiltered').style.display = 'none';
    document.getElementById('rowLatestMovies').style.display = 'none';
    document.getElementById('profileHeaderContainer').innerHTML = '';

    if (pushState) {
      history.pushState({ movieUid: m.__uid, epId: customEpisode ? customEpisode.id : null }, '', buildMovieUrl(m, customEpisode ? customEpisode.id : null));
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function closeMovieDetail(pushState) {
    document.getElementById('movieDetailView').style.display = 'none';
    document.getElementById('rowLatestMovies').style.display = 'block';
    if (pushState) {
      history.pushState({}, '', window.location.pathname);
    }
  }

  document.getElementById('detailBackBtn').addEventListener('click', () => {
    closeMovieDetail(true);
  });

  window.addEventListener('popstate', (e) => {
    const uid = e.state && e.state.movieUid;
    const epId = e.state && e.state.epId;
    const umusobanuziId = e.state && e.state.umusobanuziId;

    if (uid) {
      const m = findMovieByUid(uid);
      if (m) {
        let targetEp = null;
        if (epId) {
          targetEp = getEpisodesForMovie(m).find(ep => ep.id === epId);
        }
        openMovieDetail(m, false, targetEp);
        return;
      }
    } else if (umusobanuziId) {
      filterByUmusobanuzi(umusobanuziId, false);
      return;
    }
    closeMovieDetail(false);
    document.getElementById('rowFiltered').style.display = 'none';
    document.getElementById('rowLatestMovies').style.display = 'block';
    document.getElementById('filterBanner').style.display = 'none';
    document.getElementById('profileHeaderContainer').innerHTML = '';
  });

  function searchMovies(query) {
    const q = query.trim().toLowerCase();
    if (!q) return [];
    return allMovies.filter(m => {
      const mName = (m.Name || '').toLowerCase();
      const { genre, category, translator } = movieMeta(m);
      return mName.includes(q)
        || genre.toLowerCase().includes(q)
        || category.toLowerCase().includes(q)
        || translator.toLowerCase().includes(q);
    });
  }

  function buildSuggestItem(m) {
    const pUrl = escapeHTML((m['poster-url'] && m['poster-url'].trim() !== '') ? m['poster-url'] : 'assets/img/agasobanuye.png');
    const { genre, translator } = movieMeta(m);
    const item = document.createElement('div');
    item.className = 'suggest-item';
    item.innerHTML = `
      <div class="suggest-thumb"><img src="${pUrl}" alt="" loading="lazy" onload="this.classList.add('loaded')" onerror="this.onerror=null;this.src='assets/img/agasobanuye.png';this.classList.add('loaded');"></div>
      <div class="suggest-text">
        <h4>${escapeHTML(m.Name)}</h4>
        <span>${escapeHTML(genre)}${genre && translator ? ' • ' : ''}${escapeHTML(translator)}</span>
      </div>
    `;
    item.addEventListener('click', () => {
      openMovieDetail(m, true);
      document.getElementById('desktopSuggestPanel').classList.remove('open');
      document.getElementById('desktopSuggestPanel').innerHTML = '';
      document.getElementById('mobileSuggestPanel').classList.remove('open');
      document.getElementById('mobileSuggestPanel').innerHTML = '';
      document.getElementById('desktopSearchInput').value = '';
      document.getElementById('mobileSearchInput').value = '';
      document.getElementById('mobileSearchOverlay').classList.remove('open');
    });
    return item;
  }

  function renderSuggestPanel(panelEl, query, { closeAfterViewAll } = {}) {
    const matches = searchMovies(query);

    if (matches.length === 0) {
      panelEl.innerHTML = '<div style="padding:10px 12px; font-size:12px; color:var(--dim);">No matches found</div>';
      panelEl.classList.add('open');
      return;
    }

    const SHOWN = 6;
    panelEl.innerHTML = '';
    matches.slice(0, SHOWN).forEach(m => panelEl.appendChild(buildSuggestItem(m)));

    if (matches.length > SHOWN) {
      const viewAll = document.createElement('div');
      viewAll.className = 'suggest-item suggest-view-all';
      viewAll.innerHTML = `<div class="suggest-text" style="width:100%;text-align:center;"><h4 style="color:var(--accent);">View all ${matches.length} results →</h4></div>`;
      viewAll.addEventListener('click', () => {
        showSearchResults(query);
        panelEl.classList.remove('open');
        if (closeAfterViewAll) closeAfterViewAll();
      });
      panelEl.appendChild(viewAll);
    }

    panelEl.classList.add('open');
  }

  function showSearchResults(query) {
    closeMovieDetail(false);
    const q = query.trim();
    const matches = searchMovies(q);

    document.getElementById('filteredRowTitle').textContent = `Search results for "${q}"`;
    renderGrid(matches, 'filteredGrid');

    document.getElementById('rowFiltered').style.display = 'block';
    document.getElementById('rowLatestMovies').style.display = 'none';
    document.getElementById('profileHeaderContainer').innerHTML = '';

    const filterBanner = document.getElementById('filterBanner');
    const filterLabel = document.getElementById('filterLabel');
    filterLabel.innerHTML = `Search results for: <strong>"${escapeHTML(q)}"</strong> (${matches.length} results)`;
    filterBanner.style.display = 'flex';
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  const desktopSearchInput = document.getElementById('desktopSearchInput');
  const desktopSuggestPanel = document.getElementById('desktopSuggestPanel');

  desktopSearchInput.addEventListener('input', (e) => {
    const query = e.target.value;
    if (!query.trim()) {
      desktopSuggestPanel.classList.remove('open');
      desktopSuggestPanel.innerHTML = '';
      return;
    }
    renderSuggestPanel(desktopSuggestPanel, query);
  });

  desktopSearchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      const q = desktopSearchInput.value.trim();
      if (q) {
        showSearchResults(q);
        desktopSuggestPanel.classList.remove('open');
      }
    }
  });

  const mobileSearchInputEl = document.getElementById('mobileSearchInput');
  const mobileSuggestPanelEl = document.getElementById('mobileSuggestPanel');

  mobileSearchInputEl.addEventListener('input', (e) => {
    const query = e.target.value;
    if (!query.trim()) {
      mobileSuggestPanelEl.classList.remove('open');
      mobileSuggestPanelEl.innerHTML = '';
      return;
    }
    renderSuggestPanel(mobileSuggestPanelEl, query, {
      closeAfterViewAll: () => {
        document.getElementById('mobileSearchOverlay').classList.remove('open');
      }
    });
  });

  mobileSearchInputEl.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      const q = mobileSearchInputEl.value.trim();
      if (q) {
        showSearchResults(q);
        mobileSuggestPanelEl.classList.remove('open');
        document.getElementById('mobileSearchOverlay').classList.remove('open');
        mobileSearchInputEl.value = '';
      }
    }
  });
});