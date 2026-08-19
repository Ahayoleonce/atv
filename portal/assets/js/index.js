// Mobile Sidebar Toggle Handler
function toggleMobileSidebar() {
  const sidebar = document.getElementById('appSidebar');
  const overlay = document.getElementById('sidebarOverlay');
  sidebar.classList.toggle('mobile-open');
  overlay.classList.toggle('active');
}

// Tab Management State & Persistence across refreshes
const openTabs = new Set();

function initTabNavigationState() {
  const urlParams = new URLSearchParams(window.location.search);
  let tabParam = urlParams.get('tab');
  if (!tabParam) {
    tabParam = 'movies-tab'; // Default initial landing tab
  }
  const titles = {
    'movies-tab': 'Manage Movies',
    'parts-tab': 'Episodes & Parts',
    'genres-tab': 'Manage Genres',
    'abasobanuzi-tab': 'Abasobanuzi',
    'users-tab': 'System Users',
    'statistics-tab': 'Statistics'
  };
  if (titles[tabParam]) {
    openTabs.add(tabParam);
    renderTabStrip();
    switchTab(tabParam);
  }
}

function openTab(tabId, title, element) {
  openTabs.add(tabId);
  renderTabStrip();
  switchTab(tabId);
  if (window.innerWidth <= 768) {
    toggleMobileSidebar();
  }
}

function switchTab(tabId) {
  document.querySelectorAll('.tab-view-content').forEach(el => el.classList.remove('active'));
  document.querySelectorAll('.tab-button').forEach(el => el.classList.remove('active'));
  document.querySelectorAll('.sidebar-menu .menu-item').forEach(el => el.classList.remove('active'));
  document.getElementById('blank-screen').style.display = 'none';
  
  const targetView = document.getElementById(tabId);
  const targetBtn = document.getElementById('tab-btn-' + tabId);
  const sidebarItem = document.getElementById('sidebar-' + tabId);
  
  if (targetView) targetView.classList.add('active');
  if (targetBtn) targetBtn.classList.add('active');
  if (sidebarItem) sidebarItem.classList.add('active');

  document.getElementById('profile_active_tab').value = tabId;

  const url = new URL(window.location);
  url.searchParams.set('tab', tabId);
  window.history.replaceState({}, '', url);

  // Refresh charts on view switch if statistics tab is active
  if (tabId === 'statistics-tab' && window.myBarChart && window.myDoughnutChart && window.myLineChart) {
    window.myBarChart.update();
    window.myDoughnutChart.update();
    window.myLineChart.update();
  }
}

function closeTab(event, tabId) {
  event.stopPropagation();
  openTabs.delete(tabId);
  
  const btn = document.getElementById('tab-btn-' + tabId);
  if (btn) btn.remove();

  const remaining = Array.from(openTabs);
  if (remaining.length > 0) {
    switchTab(remaining[remaining.length - 1]);
  } else {
    document.querySelectorAll('.tab-view-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.sidebar-menu .menu-item').forEach(el => el.classList.remove('active'));
    document.getElementById('blank-screen').style.display = 'flex';
    const url = new URL(window.location);
    url.searchParams.delete('tab');
    window.history.replaceState({}, '', url);
  }
  renderTabStrip();
}

function renderTabStrip() {
  const strip = document.getElementById('tabStrip');
  const labels = {
    'movies-tab': 'Manage Movies',
    'parts-tab': 'Episodes & Parts',
    'genres-tab': 'Manage Genres',
    'abasobanuzi-tab': 'Abasobanuzi',
    'users-tab': 'System Users',
    'statistics-tab': 'Statistics'
  };
  const icons = {
    'movies-tab': 'video_library',
    'parts-tab': 'format_list_numbered',
    'genres-tab': 'label',
    'abasobanuzi-tab': 'mic',
    'users-tab': 'group',
    'statistics-tab': 'analytics'
  };

  let html = '';
  openTabs.forEach(id => {
    const isActive = document.getElementById(id).classList.contains('active') ? 'active' : '';
    html += `
      <div class="tab-button ${isActive}" id="tab-btn-${id}" onclick="switchTab('${id}')">
          <span class="material-symbols-outlined" style="font-size:16px;">${icons[id]}</span> <span>${labels[id]}</span>
          <span class="material-symbols-outlined tab-close" onclick="closeTab(event, '${id}')">close</span>
      </div>
    `;
  });
  strip.innerHTML = html;
}

