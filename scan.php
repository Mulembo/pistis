<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vignette Valide</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            background-color: #f2f4f7; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh;
            padding: 15px;
        }
        .vignette-card { 
            background: #ffffff; 
            width: 100%; 
            max-width: 420px; 
            border-radius: 16px; 
            padding: 28px 24px; 
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            display: none;
        }
        .vignette-header-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 18px;
        }
        .status-badge-title {
            color: #15803d;
            font-size: 18px;
            font-weight: 800;
            text-align: center;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .field-row { margin-bottom: 10px; font-size: 15px; color: #111827; line-height: 1.4; }
        .field-row .label { font-weight: 700; }
        .divider { border: 0; border-top: 1px solid #e5e7eb; margin: 18px 0; }
        .conforme-badge-container { text-align: center; margin: 16px 0; }
        .conforme-badge {
            background-color: #dcfce7;
            color: #166534;
            padding: 6px 22px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }
        .vignette-footer { text-align: center; color: #9ca3af; font-size: 12px; line-height: 1.6; }
        .error-card { background: white; padding: 30px; border-radius: 12px; text-align: center; color: #dc2626; display: none; width: 100%; max-width: 420px; }
    </style>
</head>
<body>

    <div id="agent-view" class="vignette-card">
        <div class="vignette-header-logos">
            <img src="vignette.png" alt="Logo Officiel" style="height: 45px; width: auto; object-fit: contain;">
        </div>

        <div class="status-badge-title">
            <i class="fa-solid fa-check"></i> VIGNETTE VALIDE
        </div>

        <div class="field-row"><span class="label">Centre :</span> <span id="view-centre"></span></div>
        <div class="field-row"><span class="label">Plaque :</span> <span id="view-plaque"></span></div>
        <div class="field-row"><span class="label">Vignette :</span> <span id="view-vignette_no"></span></div>
        <div class="field-row"><span class="label">Modèle :</span> <span id="view-modele"></span></div>
        <div class="field-row"><span class="label">Marque :</span> <span id="view-marque"></span></div>
        <div class="field-row"><span class="label">Date expiration :</span> <span id="view-date_expiration"></span></div>

        <hr class="divider">

        <div class="field-row"><span class="label">Propriétaire :</span> <span id="view-proprietaire"></span></div>

        <div class="conforme-badge-container">
            <span class="conforme-badge">Conforme</span>
        </div>

        <hr class="divider">

        <div class="vignette-footer">
            Système de vérification officiel<br>
            RFCK - Contrôle Technique
        </div>
    </div>

    <div id="error-view" class="error-card">
        <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
        <h3 style="margin-top: 15px;">Vignette Non Trouvée</h3>
        <p style="font-size: 14px; margin-top: 5px; color: #6b7280;">Ce code QR est invalide ou expiré.</p>
    </div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');

        const agentView = document.getElementById('agent-view');
        const errorView = document.getElementById('error-view');

        agentView.style.display = 'none';
        errorView.style.display = 'none';

        if (token) {
            // Appel de l'API avec le token
            fetch(`api.php?token=${encodeURIComponent(token)}`)
            .then(res => res.json())
            .then(res => {
                if (res.success && res.data) {
                    const d = res.data;
                    
                    // Correspondance exacte avec les colonnes de l'API actuelle
                    document.getElementById('view-centre').innerText = d.centre_controle || '-';
                    document.getElementById('view-plaque').innerText = d.plaque || '-';
                    document.getElementById('view-vignette_no').innerText = d.numero_vignette || '-';
                    document.getElementById('view-modele').innerText = d.modele || '-';
                    document.getElementById('view-marque').innerText = d.marque || '-';
                    document.getElementById('view-date_expiration').innerText = d.date_expiration_fr || d.date_expiration || '-';
                    document.getElementById('view-proprietaire').innerText = d.proprietaire || '-';

                    errorView.style.display = 'none';
                    agentView.style.display = 'block';
                } else {
                    agentView.style.display = 'none';
                    errorView.style.display = 'block';
                }
            })
            .catch(() => {
                agentView.style.display = 'none';
                errorView.style.display = 'block';
            });
        } else {
            agentView.style.display = 'none';
            errorView.style.display = 'block';
        }
    });
</script>

</body>
</html>