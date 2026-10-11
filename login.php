<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    body { background: #0f172a; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
    .card { background: #ffffff; padding: 30px 25px; border-radius: 12px; width: 100%; max-width: 360px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
    h2 { text-align: center; color: #0088ff; margin-bottom: 20px; font-size: 22px; }
    .input-group { position: relative; margin-bottom: 15px; }
    .input-group input { width: 100%; padding: 12px 40px 12px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; outline: none; }
    .toggle-password { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #64748b; }
    .error-alert { background: #fef2f2; border: 1px solid #fca5a5; color: #dc2626; font-size: 13px; padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center; display: none; }
    button { width: 100%; padding: 12px; background: #0088ff; color: white; border: none; font-weight: bold; border-radius: 6px; cursor: pointer; }
  </style>
</head>
<body>

<div class="card">
  <h2>Connexion</h2>
  <div class="error-alert" id="errorMessage"></div>

  <form id="loginForm">
    <div class="input-group">
      <input type="text" id="username" placeholder="Identifiant de connexion" required>
    </div>
    <div class="input-group">
      <input type="password" id="password" placeholder="Mot de passe" required>
      <i class="fa-solid fa-eye toggle-password" id="toggleEye" onclick="togglePasswordVisibility()"></i>
    </div>
    <button type="submit">Se connecter</button>
  </form>
</div>

<script>
  function togglePasswordVisibility() {
    const pwd = document.getElementById('password');
    const eye = document.getElementById('toggleEye');
    if (pwd.type === 'password') {
      pwd.type = 'text'; eye.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
      pwd.type = 'password'; eye.classList.replace('fa-eye-slash', 'fa-eye');
    }
  }

  document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const errorBox = document.getElementById('errorMessage');
    errorBox.style.display = 'none';

    try {
      const res = await fetch('api.php?action=login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          username: document.getElementById('username').value,
          password: document.getElementById('password').value
        })
      });

      const data = await res.json();

      if (data.success) {
        localStorage.setItem('token', data.token);
        localStorage.setItem('role', data.role);
        localStorage.setItem('username', data.username);
        localStorage.setItem('displayName', data.display_name);
        localStorage.setItem('warningExpiringSoon', data.warningExpiringSoon);
        localStorage.setItem('daysLeft', data.daysLeft);

        if (data.role === 'SUPER_ADMIN') {
          window.location.href = 'super_admin.html';
        } else {
          window.location.href = 'index.html';
        }
      } else {
        errorBox.innerText = data.message;
        errorBox.style.display = 'block';
      }
    } catch (err) {
      errorBox.innerText = "Erreur de connexion au serveur.";
      errorBox.style.display = 'block';
    }
  });
</script>

</body>
</html>