// Initialize Chart.js Graphs with Custom Multi-colors (Blue, Red, Green, Orange, Purple)
function initStatisticsCharts() {
  const totalMovies = document.querySelectorAll('#tableMovies tbody tr').length;
  const totalParts = document.querySelectorAll('#tableParts tbody tr').length;
  const totalGenres = document.querySelectorAll('#tableGenres tbody tr').length;
  const totalAba = document.querySelectorAll('#tableAba tbody tr').length;
  const totalUsers = document.querySelectorAll('#tableUsers tbody tr').length;

  const chartLabels = ['Movies', 'Episodes/Parts', 'Genres', 'Abasobanuzi', 'System Users'];
  const chartDataValues = [totalMovies, totalParts, totalGenres, totalAba, totalUsers];
  
  // Distinct color palette for statistics charts


  // Bar Chart
  const ctxBar = document.getElementById('barChart').getContext('2d');
  window.myBarChart = new Chart(ctxBar, {
    type: 'bar',
    data: {
      labels: chartLabels,
      datasets: [{
        label: 'Count',
        data: chartDataValues,
        backgroundColor: chartColors,
        borderRadius: 4,
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        x: {
          grid: { color: 'rgba(255,255,255,0.05)' },
          ticks: { color: 'rgba(255,255,255,0.7)', font: { family: 'Plus Jakarta Sans', size: 11 } }
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(255,255,255,0.05)' },
          ticks: { color: 'rgba(255,255,255,0.7)', font: { family: 'Plus Jakarta Sans', size: 11 }, precision: 0 }
        }
      }
    }
  });

  // Doughnut Chart (Combined Share Overview)
  const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
  window.myDoughnutChart = new Chart(ctxDoughnut, {
    type: 'doughnut',
    data: {
      labels: chartLabels,
      datasets: [{
        data: chartDataValues,
        backgroundColor: chartColors,
        borderWidth: 1,
        borderColor: '#0a0a0a'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: 'rgba(255,255,255,0.8)',
            font: { family: 'Plus Jakarta Sans', size: 11 },
            boxWidth: 12
          }
        }
      }
    }
  });

  // Line Chart (Comparison Line Chart)
  const ctxLine = document.getElementById('lineChart').getContext('2d');
  window.myLineChart = new Chart(ctxLine, {
    type: 'line',
    data: {
      labels: chartLabels,
      datasets: [{
        label: 'Metric Trend Comparison',
        data: chartDataValues,
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        fill: true,
        tension: 0.3,
        borderWidth: 2,
        pointBackgroundColor: chartColors,
        pointRadius: 5
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          labels: {
            color: 'rgba(255,255,255,0.8)',
            font: { family: 'Plus Jakarta Sans', size: 11 }
          }
        }
      },
      scales: {
        x: {
          grid: { color: 'rgba(255,255,255,0.05)' },
          ticks: { color: 'rgba(255,255,255,0.7)', font: { family: 'Plus Jakarta Sans', size: 11 } }
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(255,255,255,0.05)' },
          ticks: { color: 'rgba(255,255,255,0.7)', font: { family: 'Plus Jakarta Sans', size: 11 }, precision: 0 }
        }
      }
    }
  });
}

