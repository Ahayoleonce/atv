

  var form = document.getElementById('loginForm');
  var submitBtn = document.getElementById('submitBtn');
  var alertError = document.getElementById('alertError');
  var alertErrorText = document.getElementById('alertErrorText');
  var togglePassword = document.getElementById('togglePassword');
  var passwordInput = document.getElementById('loginPassword');
  var eyeIcon = document.getElementById('eyeIcon');

  // If already logged in, skip straight to the dashboard.
  (function redirectIfLoggedIn(){
    if (window.AUTH && window.AUTH.getSession()) {
      window.location.replace(nextUrl() || '../index.php');
    }
  })();

  function nextUrl(){
    var params = new URLSearchParams(window.location.search);
    var n = params.get('next');
    return n ? decodeURIComponent(n) : '';
  }

  function showError(message){
    alertErrorText.textContent = message;
    alertError.classList.add('show');
  }
  function hideError(){
    alertError.classList.remove('show');
  }

  togglePassword.addEventListener('click', function(){
    var isHidden = passwordInput.type === 'password';
    passwordInput.type = isHidden ? 'text' : 'password';
    togglePassword.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    eyeIcon.innerHTML = isHidden
      ? '<path d="M3 3l18 18M10.6 10.7a3.2 3.2 0 0 0 4.5 4.5M7.4 7.5C4.7 9 3 12 3 12s3.5 7 9 7c1.8 0 3.4-.5 4.7-1.3M14.8 5.3A10.6 10.6 0 0 1 21 12s-.7 1.5-2.1 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
      : '<path d="M1.5 12S5 5 12 5s10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="3.2" stroke="currentColor" stroke-width="1.8"/>';
  });

  form.addEventListener('submit', async function(e){
    e.preventDefault();
    hideError();

    var id = document.getElementById('loginId').value.trim();
    var pw = passwordInput.value;
    var remember = document.getElementById('rememberMe').checked;

    if (!id || !pw){
      showError('Please fill in both fields.');
      return;
    }

    submitBtn.classList.add('loading');
    submitBtn.disabled = true;

    var result = await window.AUTH.login(id, pw);

    submitBtn.classList.remove('loading');
    submitBtn.disabled = false;

    if (!result.ok){
      showError(result.message);
      return;
    }

    window.AUTH.setSession(result.user, remember);
    window.location.href = nextUrl() || '../index.php';
  });
