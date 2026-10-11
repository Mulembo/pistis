<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Vérification véhicule | RFCK</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary-blue: #024383;
      --sky-blue: #0077c8;
      --dark-blue: #0b2341;
      --banner-blue: #002e5b;
      --bg-light: #f3f6fa;
      --card-bg: #ffffff;
      --green-valid: #0d8a4f;
      --green-bg: #f0fdf4;
      --red-accent: #d9381e;
      --text-main: #1e293b;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
      --icon-box-bg: #eef5fc;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      -webkit-tap-highlight-color: transparent;
    }

    body {
      background-color: var(--bg-light);
      color: var(--text-main);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    header {
      background-color: #ffffff;
      padding: 12px 20px;
      border-bottom: 1px solid var(--border-color);
    }

    .header-inner {
      max-width: 1000px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .logo-group {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .logo-rfck-img {
      height: 44px;
      width: auto;
      object-fit: contain;
    }

    .header-badge {
      display: none;
      background-color: #f1f5f9;
      border-radius: 8px;
      padding: 6px 12px;
      font-size: 11px;
      font-weight: 700;
      color: var(--primary-blue);
      align-items: center;
      gap: 6px;
    }

    .green-dot {
      width: 7px;
      height: 7px;
      background-color: #10b981;
      border-radius: 50%;
    }

    .top-banner {
      background-color: var(--banner-blue);
      color: #ffffff;
      padding: 10px 20px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      text-align: center;
    }

    .banner-inner {
      max-width: 1000px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }

    .yellow-dot {
      width: 8px;
      height: 8px;
      background-color: #ffc107;
      border-radius: 50%;
      display: inline-block;
      flex-shrink: 0;
    }

    .container {
      width: 100%;
      max-width: 1000px;
      margin: 0 auto;
      padding: 24px 16px;
      flex: 1;
    }

    .hero-section {
      text-align: center;
      margin-bottom: 24px;
    }

    .category-title {
      font-size: 11px;
      color: var(--primary-blue);
      font-weight: 800;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .red-line {
      width: 24px;
      height: 2px;
      background-color: var(--red-accent);
    }

    .hero-title {
      font-size: clamp(22px, 4vw, 32px);
      font-weight: 800;
      color: var(--dark-blue);
      line-height: 1.25;
      margin-bottom: 14px;
    }

    .hero-title-sky {
      color: var(--sky-blue);
    }

    .hero-desc {
      font-size: 13.5px;
      color: var(--text-muted);
      line-height: 1.5;
      margin-bottom: 20px;
    }

    .bullet-points {
      display: flex;
      flex-direction: column;
      gap: 10px;
      align-items: center;
      font-size: 13px;
      color: var(--text-muted);
      font-weight: 600;
    }

    .bullet-item {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .icon-check-green {
      width: 15px;
      height: 15px;
      color: var(--green-valid);
    }

    .card {
      background: var(--card-bg);
      border-radius: 20px;
      padding: 24px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
      border: 1px solid var(--border-color);
      margin-bottom: 24px;
    }

    .card-header-wrapper {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 20px;
    }

    .card-icon-square {
      width: 54px;
      height: 54px;
      background-color: #eef5fc;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .card-official-tag {
      font-size: 11px;
      font-weight: 800;
      color: var(--primary-blue);
      letter-spacing: 0.8px;
      text-transform: uppercase;
      margin-bottom: 3px;
    }

    .search-title {
      font-size: 21px;
      font-weight: 800;
      color: var(--dark-blue);
      margin: 0;
    }

    .card-divider {
      height: 1px;
      background-color: #f1f5f9;
      margin-bottom: 20px;
    }

    .search-sub {
      font-size: 13.5px;
      color: var(--text-muted);
      margin-bottom: 20px;
      line-height: 1.5;
      font-weight: 500;
    }

    .input-group {
      display: flex;
      border: 1px solid #cbd5e1;
      border-radius: 12px;
      overflow: hidden;
      background: #ffffff;
      height: 58px;
      margin-bottom: 16px;
    }

    .badge-rdc-wrapper {
      background-color: #033669;
      padding: 0 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .badge-rdc-box {
      border: 1px solid rgba(255, 255, 255, 0.35);
      border-radius: 8px;
      color: #ffffff;
      font-weight: 700;
      font-size: 11px;
      padding: 5px 10px;
      letter-spacing: 0.5px;
    }

    .input-field {
      width: 100%;
      height: 100%;
      border: none;
      outline: none;
      font-size: 18px;
      font-weight: 600;
      color: #1e293b;
      padding: 0 16px;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .btn-submit {
      width: 100%;
      height: 54px;
      background: linear-gradient(180deg, #0252a3 0%, #003e7e 100%);
      color: #ffffff;
      border: none;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      box-shadow: 0 4px 14px rgba(2, 67, 131, 0.25);
      margin-bottom: 20px;
      transition: background 0.2s ease;
    }

    .btn-submit:disabled {
      opacity: 0.8;
      cursor: not-allowed;
    }

    .secured-text {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      gap: 8px;
      font-size: 12.5px;
      color: var(--text-muted);
      font-weight: 500;
    }

    .info-grid {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-bottom: 24px;
    }

    .info-box-item {
      background: #ffffff;
      border-radius: 14px;
      padding: 16px;
      display: flex;
      align-items: center;
      gap: 14px;
      border: 1px solid var(--border-color);
    }

    .icon-box-wrapper {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background-color: var(--icon-box-bg);
      color: var(--primary-blue);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .how-to-card {
      margin-bottom: 24px;
    }

    .how-to-title-wrapper {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 24px;
    }

    .red-bar-accent {
      width: 4px;
      height: 22px;
      background-color: var(--red-accent);
      border-radius: 2px;
      display: inline-block;
    }

    .how-to-title {
      font-size: 18px;
      font-weight: 800;
      color: var(--dark-blue);
    }

    .how-to-steps {
      display: flex;
      flex-direction: column;
      gap: 22px;
    }

    .step-item {
      display: flex;
      align-items: flex-start;
      gap: 16px;
    }

    .step-number {
      font-size: 18px;
      font-weight: 800;
      color: var(--primary-blue);
      min-width: 28px;
    }

    .step-title {
      font-size: 15px;
      font-weight: 800;
      color: var(--dark-blue);
      margin-bottom: 4px;
    }

    .step-desc {
      font-size: 13.5px;
      color: var(--text-muted);
      line-height: 1.4;
      font-weight: 500;
    }

    .custom-error-card {
      display: none;
      background: #ffffff;
      border: 1px solid #fecaca;
      border-left: 6px solid #b91c1c;
      border-radius: 16px;
      padding: 20px 22px;
      margin-top: 24px;
      box-shadow: 0 4px 20px rgba(185, 28, 28, 0.05);
    }

    .custom-error-inner {
      display: flex;
      align-items: center;
      gap: 18px;
    }

    .error-icon-circle {
      width: 48px;
      height: 48px;
      background-color: #fef2f2;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      font-weight: 900;
      color: #991b1b;
      flex-shrink: 0;
    }

    .error-text-title {
      font-size: 17px;
      font-weight: 800;
      color: #881337;
      margin-bottom: 4px;
    }

    .error-text-sub {
      font-size: 14px;
      color: #4b5563;
      font-weight: 500;
      line-height: 1.4;
    }

    #result-view {
      display: none;
      margin-top: 24px;
    }

    .result-card {
      background: #ffffff;
      border-radius: 16px;
      overflow: hidden;
      border: 1px solid var(--border-color);
      box-shadow: 0 6px 24px rgba(0,0,0,0.04);
    }

    .result-header {
      background-color: var(--green-bg);
      padding: 24px 20px;
      display: flex;
      flex-direction: column;
      gap: 16px;
      border-bottom: 1px solid #dcfce7;
    }

    .status-left {
      display: flex;
      align-items: flex-start;
      gap: 14px;
    }

    .status-icon-circle {
      width: 48px;
      height: 48px;
      background-color: var(--green-valid);
      color: #ffffff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 4px 12px rgba(13, 138, 79, 0.2);
    }

    .result-body {
      padding: 20px;
    }

    .plaque-box-container {
      background-color: #f8fafc;
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 18px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
    }

    .plaque-box-number {
      font-size: 26px;
      font-weight: 800;
      color: #0b2341;
      letter-spacing: 1.5px;
    }

    .result-rdc-badge {
      background-color: #002e5b;
      color: #ffffff;
      font-weight: 800;
      font-size: 13px;
      padding: 10px 18px;
      border-radius: 10px;
      letter-spacing: 0.5px;
      text-align: center;
      user-select: none;
    }

    .section-header-line {
      font-size: 11.5px;
      font-weight: 800;
      color: var(--dark-blue);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 22px 0 14px 0;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .section-header-line::before {
      content: "";
      width: 3px;
      height: 14px;
      background-color: var(--primary-blue);
      border-radius: 2px;
    }

    .data-grid {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .info-row-label {
      font-size: 11.5px;
      color: var(--text-muted);
      margin-bottom: 2px;
    }

    .info-row-value {
      font-size: 14px;
      font-weight: 800;
      color: var(--dark-blue);
    }

    .info-row-value.mono {
      font-family: ui-monospace, SFMono-Regular, Consolas, monospace;
      letter-spacing: 0.5px;
    }

    .official-info-footer {
      background-color: #f8fafc;
      border-top: 1px solid var(--border-color);
      padding: 24px 20px;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .btn-reset {
      background-color: #ffffff;
      border: 1px solid #c2d6eb;
      color: var(--primary-blue);
      padding: 14px 20px;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      transition: all 0.2s ease;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }

    .btn-reset:hover {
      background-color: #f1f5f9;
      border-color: var(--primary-blue);
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .spinner-icon {
      animation: spin 1s linear infinite;
    }

    @media (min-width: 768px) {
      .header-badge { display: flex; }
      .bullet-points { flex-direction: row; justify-content: center; gap: 20px; }
      .info-grid { display: grid; grid-template-columns: repeat(3, 1fr); }
      .result-header { flex-direction: row; align-items: center; justify-content: space-between; padding: 24px 28px; }
      .result-body { padding: 28px; }
      .data-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 40px; }
      .official-info-footer { padding: 28px; }
    }
  </style>
</head>
<body>

  <header>
    <div class="header-inner">
      <div class="logo-group">
        <img src="vignette.png" alt="Logo RFCK" class="logo-rfck-img">
      </div>

      <div class="header-badge">
        <span class="green-dot"></span>
        PORTAIL OFFICIEL
      </div>
    </div>
  </header>

  <div class="top-banner">
    <div class="banner-inner">
      <span class="yellow-dot"></span>
      <span>RÉGIE DES FOURRIÈRES & CONTRÔLE TECHNIQUE</span>
    </div>
  </div>

  <div class="container">

    <div id="search-view">
      
      <div class="hero-section">
        <div class="category-title">
          <span class="red-line"></span>
          CONTRÔLE TECHNIQUE DES VÉHICULES
          <span class="red-line"></span>
        </div>
        <h1 class="hero-title">
          Vérifiez un véhicule<br>
          <span class="hero-title-sky">en toute simplicité.</span>
        </h1>
        <p class="hero-desc">Consultez instantanément les informations relatives au contrôle technique d'un véhicule à partir de son numéro de plaque.</p>

        <div class="bullet-points">
          <div class="bullet-item">
            <svg class="icon-check-green" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            Données du registre RFCK
          </div>
          <div class="bullet-item">
            <svg class="icon-check-green" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            Vérification en ligne
          </div>
          <div class="bullet-item">
            <svg class="icon-check-green" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            Service public
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header-wrapper">
          <div class="card-icon-square">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#024383" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="5" width="20" height="14" rx="3"/>
              <line x1="2" y1="10" x2="22" y2="10"/>
              <line x1="6" y1="14" x2="10" y2="14"/>
            </svg>
          </div>
          <div>
            <div class="card-official-tag">RECHERCHE OFFICIELLE</div>
            <h2 class="search-title">Numéro de plaque</h2>
          </div>
        </div>

        <div class="card-divider"></div>

        <p class="search-sub">Saisissez le numéro de plaque du véhicule que vous souhaitez vérifier.</p>

        <form id="check-form">
          <div class="input-group">
            <div class="badge-rdc-wrapper">
              <div class="badge-rdc-box">RDC</div>
            </div>
            <input type="text" id="plaque-input" class="input-field" placeholder="0000AJ01" required>
          </div>

          <button type="submit" class="btn-submit" id="btn-submit">
            <svg class="search-icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/>
              <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <span id="btn-text">Vérifier le véhicule</span>
            <span id="btn-arrow" style="margin-left: 4px;">→</span>
          </button>
        </form>

        <div class="secured-text">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0d8a4f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            <path d="M9 12l2 2 4-4"/>
          </svg>
          <span>Consultation sécurisée du registre RFCK</span>
        </div>
      </div>

      <div class="info-grid">
        <div class="info-box-item">
          <div class="icon-box-wrapper">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
              <path d="M9 12l2 2 4-4"/>
            </svg>
          </div>
          <div>
            <div style="font-size:13.5px; font-weight:800; color:var(--dark-blue);">Source officielle</div>
            <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">Registre de contrôle technique RFCK</div>
          </div>
        </div>

        <div class="info-box-item">
          <div class="icon-box-wrapper">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="16" x2="12" y2="12"/>
              <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
          </div>
          <div>
            <div style="font-size:13.5px; font-weight:800; color:var(--dark-blue);">Information vérifiée</div>
            <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">Résultats issus des données enregistrées</div>
          </div>
        </div>

        <div class="info-box-item">
          <div class="icon-box-wrapper">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"/>
              <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
          </div>
          <div>
            <div style="font-size:13.5px; font-weight:800; color:var(--dark-blue);">Service accessible</div>
            <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">Vérification disponible en ligne</div>
          </div>
        </div>
      </div>

      <div class="card how-to-card">
        <div class="how-to-title-wrapper">
          <span class="red-bar-accent"></span>
          <h3 class="how-to-title">Comment effectuer une vérification ?</h3>
        </div>

        <div class="how-to-steps">
          <div class="step-item">
            <div class="step-number">01</div>
            <div>
              <div class="step-title">Saisissez la plaque</div>
              <div class="step-desc">Entrez le numéro de plaque du véhicule dans le champ de recherche.</div>
            </div>
          </div>

          <div class="step-item">
            <div class="step-number">02</div>
            <div>
              <div class="step-title">Lancez la vérification</div>
              <div class="step-desc">Cliquez sur « Vérifier le véhicule » pour consulter le registre.</div>
            </div>
          </div>

          <div class="step-item">
            <div class="step-number">03</div>
            <div>
              <div class="step-title">Consultez le résultat</div>
              <div class="step-desc">Visualisez l'état du contrôle et les informations enregistrées.</div>
            </div>
          </div>
        </div>
      </div>

      <div id="error-card" class="custom-error-card">
        <div class="custom-error-inner">
          <div class="error-icon-circle">!</div>
          <div>
            <div class="error-text-title" id="error-title">Vérification impossible</div>
            <div class="error-text-sub" id="error-sub">Véhicule non enregistré dans le système RFCK.</div>
          </div>
        </div>
      </div>

    </div>

    <div id="result-view">
      <div class="result-card">
        <div class="result-header">
          <div class="status-left">
            <div class="status-icon-circle">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
            </div>
            <div>
              <div style="font-size:11px; font-weight:800; color:#15803d; letter-spacing:0.5px; text-transform:uppercase;">RÉSULTAT DE LA VÉRIFICATION</div>
              <div style="font-size:20px; font-weight:800; color:var(--green-valid); margin-top:2px;">Contrôle technique valide</div>
              <div style="font-size:12.5px; color:var(--text-muted); margin-top:2px;">Le véhicule dispose actuellement d'un contrôle technique valide.</div>
            </div>
          </div>
        </div>

        <div style="background-color: #f8fafc; padding: 14px 20px; border-bottom: 1px solid var(--border-color);">
          <div style="font-size:10px; font-weight:800; color:var(--text-muted); letter-spacing:0.5px; text-transform:uppercase;">EXPIRATION</div>
          <div style="font-size:20px; font-weight:800; color:var(--dark-blue); margin-top:2px;" id="res-expiration-top">-</div>
        </div>

        <div class="result-body">
          <div class="plaque-box-container">
            <div>
              <div style="font-size:10px; font-weight:800; color:var(--text-muted); letter-spacing:0.5px; margin-bottom: 6px;">NUMÉRO DE PLAQUE</div>
              <div class="plaque-box-number" id="res-plaque">-</div>
            </div>
            <div class="result-rdc-badge">RDC</div>
          </div>

          <div class="section-header-line">INFORMATIONS DU VÉHICULE</div>
          <div class="data-grid">
            <div>
              <div class="info-row-label">Propriétaire</div>
              <div class="info-row-value" id="res-proprietaire">-</div>
            </div>
            <div>
              <div class="info-row-label">Marque</div>
              <div class="info-row-value" id="res-marque">-</div>
            </div>
            <div>
              <div class="info-row-label">Modèle</div>
              <div class="info-row-value" id="res-modele">-</div>
            </div>
            <div>
              <div class="info-row-label">Centre de contrôle</div>
              <div class="info-row-value" id="res-centre">-</div>
            </div>
          </div>

          <div class="section-header-line">INFORMATIONS DU CONTRÔLE</div>
          <div class="data-grid">
            <div>
              <div class="info-row-label">Date du contrôle</div>
              <div class="info-row-value" id="res-date-controle">-</div>
            </div>
            <div>
              <div class="info-row-label">Date d'expiration</div>
              <div class="info-row-value" id="res-date-expiration">-</div>
            </div>
            <div style="grid-column: 1 / -1;">
              <div class="info-row-label">Numéro de vignette</div>
              <div class="info-row-value mono" id="res-vignette">-</div>
            </div>
          </div>
        </div>

        <div class="official-info-footer">
          <div>
            <div style="font-size:11px; font-weight:800; color:var(--primary-blue); letter-spacing:0.5px; text-transform:uppercase; margin-bottom: 6px;">INFORMATION OFFICIELLE</div>
            <div style="font-size:13px; color:var(--text-muted); line-height:1.4;">Ces informations sont fournies à titre de consultation à partir des données enregistrées dans le système RFCK.</div>
          </div>

          <button class="btn-reset" id="btn-reset">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
              <path d="M3 3v5h5"/>
            </svg>
            <span>Nouvelle vérification</span>
          </button>
        </div>
      </div>
    </div>

  </div>

  <script>
    const checkForm = document.getElementById('check-form');
    const plaqueInput = document.getElementById('plaque-input');
    const resultView = document.getElementById('result-view');
    const btnReset = document.getElementById('btn-reset');
    const errorCard = document.getElementById('error-card');
    const errorTitle = document.getElementById('error-title');
    const errorSub = document.getElementById('error-sub');
    const btnSubmit = document.getElementById('btn-submit');

    function showError(title, subtitle) {
      errorTitle.textContent = title;
      errorSub.textContent = subtitle;
      errorCard.style.display = 'block';
      resultView.style.display = 'none';
      errorCard.scrollIntoView({ behavior: 'smooth', block: 'end' });
    }

    checkForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const plaqueValue = plaqueInput.value.trim();
      errorCard.style.display = 'none';
      resultView.style.display = 'none';

      btnSubmit.disabled = true;
      btnSubmit.innerHTML = `
        <svg class="spinner-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
          <path d="M12 2 a10 10 0 0 1 10 10"/>
        </svg>
        <span>Vérification...</span>`;

      fetch(`api.php?plaque=${encodeURIComponent(plaqueValue)}`)
        .then(response => {
          if (!response.ok) {
            throw new Error('Erreur HTTP ' + response.status + ' : Le fichier api.php est introuvable.');
          }
          return response.json();
        })
        .then(data => {
          btnSubmit.disabled = false;
          btnSubmit.innerHTML = `
            <svg class="search-icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/>
              <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <span>Vérifier le véhicule</span>
            <span style="margin-left: 4px;">→</span>`;

          if (data.success) {
            const v = data.data;

            document.getElementById('res-plaque').textContent = v.plaque;
            document.getElementById('res-expiration-top').textContent = v.date_expiration_fr;
            document.getElementById('res-proprietaire').textContent = v.proprietaire;
            document.getElementById('res-marque').textContent = v.marque;
            document.getElementById('res-modele').textContent = v.modele;
            document.getElementById('res-centre').textContent = v.centre_controle;
            document.getElementById('res-date-controle').textContent = v.date_controle_fr;
            document.getElementById('res-date-expiration').textContent = v.date_expiration_fr;
            document.getElementById('res-vignette').textContent = v.numero_vignette;

            resultView.style.display = 'block';
            resultView.scrollIntoView({ behavior: 'smooth', block: 'start' });
          } else {
            resultView.style.display = 'none';

            if (data.error_type === 'db_connection_error') {
              showError("Erreur de connexion DB", data.message);
            } else if (data.error_type === 'sql_query_error') {
              showError("Erreur de requête SQL", data.message);
            } else {
              showError("Plaque non trouvée", data.message);
            }
          }
        })
        .catch(err => {
          btnSubmit.disabled = false;
          btnSubmit.innerHTML = `
            <svg class="search-icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/>
              <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <span>Vérifier le véhicule</span>
            <span style="margin-left: 4px;">→</span>`;
            
          resultView.style.display = 'none';
          showError("Erreur de communication", err.message);
        });
    });

    btnReset.addEventListener('click', function() {
      resultView.style.display = 'none';
      errorCard.style.display = 'none';
      plaqueInput.value = '';
      plaqueInput.focus();
      checkForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  </script>
</body>
</html>