// Advanced Preview Modal Handlers
function openMoviePreview(m, genreName, abaName) {
  const modal = document.getElementById('previewModal');
  document.getElementById('previewModalTitle').innerText = m.Name + ' (Advanced Details)';
  
  let poster = m['poster-url'] ? m['poster-url'] : 'assets/agasobanuye.svg';
  let trailerUrl = m['trailer-url'] || '#';
  let downloadUrl = m['download-url'] || '#';
  let watchUrl = m['watch-url'] || '#';
  let category = m.category || 'Film';
  let bioText = m.bio ? m.bio : 'No description provided.';

  let linksHtml = `<div class="preview-links">`;
  if (trailerUrl !== '#') {
    linksHtml += `<a href="${trailerUrl}" target="_blank" class="preview-link-btn"><span class="material-symbols-outlined" style="font-size:16px;">play_arrow</span> Watch Trailer Link</a>`;
  }
  if (category === 'Film') {
    if (watchUrl !== '#') {
      linksHtml += `<a href="${watchUrl}" target="_blank" class="preview-link-btn"><span class="material-symbols-outlined" style="font-size:16px;">visibility</span> Direct Watch Link</a>`;
    }
    if (downloadUrl !== '#') {
      linksHtml += `<a href="${downloadUrl}" target="_blank" class="preview-link-btn"><span class="material-symbols-outlined" style="font-size:16px;">download</span> Direct Download Link</a>`;
    }
  } else {
    linksHtml += `<button type="button" class="preview-link-btn" onclick="filterPartsByMovie('${m.id}', '${category}')"><span class="material-symbols-outlined" style="font-size:16px;">format_list_numbered</span> Get ${category}</button>`;
  }
  linksHtml += `</div>`;

  let html = `
    <div class="preview-flex">
      <img src="${poster}" alt="" class="preview-poster">
      <div class="preview-info" style="flex:1; overflow:hidden;">
        <h3>${m.Name}</h3>
        <p><strong>Category:</strong> <span class="badge">${category}</span></p>
        <p><strong>Genre:</strong> ${genreName}</p>
        <p><strong>Umusobanuzi:</strong> ${abaName}</p>
        <div class="preview-bio-container">
          <p style="margin-bottom:2px;"><strong>Bio:</strong></p>
          <div class="preview-bio-text" id="previewBioBox">${bioText}</div>
          <button type="button" class="bio-toggle-btn" id="bioToggleBtn" onclick="toggleBioExpansion()" style="display:none;">... Read more</button>
        </div>
      </div>
    </div>
    ${linksHtml}
  `;

  document.getElementById('previewModalContent').innerHTML = html;
  modal.classList.add('open');

  setTimeout(() => {
    const bioBox = document.getElementById('previewBioBox');
    const toggleBtn = document.getElementById('bioToggleBtn');
    if (bioBox && toggleBtn) {
      if (bioBox.scrollHeight > bioBox.clientHeight || bioText.length > 130) {
        toggleBtn.style.display = 'inline-block';
      }
    }
  }, 50);
}

function openAbaPreview(a) {
  const modal = document.getElementById('previewModal');
  document.getElementById('previewModalTitle').innerText = a.name + ' (Umusobanuzi Profile)';

  let poster = a['poster-url'] ? a['poster-url'] : 'assets/agasobanuye.svg';
  let createdAt = a.created_at || 'N/A';

  let linksHtml = `
    <div class="preview-links">
      <button type="button" class="preview-link-btn" onclick="filterMoviesByAba('${a.name}')"><span class="material-symbols-outlined" style="font-size:16px;">video_library</span> Get ${a.name}'s Movies Only</button>
    </div>
  `;

  let html = `
    <div class="preview-flex">
      <img src="${poster}" alt="" class="preview-poster" style="border-radius: 8px;">
      <div class="preview-info" style="flex:1; overflow:hidden;">
        <h3>${a.name}</h3>
        <p><strong>Profile ID:</strong> ${a.id}</p>
        <p><strong>Registered:</strong> ${createdAt}</p>
      </div>
    </div>
    ${linksHtml}
  `;

  document.getElementById('previewModalContent').innerHTML = html;
  modal.classList.add('open');
}

function filterMoviesByAba(abaName) {
  closePreviewModal();
  openTab('movies-tab', 'Manage Movies');
  const searchBox = document.getElementById('searchMovies');
  if (searchBox) {
    searchBox.value = abaName;
    filterTable('searchMovies', 'tableMovies');
  }
}

