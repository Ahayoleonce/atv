<?php
/**
 * AGASOBANUYE TV Admin Panel - Mobile Responsive with Offline Material Icons & Statistics Charts
 */

declare(strict_types=1);
session_start();

$feedbackMessage = "";

// Ensure JSON assets directory exists
$jsonDir = __DIR__ . '/assets/json';
if (!is_dir($jsonDir)) {
    mkdir($jsonDir, 0777, true);
}

// File paths for JSON storage
$moviesFile = $jsonDir . '/movies-details.json';
$partsFile = $jsonDir . '/parts-epsode.json';
$abasobanuziFile = $jsonDir . '/abasobanuzi.json';
$genreFile = $jsonDir . '/genre.json';
$usersInfoFile = $jsonDir . '/login-info.json';

// Helper functions to read/write JSON
function readJson($filePath) {
    if (!file_exists($filePath)) return [];
    $data = file_get_contents($filePath);
    return json_decode($data, true) ?? [];
}

function writeJson($filePath, $data) {
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
}

// Initialize JSON files if missing
if (!file_exists($moviesFile)) writeJson($moviesFile, []);
if (!file_exists($partsFile)) writeJson($partsFile, []);
if (!file_exists($abasobanuziFile)) writeJson($abasobanuziFile, []);
if (!file_exists($genreFile)) writeJson($genreFile, []);

// Standard formatted timestamp requested: 1/2/2026 11:00 am
$currentFormattedTime = date('n/j/Y g:i a');

