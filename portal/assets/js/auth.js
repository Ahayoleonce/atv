/* ============================================================
   AGASOBANUYE TV — Auth helper
   Shared by portal/auth/index.html (login) and portal/index.html
   (dashboard guard). Loaded as a plain <script>, exposes window.AUTH.
   ============================================================ */
(function (global) {
  "use strict";

  var SESSION_KEY = "agasobanuye_session";

  /**
   * Figure out where "assets/" actually is, relative to whichever page loaded
   * this script — by reading this <script> tag's own src attribute. This
   * means login-info.json resolves correctly no matter how deep the calling
   * page is nested (portal/index.html, portal/auth/index.html, or anywhere
   * else you move things), instead of relying on a hardcoded guess.
   */
  function detectAssetsBase() {
    var el = document.currentScript;
    if (!el) {
      // Fallback for browsers/edge-cases where currentScript is unavailable:
      // scan all <script> tags for the one that loaded this file.
      var scripts = document.getElementsByTagName("script");
      for (var i = 0; i < scripts.length; i++) {
        var s = scripts[i].getAttribute("src") || "";
        if (s.indexOf("assets/js/auth.js") !== -1) { el = scripts[i]; break; }
      }
    }
    var src = el ? (el.getAttribute("src") || "") : "";
    var idx = src.indexOf("assets/js/auth.js");
    return idx !== -1 ? src.slice(0, idx) + "assets/" : "assets/";
  }

  var USERS_JSON_PATH_FROM_ROOT = detectAssetsBase() + "json/login-info.json";

  /**
   * Resolve the users JSON path relative to whichever page is calling in.
   * Auto-detected from this script's own <script src="..."> by default;
   * pass options.usersPath to override.
   */
  function resolveUsersPath(customPath) {
    return customPath || USERS_JSON_PATH_FROM_ROOT;
  }

  /** SHA-256 hash a UTF-8 string, return lowercase hex. Uses the native Web Crypto API. */
  async function sha256Hex(text) {
    if (!global.crypto || !global.crypto.subtle) {
      throw new Error(
        "Web Crypto API unavailable. Serve this site over HTTPS or http://localhost — " +
        "browsers block crypto.subtle on plain file:// pages."
      );
    }
    var enc = new TextEncoder().encode(text);
    var digest = await global.crypto.subtle.digest("SHA-256", enc);
    var bytes = Array.from(new Uint8Array(digest));
    return bytes.map(function (b) { return b.toString(16).padStart(2, "0"); }).join("");
  }

  /** Hash a plaintext password with a per-user salt the same way login-info.json stores it. */
  async function hashPassword(password, salt) {
    return sha256Hex(String(salt) + String(password));
  }

  /** Load and parse the users file. Throws a friendly error on failure (e.g. opened via file://). */
  async function loadUsers(customPath) {
    var path = resolveUsersPath(customPath);
    var res;
    try {
      res = await fetch(path, { cache: "no-store" });
    } catch (err) {
      throw new Error(
        "Could not reach " + path + ". If you're opening this file directly (file://), " +
        "start a local server instead, e.g. `python3 -m http.server` from the AgasobanuyeTv folder, " +
        "then open http://localhost:8000/portal/auth/"
      );
    }
    if (!res.ok) throw new Error("Could not load user records (HTTP " + res.status + ").");
    var data = await res.json();
    return Array.isArray(data.users) ? data.users : [];
  }

  /** Find a user whose username, email, or phone number matches the given identifier. */
  function findUser(users, identifier) {
    var q = String(identifier).trim().toLowerCase();
    return users.find(function (u) {
      return (
        (u.username && u.username.toLowerCase() === q) ||
        (u.email && u.email.toLowerCase() === q) ||
        (u.telnumber && u.telnumber.replace(/\s+/g, "") === identifier.trim().replace(/\s+/g, ""))
      );
    });
  }

  /**
   * Attempt to log in. Resolves to { ok:true, user } on success,
   * or { ok:false, message } on failure. Never throws for bad credentials.
   */
  async function login(identifier, password, options) {
    options = options || {};
    try {
      var users = await loadUsers(options.usersPath);
      var user = findUser(users, identifier);
      if (!user) return { ok: false, message: "No account matches that username, email or phone number." };

      var candidateHash = await hashPassword(password, user.salt);
      if (candidateHash !== user.passwordHash) {
        return { ok: false, message: "Incorrect password. Please try again." };
      }
      return { ok: true, user: user };
    } catch (err) {
      return { ok: false, message: err.message || "Something went wrong while signing in." };
    }
  }

  /** Persist a session. `remember=true` -> survives browser close (localStorage). Otherwise sessionStorage. */
  function setSession(user, remember) {
    var payload = {
      username: user.username,
      email: user.email,
      role: user.role || "other",
      loginAt: new Date().toISOString()
    };
    var raw = JSON.stringify(payload);
    // Always clear both first so we never end up with two stale copies.
    localStorage.removeItem(SESSION_KEY);
    sessionStorage.removeItem(SESSION_KEY);
    (remember ? localStorage : sessionStorage).setItem(SESSION_KEY, raw);
    return payload;
  }

  /** Read the current session from either storage. Returns null if not logged in. */
  function getSession() {
    var raw = localStorage.getItem(SESSION_KEY) || sessionStorage.getItem(SESSION_KEY);
    if (!raw) return null;
    try {
      return JSON.parse(raw);
    } catch (e) {
      return null;
    }
  }

  function clearSession() {
    localStorage.removeItem(SESSION_KEY);
    sessionStorage.removeItem(SESSION_KEY);
  }

  /**
   * Call at the very top of a protected page. Redirects to the login page
   * if there is no valid session, and returns the session object otherwise.
   * `loginPath` should point at auth/index.html relative to the calling page.
   */
  function requireAuth(loginPath) {
    var session = getSession();
    if (!session) {
      var here = global.location.pathname + global.location.search;
      global.location.replace(loginPath + "?next=" + encodeURIComponent(here));
      return null;
    }
    return session;
  }

  global.AUTH = {
    sha256Hex: sha256Hex,
    hashPassword: hashPassword,
    loadUsers: loadUsers,
    findUser: findUser,
    login: login,
    setSession: setSession,
    getSession: getSession,
    clearSession: clearSession,
    requireAuth: requireAuth
  };
})(window);