function toggleBioExpansion() {
  const bioBox = document.getElementById('previewBioBox');
  const toggleBtn = document.getElementById('bioToggleBtn');
  if (bioBox.classList.contains('expanded')) {
    bioBox.classList.remove('expanded');
    toggleBtn.innerText = '... Read more';
  } else {
    bioBox.classList.add('expanded');
    toggleBtn.innerText = 'Show less';
  }
}

function openPartPreview(p, parentMovName, parentMovieObj) {
  const modal = document.getElementById('previewModal');
  document.getElementById('previewModalTitle').innerText = p.name + ' (Episode / Part Details)';

  let poster = (parentMovieObj && parentMovieObj['poster-url']) ? parentMovieObj['poster-url'] : 'assets/agasobanuye.svg';
  let watchUrl = p['watch url'] || '#';
  let downloadUrl = p['Download url'] || '#';

  let linksHtml = `
    <div class="preview-links">
      <a href="${watchUrl}" target="_blank" class="preview-link-btn"><span class="material-symbols-outlined" style="font-size:16px;">visibility</span> Watch Episode Link</a>
      <a href="${downloadUrl}" target="_blank" class="preview-link-btn"><span class="material-symbols-outlined" style="font-size:16px;">download</span> Download Episode Link</a>
    </div>
  `;

  let html = `
    <div class="preview-flex">
      <img src="${poster}" alt="" class="preview-poster">
      <div class="preview-info">
        <h3>${p.name}</h3>
        <p><strong>Parent Movie:</strong> ${parentMovName}</p>
        <p><strong>Episode / Part Entry ID:</strong> ${p.id}</p>
      </div>
    </div>
    ${linksHtml}
  `;

  document.getElementById('previewModalContent').innerHTML = html;
  modal.classList.add('open');
}

function closePreviewModal() {
  document.getElementById('previewModal').classList.remove('open');
}

function filterPartsByMovie(movieId, category) {
  closePreviewModal();
  openTab('parts-tab', 'Episodes & Parts');
  
  const partMovieSelect = document.getElementById('p_movie_id');
  if (partMovieSelect) {
    partMovieSelect.value = movieId;
  }
  const partFormBody = document.getElementById('sec-parts-form');
  if (partFormBody && !partFormBody.classList.contains('open')) {
    partFormBody.classList.add('open');
  }

  const searchBox = document.getElementById('searchParts');
  const selectElem = document.getElementById('p_movie_id');
  let movieNameQuery = movieId;
  for (let i = 0; i < selectElem.options.length; i++) {
    if (selectElem.options[i].value === movieId) {
      movieNameQuery = selectElem.options[i].text.split('(')[0].trim();
      break;
    }
  }
  searchBox.value = movieNameQuery;
  filterTable('searchParts', 'tableParts');
}

// Collapsible Section Toggle
function toggleSection(sectionId) {
  const body = document.getElementById(sectionId);
  if (body) {
    body.classList.toggle('open');
  }
}

// Profile Modal Controls
function openProfileModal() {
  document.getElementById('profileModal').classList.add('open');
}

function closeProfileModal() {
  document.getElementById('profileModal').classList.remove('open');
}

// Delete Modal Controls
function openDeleteModal(actionType, paramName, paramVal, activeTab) {
  const modal = document.getElementById('deleteConfirmModal');
  document.getElementById('delete_action_input').value = actionType;
  document.getElementById('delete_tab_input').value = activeTab;
  
  let oldDynamicInput = document.getElementById('dynamicDeleteTargetInput');
  if (oldDynamicInput) oldDynamicInput.remove();

  const hiddenInput = document.createElement('input');
  hiddenInput.type = 'hidden';
  hiddenInput.name = paramName;
  hiddenInput.value = paramVal;
  hiddenInput.id = 'dynamicDeleteTargetInput';
  document.getElementById('deleteForm').appendChild(hiddenInput);

  modal.classList.add('open');
}

function closeDeleteModal() {
  document.getElementById('deleteConfirmModal').classList.remove('open');
}

// Edit Handlers for Update CRUD Operations
function editGenre(g) {
  document.getElementById('genre-action-input').value = 'update_genre';
  document.getElementById('genre-id-input').value = g.id;
  document.getElementById('g_name').value = g.name;
  document.getElementById('sec-genre-form').classList.add('open');
  window.scrollTo({top: 0, behavior: 'smooth'});
}