// ================= HANDLE POST REQUESTS WITH PRG PATTERN =================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $activeTabPost = $_POST['active_tab'] ?? 'movies-tab';

    // 1. GENRE CRUD
    if ($action === 'add_genre') {
        $name = trim($_POST['name'] ?? '');
        if ($name !== '') {
            $genres = readJson($genreFile);
            $genres[] = [
                'id' => uniqid('gen_'),
                'name' => $name,
                'created_at' => $currentFormattedTime
            ];
            writeJson($genreFile, $genres);
            $_SESSION['feedback'] = "success:Genre saved.";
        }
    } elseif ($action === 'update_genre') {
        $id = trim($_POST['id'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $genres = readJson($genreFile);
        foreach ($genres as &$g) {
            if ($g['id'] === $id) { $g['name'] = $name; }
        }
        writeJson($genreFile, $genres);
        $_SESSION['feedback'] = "success:Genre saved.";
    } elseif ($action === 'delete_genre') {
        $id = trim($_POST['id'] ?? '');
        $genres = readJson($genreFile);
        $genres = array_values(array_filter($genres, fn($g) => $g['id'] !== $id));
        writeJson($genreFile, $genres);
        $_SESSION['feedback'] = "success:Genre saved.";
    }

    // 2. ABASOBANUZI CRUD
    if ($action === 'add_abasobanuzi') {
        $name = trim($_POST['name'] ?? '');
        $poster_url = trim($_POST['poster_url'] ?? '');
        if ($name !== '') {
            $abasobanuzi = readJson($abasobanuziFile);
            $abasobanuzi[] = [
                'id' => uniqid('aba_'),
                'name' => $name,
                'poster-url' => $poster_url,
                'created_at' => $currentFormattedTime
            ];
            writeJson($abasobanuziFile, $abasobanuzi);
            $_SESSION['feedback'] = "success:Umusobanuzi saved.";
        }
    } elseif ($action === 'update_abasobanuzi') {
        $id = trim($_POST['id'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $poster_url = trim($_POST['poster_url'] ?? '');
        $abasobanuzi = readJson($abasobanuziFile);
        foreach ($abasobanuzi as &$a) {
            if ($a['id'] === $id) { 
                $a['name'] = $name; 
                $a['poster-url'] = $poster_url;
            }
        }
        writeJson($abasobanuziFile, $abasobanuzi);
        $_SESSION['feedback'] = "success:Umusobanuzi saved.";
    } elseif ($action === 'delete_abasobanuzi') {
        $id = trim($_POST['id'] ?? '');
        $abasobanuzi = readJson($abasobanuziFile);
        $abasobanuzi = array_values(array_filter($abasobanuzi, fn($a) => $a['id'] !== $id));
        writeJson($abasobanuziFile, $abasobanuzi);
        $_SESSION['feedback'] = "success:Umusobanuzi saved.";
    }

    // 3. MOVIE CRUD
    if ($action === 'add_movie') {
        $name = trim($_POST['name'] ?? '');
        $genre_id = trim($_POST['genre_id'] ?? '');
        $category = trim($_POST['category'] ?? 'Film');
        $umusobanuzi_id = trim($_POST['umusobanuzi_id'] ?? '');
        $trailer_url = trim($_POST['trailer_url'] ?? '');
        $download_url = trim($_POST['download_url'] ?? '');
        $watch_url = trim($_POST['watch_url'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $poster_url = trim($_POST['poster_url'] ?? '');

        if ($name !== '') {
            $movies = readJson($moviesFile);
            if ($category === 'Epsodes' || $category === 'Parts') {
                $download_url = '';
                $watch_url = '';
            }
            $movies[] = [
                'id' => uniqid('mov_'),
                'Name' => $name,
                'Genre_id' => $genre_id,
                'category' => $category,
                'umusobanuzi_id' => $umusobanuzi_id,
                'trailer-url' => $trailer_url,
                'download-url' => $download_url,
                'watch-url' => $watch_url,
                'bio' => $bio,
                'poster-url' => $poster_url,
                'posteda-at' => $currentFormattedTime
            ];
            writeJson($moviesFile, $movies);
            $_SESSION['feedback'] = "success:Movie saved.";
        }
    } elseif ($action === 'update_movie') {
        $id = trim($_POST['id'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $genre_id = trim($_POST['genre_id'] ?? '');
        $category = trim($_POST['category'] ?? 'Film');
        $umusobanuzi_id = trim($_POST['umusobanuzi_id'] ?? '');
        $trailer_url = trim($_POST['trailer_url'] ?? '');
        $download_url = trim($_POST['download_url'] ?? '');
        $watch_url = trim($_POST['watch_url'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $poster_url = trim($_POST['poster_url'] ?? '');

        $movies = readJson($moviesFile);
        foreach ($movies as &$m) {
            if ($m['id'] === $id) {
                $m['Name'] = $name;
                $m['Genre_id'] = $genre_id;
                $m['category'] = $category;
                $m['umusobanuzi_id'] = $umusobanuzi_id;
                $m['trailer-url'] = $trailer_url;
                $m['download-url'] = ($category === 'Epsodes' || $category === 'Parts') ? '' : $download_url;
                $m['watch-url'] = ($category === 'Epsodes' || $category === 'Parts') ? '' : $watch_url;
                $m['bio'] = $bio;
                $m['poster-url'] = $poster_url;
                // Optionally update posted time on core movie modifications as well
                $m['posteda-at'] = $currentFormattedTime;
            }
        }
        writeJson($moviesFile, $movies);
        $_SESSION['feedback'] = "success:Movie saved.";
    } elseif ($action === 'delete_movie') {
        $id = trim($_POST['id'] ?? '');
        $movies = readJson($moviesFile);
        $movies = array_values(array_filter($movies, fn($m) => $m['id'] !== $id));
        writeJson($moviesFile, $movies);
        $_SESSION['feedback'] = "success:Movie saved.";
    }

    // 4. PART / EPISODE CRUD (Updates parent movie 'posteda-at' timestamp using formatted string)
    if ($action === 'add_part_episode') {
        $movie_id = trim($_POST['movie_id'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $download_url = trim($_POST['download_url'] ?? '');
        $watch_url = trim($_POST['watch_url'] ?? '');

        if ($movie_id !== '' && $name !== '') {
            $parts = readJson($partsFile);
            $parts[] = [
                'id' => uniqid('part_'),
                'movie_id' => $movie_id,
                'name' => $name,
                'Download url' => $download_url,
                'watch url' => $watch_url
            ];
            writeJson($partsFile, $parts);

            // Update parent movie timestamp
            $movies = readJson($moviesFile);
            foreach ($movies as &$m) {
                if ($m['id'] === $movie_id) {
                    $m['posteda-at'] = $currentFormattedTime;
                }
            }
            unset($m);
            writeJson($moviesFile, $movies);

            $_SESSION['feedback'] = "success:Episode/Part saved and parent movie time updated.";
        }
    } elseif ($action === 'update_part_episode') {
        $id = trim($_POST['id'] ?? '');
        $movie_id = trim($_POST['movie_id'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $download_url = trim($_POST['download_url'] ?? '');
        $watch_url = trim($_POST['watch_url'] ?? '');

        $parts = readJson($partsFile);
        foreach ($parts as &$p) {
            if ($p['id'] === $id) {
                $p['movie_id'] = $movie_id;
                $p['name'] = $name;
                $p['Download url'] = $download_url;
                $p['watch url'] = $watch_url;
            }
        }
        writeJson($partsFile, $parts);

        // Update parent movie timestamp on edit as well
        $movies = readJson($moviesFile);
        foreach ($movies as &$m) {
            if ($m['id'] === $movie_id) {
                $m['posteda-at'] = $currentFormattedTime;
            }
        }
        unset($m);
        writeJson($moviesFile, $movies);

        $_SESSION['feedback'] = "success:Episode/Part saved and parent movie time updated.";
    } elseif ($action === 'delete_part_episode') {
        $id = trim($_POST['id'] ?? '');
        $parts = readJson($partsFile);
        $parts = array_values(array_filter($parts, fn($p) => $p['id'] !== $id));
        writeJson($partsFile, $parts);
        $_SESSION['feedback'] = "success:Episode/Part saved.";
    }

    // 5. USER DELETE
    if ($action === 'delete_user') {
        $targetUsername = trim($_POST['username'] ?? '');
        $authData = readJson($usersInfoFile);
        $usersList = isset($authData['users']) ? $authData['users'] : (is_array($authData) ? $authData : []);
        
        $updatedUsers = array_filter($usersList, fn($u) => ($u['username'] ?? '') !== $targetUsername);
        if (isset($authData['users'])) {
            $authData['users'] = array_values($updatedUsers);
            writeJson($usersInfoFile, $authData);
        } else {
            writeJson($usersInfoFile, array_values($updatedUsers));
        }
        $_SESSION['feedback'] = "success:User saved.";
    }

    // 6. UPDATE PROFILE CREDENTIALS & PASSWORD
    if ($action === 'update_profile') {
        $current_user = trim($_POST['current_username'] ?? '');
        $new_username = trim($_POST['new_username'] ?? '');
        $new_email = trim($_POST['new_email'] ?? '');
        $new_phone = trim($_POST['new_phone'] ?? '');
        $new_password = trim($_POST['new_password'] ?? '');

        if ($current_user !== '' && $new_username !== '') {
            $authData = readJson($usersInfoFile);
            $usersList = isset($authData['users']) ? $authData['users'] : (is_array($authData) ? $authData : []);
            
            foreach ($usersList as &$u) {
                if (($u['username'] ?? '') === $current_user) {
                    $u['username'] = $new_username;
                    $u['email'] = $new_email;
                    $u['telnumber'] = $new_phone;
                    if ($new_password !== '') {
                        $u['password'] = $new_password; 
                    }
                }
            }
            unset($u);

            if (isset($authData['users'])) {
                $authData['users'] = $usersList;
                writeJson($usersInfoFile, $authData);
            } else {
                writeJson($usersInfoFile, $usersList);
            }
            $_SESSION['feedback'] = "success:Profile and credentials updated successfully.";
            $_SESSION['updated_username'] = $new_username;
        }
    }

    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?') . "?tab=" . urlencode($activeTabPost));
    exit;
}

if (isset($_SESSION['feedback'])) {
    $feedbackMessage = $_SESSION['feedback'];
    unset($_SESSION['feedback']);
}

$updatedUsernameSession = "";
if (isset($_SESSION['updated_username'])) {
    $updatedUsernameSession = $_SESSION['updated_username'];
    unset($_SESSION['updated_username']);
}

// Load JSON Data for display
$moviesList = readJson($moviesFile);
$partsList = readJson($partsFile);
$abasobanuziList = readJson($abasobanuziFile);
$genresList = readJson($genreFile);

$authData = readJson($usersInfoFile);
$jsonUsers = isset($authData['users']) ? $authData['users'] : (is_array($authData) ? $authData : []);

// ================= SORT ALL LISTS ALPHABETICALLY BY NAME (A-Z) =================
usort($moviesList, fn($a, $b) => strcasecmp($a['Name'] ?? '', $b['Name'] ?? ''));
usort($partsList, fn($a, $b) => strcasecmp($a['name'] ?? '', $b['name'] ?? ''));
usort($abasobanuziList, fn($a, $b) => strcasecmp($a['name'] ?? '', $b['name'] ?? ''));
usort($genresList, fn($a, $b) => strcasecmp($a['name'] ?? '', $b['name'] ?? ''));
usort($jsonUsers, fn($a, $b) => strcasecmp($a['username'] ?? '', $b['username'] ?? ''));

// Create Lookup Maps for Names instead of IDs
$genreMap = [];
foreach ($genresList as $g) {
    $genreMap[$g['id']] = $g['name'];
}

$abaMap = [];
foreach ($abasobanuziList as $a) {
    $abaMap[$a['id']] = $a['name'];
}

$movieMap = [];
foreach ($moviesList as $m) {
    $movieMap[$m['id']] = $m['Name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AGASOBANUYE TV</title>
<link rel="shortcut icon" href="../assets/agasobanuye.svg" type="image/x-icon">
<link rel="shortcut icon" href="assets/agasobanuye.svg" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<!-- Chart.js for Modern Graphs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- External Stylesheet Links -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
<link rel="stylesheet" href="assets/css/index.css">
<script src="assets/js/auth.js"></script>
<script>
  let loggedInUser = null;
  let loggedInEmail = "";
  let loggedInPhone = "";
  let isAdmin = false;

  document.addEventListener("DOMContentLoaded", function() {
    if (window.AUTH) {
      var session = window.AUTH.requireAuth('auth/');
      if (session) {
        loggedInUser = session.username;
        loggedInEmail = session.email || "";
        loggedInPhone = session.telnumber || "";
        
        let phpUpdatedUser = "<?= htmlspecialchars($updatedUsernameSession) ?>";
        if (phpUpdatedUser !== "") {
          loggedInUser = phpUpdatedUser;
          session.username = phpUpdatedUser;
          window.AUTH.setSession(session);
        }

        isAdmin = (session.role === 'admin' || session.username.toLowerCase() === 'admin');
        document.getElementById('displayUsername').textContent = loggedInUser;
        document.getElementById('profile_curr_username').value = loggedInUser;
        document.getElementById('profile_new_username').value = loggedInUser;
        document.getElementById('profile_new_email').value = loggedInEmail;
        document.getElementById('profile_new_phone').value = loggedInPhone;
        
        if (!isAdmin) {
          document.querySelectorAll('.admin-only-section').forEach(el => el.style.display = 'none');
        }
      }
    }
    initDrafts();
    initTabNavigationState();
    initStatisticsCharts();
  });
</script>
</head>
<body>

<!-- MOBILE BACKDROP OVERLAY -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleMobileSidebar()"></div>

<!-- SIDEBAR NAVIGATION -->
<aside class="sidebar" id="appSidebar">
    <div class="sidebar-brand">
        <span><span class="material-symbols-outlined" style="vertical-align:-4px; margin-right:6px;">movie</span> AGASOBANUYE TV</span>
        <span class="material-symbols-outlined" style="cursor:pointer; display:none;" id="sidebarCloseBtn" onclick="toggleMobileSidebar()">close</span>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-item" id="sidebar-movies-tab" onclick="openTab('movies-tab', 'Manage Movies', this)">
            <div class="menu-item-left"><span class="material-symbols-outlined">video_library</span> Movies Library</div>
            <span class="material-symbols-outlined" style="font-size:14px;">add</span>
        </li>
        <li class="menu-item" id="sidebar-parts-tab" onclick="openTab('parts-tab', 'Episodes & Parts', this)">
            <div class="menu-item-left"><span class="material-symbols-outlined">format_list_numbered</span> Episodes / Parts</div>
            <span class="material-symbols-outlined" style="font-size:14px;">add</span>
        </li>
        <li class="menu-item" id="sidebar-genres-tab" onclick="openTab('genres-tab', 'Manage Genres', this)">
            <div class="menu-item-left"><span class="material-symbols-outlined">label</span> Genres</div>
            <span class="material-symbols-outlined" style="font-size:14px;">add</span>
        </li>
        <li class="menu-item" id="sidebar-abasobanuzi-tab" onclick="openTab('abasobanuzi-tab', 'Abasobanuzi', this)">
            <div class="menu-item-left"><span class="material-symbols-outlined">mic</span> Abasobanuzi</div>
            <span class="material-symbols-outlined" style="font-size:14px;">add</span>
        </li>
        <li class="menu-item" id="sidebar-users-tab" onclick="openTab('users-tab', 'System Users', this)">
            <div class="menu-item-left"><span class="material-symbols-outlined">group</span> System Users</div>
            <span class="material-symbols-outlined" style="font-size:14px;">add</span>
        </li>
        <li class="menu-item" id="sidebar-statistics-tab" onclick="openTab('statistics-tab', 'Statistics', this)">
            <div class="menu-item-left"><span class="material-symbols-outlined">analytics</span> Statistics</div>
            <span class="material-symbols-outlined" style="font-size:14px;">trending_up</span>
        </li>
    </ul>
    <div class="sidebar-footer">
        <div class="user-info">
            Signed in as:
            <b id="displayUsername" onclick="openProfileModal()" title="Click to update credentials">Admin</b>
        </div>
        <button onclick="window.AUTH.clearSession(); window.location.href='auth/';" class="btn-logout">
            <span class="material-symbols-outlined" style="font-size:14px;">power_settings_new</span> Logout
        </button>
    </div>
</aside>

<!-- MAIN WORKSPACE -->
<main class="workspace">
    <!-- MOBILE TOP HEADER BAR WITH HAMBURGER ICON -->
    <header class="mobile-top-bar">
        <button class="hamburger-btn" onclick="toggleMobileSidebar()" aria-label="Toggle Menu">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
        <span style="font-weight:700; font-size:13.5px; letter-spacing:0.5px;">AGASOBANUYE TV</span>
        <span class="material-symbols-outlined" style="font-size:22px; cursor:pointer;" onclick="openProfileModal()">account_circle</span>
    </header>

    <header class="tab-header-bar">
        <div class="tab-strip" id="tabStrip"></div>
    </header>

    <!-- TOASTER NOTIFICATIONS CONTAINER -->
    <div id="toast-container">
        <?php if ($feedbackMessage !== ""): 
            list($type, $msg) = explode(':', $feedbackMessage, 2);
        ?>
            <div class="toast">
                <span class="material-symbols-outlined" style="color: #fff; font-size:18px;"><?= $type === 'success' ? 'check_circle' : 'warning' ?></span>
                <span><?= htmlspecialchars($msg) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- BLANK SCREEN IF ALL TABS CLOSED -->
    <div id="blank-screen" class="blank-welcome-screen" style="display:flex;">
        AGASOBANUYE TV
    </div>

    <!-- 1. MOVIES TAB -->
    <section id="movies-tab" class="tab-view-content">
        <div class="view-title">Manage Movies</div>
        
        <div class="admin-only-section">
            <div class="section-toggle" onclick="toggleSection('sec-movie-form')">
                <span><span class="material-symbols-outlined" style="vertical-align:-4px; margin-right:6px;">edit</span> Register New Movie / Entry form</span>
                <span class="material-symbols-outlined">expand_more</span>
            </div>
            <div id="sec-movie-form" class="collapsible-body">
                <div class="card">
                    <form method="POST" action="" class="draft-form" id="form-movie">
                        <input type="hidden" name="action" id="movie-action-input" value="add_movie">
                        <input type="hidden" name="id" id="movie-id-input" value="">
                        <input type="hidden" name="active_tab" value="movies-tab">
                        <div class="grid-3">
                            <div class="field">
                                <label>Movie Name</label>
                                <input type="text" name="name" id="m_name" class="control draft-field" required placeholder="e.g. Action Terminal">
                            </div>
                            <div class="field">
                                <label>Genre</label>
                                <select name="genre_id" id="m_genre" class="control draft-field" required>
                                    <option value="">Select Genre</option>
                                    <?php foreach ($genresList as $g): ?>
                                        <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="field">
                                <label>Category</label>
                                <select name="category" id="movieCategory" class="control draft-field" onchange="toggleCategoryFields()" required>
                                    <option value="Film">Film</option>
                                    <option value="Epsodes">Epsodes</option>
                                    <option value="Parts">Parts</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid-3">
                            <div class="field">
                                <label>Umusobanuzi</label>
                                <select name="umusobanuzi_id" id="m_aba" class="control draft-field" required>
                                    <option value="">Select Umusobanuzi</option>
                                    <?php foreach ($abasobanuziList as $a): ?>
                                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="field">
                                <label>Trailer URL</label>
                                <input type="url" name="trailer_url" id="m_trailer" class="control draft-field" placeholder="https://...">
                            </div>
                            <div class="field direct-media-field">
                                <label>Download URL</label>
                                <input type="url" name="download_url" id="m_download" class="control draft-field" placeholder="https://...">
                            </div>
                        </div>

                        <div class="grid-3">
                            <div class="field direct-media-field">
                                <label>Watch URL</label>
                                <input type="url" name="watch_url" id="m_watch" class="control draft-field" placeholder="https://...">
                            </div>
                            <div class="field">
                                <label>Poster Image URL</label>
                                <input type="text" name="poster_url" id="m_poster" class="control draft-field" placeholder="assets/img/...">
                            </div>
                            <div class="field">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn-submit" style="width:100%;">save</button>
                            </div>
                        </div>

                        <div class="field">
                            <label>Bio / Description</label>
                            <textarea name="bio" id="m_bio" class="control draft-field" rows="2" placeholder="Movie overview..."></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="section-toggle" onclick="toggleSection('sec-movie-list')">
            <span><span class="material-symbols-outlined" style="vertical-align:-4px; margin-right:6px;">table_view</span> Existing Movies Registry (Double click row for details)</span>
            <span class="material-symbols-outlined">expand_more</span>
        </div>
        <div id="sec-movie-list" class="collapsible-body open">
            <div class="search-box-container">
                <input type="text" id="searchMovies" onkeyup="filterTable('searchMovies', 'tableMovies')" placeholder="Hint: Type movie name, category or genre to filter records...">
            </div>
            <div class="card" style="padding:0; overflow:hidden;">
                <div class="table-responsive">
                    <table class="data-table" id="tableMovies">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Poster</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Genre</th>
                                <th>Umusobanuzi</th>
                                <th>Posted At</th>
                                <th class="admin-only-section">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($moviesList)): ?>
                                <tr><td colspan="8" style="text-align:center; color:var(--dim); padding:20px;">No movies stored.</td></tr>
                            <?php else: ?>
                                <?php $index = 1; foreach ($moviesList as $m): 
                                    $genreName = $genreMap[$m['Genre_id']] ?? $m['Genre_id'];
                                    $abaName = $abaMap[$m['umusobanuzi_id']] ?? $m['umusobanuzi_id'];
                                ?>
                                    <tr ondblclick='openMoviePreview(<?= json_encode($m) ?>, <?= json_encode($genreName) ?>, <?= json_encode($abaName) ?>)' title="Double click to open advanced details preview">
                                        <td><?= $index++ ?></td>
                                        <td><img src="<?= htmlspecialchars($m['poster-url'] ?: 'assets/agasobanuye.svg') ?>" alt="" style="width:30px; height:40px; object-fit:cover; border-radius:4px;"></td>
                                        <td><strong><?= htmlspecialchars($m['Name']) ?></strong></td>
                                        <td><span class="badge"><?= htmlspecialchars($m['category']) ?></span></td>
                                        <td><?= htmlspecialchars($genreName) ?></td>
                                        <td><?= htmlspecialchars($abaName) ?></td>
                                        <td style="color:var(--dim); font-size:12px;"><?= htmlspecialchars($m['posteda-at'] ?? 'N/A') ?></td>
                                        <td class="admin-only-section" onclick="event.stopPropagation();">
                                            <button type="button" class="btn-action" onclick='editMovie(<?= json_encode($m) ?>)'>Update</button>
                                            <button type="button" class="btn-danger" onclick='openDeleteModal("delete_movie", "id", "<?= $m['id'] ?>", "movies-tab")'>Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. PARTS & EPISODES TAB -->
    <section id="parts-tab" class="tab-view-content">
        <div class="view-title">Manage Episodes & Parts</div>
        
        <div class="admin-only-section">
            <div class="section-toggle" onclick="toggleSection('sec-parts-form')">
                <span><span class="material-symbols-outlined" style="vertical-align:-4px; margin-right:6px;">edit</span> Register Episode / Part Form</span>
                <span class="material-symbols-outlined">expand_more</span>
            </div>
            <div id="sec-parts-form" class="collapsible-body">
                <div class="card">
                    <form method="POST" action="" class="draft-form" id="form-parts">
                        <input type="hidden" name="action" id="part-action-input" value="add_part_episode">
                        <input type="hidden" name="id" id="part-id-input" value="">
                        <input type="hidden" name="active_tab" value="parts-tab">
                        <div class="grid-2">
                            <div class="field">
                                <label>Select Parent Movie (Category: Epsodes/Parts)</label>
                                <select name="movie_id" id="p_movie_id" class="control draft-field" required>
                                    <option value="">Choose Movie</option>
                                    <?php foreach ($moviesList as $m): 
                                        if ($m['category'] === 'Epsodes' || $m['category'] === 'Parts'):
                                    ?>
                                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['Name']) ?> (<?= $m['category'] ?>)</option>
                                    <?php endif; endforeach; ?>
                                </select>
                            </div>
                            <div class="field">
                                <label>Episode / Part Name</label>
                                <input type="text" name="name" id="p_name" class="control draft-field" required placeholder="e.g. Episode 1">
                            </div>
                        </div>
                        <div class="grid-2">
                            <div class="field">
                                <label>Download URL</label>
                                <input type="url" name="download_url" id="p_download" class="control draft-field" required placeholder="https://...">
                            </div>
                            <div class="field">
                                <label>Watch URL</label>
                                <input type="url" name="watch_url" id="p_watch" class="control draft-field" required placeholder="https://...">
                            </div>
                        </div>
                        <button type="submit" class="btn-submit">save</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="section-toggle" onclick="toggleSection('sec-parts-list')">
            <span><span class="material-symbols-outlined" style="vertical-align:-4px; margin-right:6px;">table_view</span> Existing Episodes & Parts (Double click row for details)</span>
            <span class="material-symbols-outlined">expand_more</span>
        </div>
        <div id="sec-parts-list" class="collapsible-body open">
            <div class="search-box-container">
                <input type="text" id="searchParts" onkeyup="filterTable('searchParts', 'tableParts')" placeholder="Hint: Search part name or movie name reference...">
            </div>
            <div class="card" style="padding:0; overflow:hidden;">
                <div class="table-responsive">
                    <table class="data-table" id="tableParts">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Parent Movie Name</th>
                                <th>Part / Episode Name</th>
                                <th>Download Link</th>
                                <th>Watch Link</th>
                                <th class="admin-only-section">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($partsList)): ?>
                                <tr><td colspan="6" style="text-align:center; color:var(--dim); padding:20px;">No parts or episodes stored.</td></tr>
                            <?php else: ?>
                                <?php $index = 1; foreach ($partsList as $p): 
                                    $parentMovName = $movieMap[$p['movie_id']] ?? $p['movie_id'];
                                    $parentMovieObj = null;
                                    foreach($moviesList as $m) {
                                        if ($m['id'] === $p['movie_id']) { $parentMovieObj = $m; break; }
                                    }
                                ?>
                                    <tr ondblclick='openPartPreview(<?= json_encode($p) ?>, <?= json_encode($parentMovName) ?>, <?= json_encode($parentMovieObj) ?>)' title="Double click to open advanced details preview">
                                        <td><?= $index++ ?></td>
                                        <td><strong><?= htmlspecialchars($parentMovName) ?></strong></td>
                                        <td><?= htmlspecialchars($p['name']) ?></td>
                                        <td><a href="<?= htmlspecialchars($p['Download url']) ?>" target="_blank" style="text-decoration:underline; color:#fff;" onclick="event.stopPropagation();">Download</a></td>
                                        <td><a href="<?= htmlspecialchars($p['watch url']) ?>" target="_blank" style="text-decoration:underline; color:#fff;" onclick="event.stopPropagation();">Watch</a></td>
                                        <td class="admin-only-section" onclick="event.stopPropagation();">
                                            <button type="button" class="btn-action" onclick='editPart(<?= json_encode($p) ?>)'>Update</button>
                                            <button type="button" class="btn-danger" onclick='openDeleteModal("delete_part_episode", "id", "<?= $p['id'] ?>", "parts-tab")'>Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. GENRES TAB -->
    <section id="genres-tab" class="tab-view-content">
        <div class="view-title">Manage Genres</div>
        
        <div class="admin-only-section">
            <div class="section-toggle" onclick="toggleSection('sec-genre-form')">
                <span><span class="material-symbols-outlined" style="vertical-align:-4px; margin-right:6px;">edit</span> Add/Edit Genre Form</span>
                <span class="material-symbols-outlined">expand_more</span>
            </div>
            <div id="sec-genre-form" class="collapsible-body">
                <div class="card">
                    <form method="POST" action="" class="draft-form" id="form-genres">
                        <input type="hidden" name="action" id="genre-action-input" value="add_genre">
                        <input type="hidden" name="id" id="genre-id-input" value="">
                        <input type="hidden" name="active_tab" value="genres-tab">
                        <div class="grid-2">
                            <div class="field">
                                <label>Genre Name</label>
                                <input type="text" name="name" id="g_name" class="control draft-field" required placeholder="e.g. Action, Horror, Sci-Fi">
                            </div>
                            <div class="field" style="display:flex; align-items:flex-end;">
                                <button type="submit" class="btn-submit" style="width:100%;">save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="section-toggle" onclick="toggleSection('sec-genre-list')">
            <span><span class="material-symbols-outlined" style="vertical-align:-4px; margin-right:6px;">table_view</span> Existing Genres Registry</span>
            <span class="material-symbols-outlined">expand_more</span>
        </div>
        <div id="sec-genre-list" class="collapsible-body open">
            <div class="search-box-container">
                <input type="text" id="searchGenres" onkeyup="filterTable('searchGenres', 'tableGenres')" placeholder="Hint: Search genre name...">
            </div>
            <div class="card" style="padding:0; overflow:hidden;">
                <div class="table-responsive">
                    <table class="data-table" id="tableGenres">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Genre Name</th>
                                <th>Created At</th>
                                <th class="admin-only-section">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($genresList)): ?>
                                <tr><td colspan="4" style="text-align:center; color:var(--dim); padding:20px;">No genres found.</td></tr>
                            <?php else: ?>
                                <?php $index = 1; foreach ($genresList as $g): ?>
                                    <tr>
                                        <td><?= $index++ ?></td>
                                        <td><strong><?= htmlspecialchars($g['name']) ?></strong></td>
                                        <td style="color:var(--dim);"><?= htmlspecialchars($g['created_at']) ?></td>
                                        <td class="admin-only-section">
                                            <button type="button" class="btn-action" onclick='editGenre(<?= json_encode($g) ?>)'>Update</button>
                                            <button type="button" class="btn-danger" onclick='openDeleteModal("delete_genre", "id", "<?= $g['id'] ?>", "genres-tab")'>Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. ABASOBANUZI TAB -->
    <section id="abasobanuzi-tab" class="tab-view-content">
        <div class="view-title">Manage Abasobanuzi</div>
        
        <div class="admin-only-section">
            <div class="section-toggle" onclick="toggleSection('sec-aba-form')">
                <span><span class="material-symbols-outlined" style="vertical-align:-4px; margin-right:6px;">edit</span> Add/Edit Umusobanuzi Form</span>
                <span class="material-symbols-outlined">expand_more</span>
            </div>
            <div id="sec-aba-form" class="collapsible-body">
                <div class="card">
                    <form method="POST" action="" class="draft-form" id="form-abasobanuzi">
                        <input type="hidden" name="action" id="aba-action-input" value="add_abasobanuzi">
                        <input type="hidden" name="id" id="aba-id-input" value="">
                        <input type="hidden" name="active_tab" value="abasobanuzi-tab">
                        <div class="grid-2">
                            <div class="field">
                                <label>Umusobanuzi Name</label>
                                <input type="text" name="name" id="a_name" class="control draft-field" required placeholder="e.g. Yakuza, Rocky, etc.">
                            </div>
                            <div class="field">
                                <label>Poster / Profile Image URL</label>
                                <input type="text" name="poster_url" id="a_poster" class="control draft-field" placeholder="assets/img/profile.jpg">
                            </div>
                        </div>
                        <div class="field">
                            <button type="submit" class="btn-submit">save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="section-toggle" onclick="toggleSection('sec-aba-list')">
            <span><span class="material-symbols-outlined" style="vertical-align:-4px; margin-right:6px;">table_view</span> Existing Abasobanuzi Registry (Double click row for profile details)</span>
            <span class="material-symbols-outlined">expand_more</span>
        </div>
        <div id="sec-aba-list" class="collapsible-body open">
            <div class="search-box-container">
                <input type="text" id="searchAba" onkeyup="filterTable('searchAba', 'tableAba')" placeholder="Hint: Search umusobanuzi name...">
            </div>
            <div class="card" style="padding:0; overflow:hidden;">
                <div class="table-responsive">
                    <table class="data-table" id="tableAba">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Created At</th>
                                <th class="admin-only-section">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($abasobanuziList)): ?>
                                <tr><td colspan="5" style="text-align:center; color:var(--dim); padding:20px;">No abasobanuzi found.</td></tr>
                            <?php else: ?>
                                <?php $index = 1; foreach ($abasobanuziList as $a): 
                                    $abaImg = (!empty(trim($a['poster-url']))) ? $a['poster-url'] : 'assets/agasobanuye.svg';
                                ?>
                                    <tr ondblclick='openAbaPreview(<?= json_encode($a) ?>)' title="Double click to open profile details" style="border-radius: 8px;">
                                        <td><?= $index++ ?></td>
                                        <td><img src="<?= htmlspecialchars($abaImg) ?>" alt="" style="width:32px; height:42px; object-fit:cover; border-radius:6px;"></td>
                                        <td><strong><?= htmlspecialchars($a['name']) ?></strong></td>
                                        <td style="color:var(--dim);"><?= htmlspecialchars($a['created_at']) ?></td>
                                        <td class="admin-only-section" onclick="event.stopPropagation();">
                                            <button type="button" class="btn-action" onclick='editAba(<?= json_encode($a) ?>)'>Update</button>
                                            <button type="button" class="btn-danger" onclick='openDeleteModal("delete_abasobanuzi", "id", "<?= $a['id'] ?>", "abasobanuzi-tab")'>Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. USERS TAB -->
    <section id="users-tab" class="tab-view-content">
        <div class="view-title">System Users</div>
        <div class="search-box-container">
            <input type="text" id="searchUsers" onkeyup="filterTable('searchUsers', 'tableUsers')" placeholder="Hint: Search username or email address...">
        </div>
        <div class="card" style="padding:0; overflow:hidden;">
            <div class="table-responsive">
                <table class="data-table" id="tableUsers">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Role</th>
                            <th class="admin-only-section">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jsonUsers)): ?>
                            <tr><td colspan="6" style="text-align:center; color:var(--dim); padding:20px;">No users found.</td></tr>
                        <?php else: ?>
                            <?php $index = 1; foreach ($jsonUsers as $u): 
                                $uname = $u['username'] ?? '';
                            ?>
                                <tr>
                                    <td><?= $index++ ?></td>
                                    <td><strong><?= htmlspecialchars($uname) ?></strong></td>
                                    <td style="color:var(--dim);"><?= htmlspecialchars($u['email'] ?? 'N/A') ?></td>
                                    <td style="color:var(--dim);"><?= htmlspecialchars($u['telnumber'] ?? 'N/A') ?></td>
                                    <td><span class="badge"><?= htmlspecialchars($u['role'] ?? 'user') ?></span></td>
                                    <td class="admin-only-section">
                                        <?php if ($uname !== ''): ?>
                                            <button type="button" class="btn-danger delete-user-btn" onclick='openDeleteModal("delete_user", "username", "<?= htmlspecialchars($uname) ?>", "users-tab")' data-username="<?= htmlspecialchars($uname) ?>">Delete</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- 6. STATISTICS TAB -->
    <section id="statistics-tab" class="tab-view-content">
        <div class="view-title">
            <span>System Analytics & Statistics</span>
            <span style="font-size: 12px; font-weight: 500; color: var(--dim); font-family: 'JetBrains Mono', monospace;">Real-time overview</span>
        </div>

        <!-- STATS METRIC CARDS -->
        <div class="stats-grid">
            <div class="stat-box">
                <span class="stat-box-title">Total Movies</span>
                <span class="stat-box-value"><?= count($moviesList) ?></span>
                <span class="material-symbols-outlined stat-box-icon">video_library</span>
            </div>
            <div class="stat-box">
                <span class="stat-box-title">Episodes & Parts</span>
                <span class="stat-box-value"><?= count($partsList) ?></span>
                <span class="material-symbols-outlined stat-box-icon">format_list_numbered</span>
            </div>
            <div class="stat-box">
                <span class="stat-box-title">Genres</span>
                <span class="stat-box-value"><?= count($genresList) ?></span>
                <span class="material-symbols-outlined stat-box-icon">label</span>
            </div>
            <div class="stat-box">
                <span class="stat-box-title">Abasobanuzi</span>
                <span class="stat-box-value"><?= count($abasobanuziList) ?></span>
                <span class="material-symbols-outlined stat-box-icon">mic</span>
            </div>
            <div class="stat-box">
                <span class="stat-box-title">System Users</span>
                <span class="stat-box-value"><?= count($jsonUsers) ?></span>
                <span class="material-symbols-outlined stat-box-icon">group</span>
            </div>
        </div>

        <!-- GRAPHS SECTION -->
        <div class="charts-grid">
            <!-- Individual Module Bar Graph -->
            <div class="chart-card">
                <div class="chart-card-title">
                    <span class="material-symbols-outlined" style="font-size:18px;">bar_chart</span> Module Distribution Bar Graph
                </div>
                <div class="chart-canvas-container">
                    <canvas id="barChart"></canvas>
                </div>
            </div>

            <!-- Combined Overview Doughnut Graph -->
            <div class="chart-card">
                <div class="chart-card-title">
                    <span class="material-symbols-outlined" style="font-size:18px;">donut_large</span> Combined Share Overview
                </div>
                <div class="chart-canvas-container">
                    <canvas id="doughnutChart"></canvas>
                </div>
            </div>
        </div>

        <!-- COMPARISON LINE CHARTS SECTION -->
        <div class="charts-grid" style="grid-template-columns: 1fr; margin-top: 20px;">
            <div class="chart-card">
                <div class="chart-card-title">
                    <span class="material-symbols-outlined" style="font-size:18px;">show_chart</span> Module Metric Comparison Line Chart
                </div>
                <div class="chart-canvas-container" style="height: 300px;">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>
    </section>

</main>

<!-- EDIT PROFILE MODAL -->
<div class="modal-overlay" id="profileModal">
    <div class="modal-card">
        <div class="modal-header">
            <span>Update Credentials & Password</span>
            <span class="material-symbols-outlined modal-close" onclick="closeProfileModal()">close</span>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="update_profile">
            <input type="hidden" name="current_username" id="profile_curr_username" value="">
            <input type="hidden" name="active_tab" id="profile_active_tab" value="movies-tab">
            <div class="field">
                <label>Username</label>
                <input type="text" name="new_username" id="profile_new_username" class="control" required>
            </div>
            <div class="field">
                <label>Email Address</label>
                <input type="email" name="new_email" id="profile_new_email" class="control">
            </div>
            <div class="field">
                <label>Phone Number</label>
                <input type="text" name="new_phone" id="profile_new_phone" class="control">
            </div>
            <div class="field">
                <label>New Password (leave blank to keep current)</label>
                <input type="password" name="new_password" id="profile_new_password" class="control" placeholder="••••••••">
            </div>
            <button type="submit" class="btn-submit" style="width:100%; margin-top:10px;">save</button>
        </form>
    </div>
</div>

<!-- ADVANCED MOVIE/PART/ABASOBANUZI PREVIEW MODAL -->
<div class="modal-overlay" id="previewModal">
    <div class="preview-modal-card">
        <div class="modal-header">
            <span id="previewModalTitle">Advanced Entry Details</span>
            <span class="material-symbols-outlined modal-close" onclick="closePreviewModal()">close</span>
        </div>
        <div class="modal-body" id="previewModalContent"></div>
        <div class="modal-footer" id="previewModalFooter">
            <button type="button" class="btn-action" onclick="closePreviewModal()">Close</button>
        </div>
    </div>
</div>

<!-- CONFIRMATION MODAL FOR DELETIONS -->
<div class="modal-overlay" id="deleteConfirmModal">
    <div class="modal-card">
        <div class="modal-header">
            <span>Confirm Deletion</span>
            <span class="material-symbols-outlined modal-close" onclick="closeDeleteModal()">close</span>
        </div>
        <div class="modal-body">
            Are you sure you want to delete this item? This action cannot be undone.
        </div>
        <div class="modal-footer">
            <form id="deleteForm" method="POST" action="">
                <input type="hidden" name="action" id="delete_action_input" value="">
                <input type="hidden" name="delete_target_key" id="delete_key_input" value="">
                <input type="hidden" name="active_tab" id="delete_tab_input" value="">
                <button type="button" class="btn-action" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" class="btn-danger">Confirm Delete</button>
            </form>
        </div>
    </div>
</div>

<!-- External JavaScript Link -->
<script src="assets/js/index.js"></script>
</body>
</html>