function editAba(a) {
  document.getElementById('aba-action-input').value = 'update_abasobanuzi';
  document.getElementById('aba-id-input').value = a.id;
  document.getElementById('a_name').value = a.name;
  document.getElementById('a_poster').value = a['poster-url'] || '';
  document.getElementById('sec-aba-form').classList.add('open');
  window.scrollTo({top: 0, behavior: 'smooth'});
}

function editMovie(m) {
  document.getElementById('movie-action-input').value = 'update_movie';
  document.getElementById('movie-id-input').value = m.id;
  document.getElementById('m_name').value = m.Name;
  document.getElementById('m_genre').value = m.Genre_id;
  document.getElementById('movieCategory').value = m.category;
  document.getElementById('m_aba').value = m.umusobanuzi_id;
  document.getElementById('m_trailer').value = m['trailer-url'] || '';
  document.getElementById('m_download').value = m['download-url'] || '';
  document.getElementById('m_watch').value = m['watch-url'] || '';
  document.getElementById('m_poster').value = m['poster-url'] || '';
  document.getElementById('m_bio').value = m.bio || '';
  toggleCategoryFields();
  document.getElementById('sec-movie-form').classList.add('open');
  window.scrollTo({top: 0, behavior: 'smooth'});
}

function editPart(p) {
  document.getElementById('part-action-input').value = 'update_part_episode';
  document.getElementById('part-id-input').value = p.id;
  document.getElementById('p_movie_id').value = p.movie_id;
  document.getElementById('p_name').value = p.name;
  document.getElementById('p_download').value = p['Download url'] || '';
  document.getElementById('p_watch').value = p['watch url'] || '';
  document.getElementById('sec-parts-form').classList.add('open');
  window.scrollTo({top: 0, behavior: 'smooth'});
}

// Live Table Search Filtering with hint
function filterTable(inputId, tableId) {
  const input = document.getElementById(inputId);
  const filter = input.value.toLowerCase();
  const table = document.getElementById(tableId);
  const tr = table.getElementsByTagName('tr');

  for (let i = 1; i < tr.length; i++) {
    let visible = false;
    const td = tr[i].getElementsByTagName('td');
    for (let j = 0; j < td.length; j++) {
      if (td[j]) {
        if (td[j].innerText.toLowerCase().indexOf(filter) > -1) {
          visible = true;
          break;
        }
      }
    }
    tr[i].style.display = visible ? '' : 'none';
  }
}

// Form Draft LocalStorage Persistence
function initDrafts() {
  document.querySelectorAll('.draft-form').forEach(form => {
    const formId = form.id;
    
    const savedDraft = localStorage.getItem('draft_' + formId);
    if (savedDraft) {
      try {
        const data = JSON.parse(savedDraft);
        Object.keys(data).forEach(name => {
          const field = form.querySelector(`[name="${name}"]`);
          if (field) field.value = data[name];
        });
      } catch(e) {}
    }

    form.addEventListener('input', function() {
      const formData = {};
      form.querySelectorAll('.draft-field').forEach(field => {
        if (field.name) formData[field.name] = field.value;
      });
      localStorage.setItem('draft_' + formId, JSON.stringify(formData));
    });

    form.addEventListener('trigger-reset', function() {
      localStorage.removeItem('draft_' + formId);
    });
  });

  document.querySelectorAll('.delete-user-btn').forEach(btn => {
    if (btn.getAttribute('data-username') === loggedInUser) {
      btn.style.opacity = '0.3';
      btn.style.cursor = 'not-allowed';
      btn.disabled = true;
      btn.title = "You cannot delete your own active account session.";
    }
  });
}

function toggleCategoryFields() {
  var cat = document.getElementById('movieCategory').value;
  var fields = document.querySelectorAll('.direct-media-field');
  fields.forEach(f => {
    f.style.display = (cat === 'Epsodes' || cat === 'Parts') ? 'none' : 'block';
  });
}