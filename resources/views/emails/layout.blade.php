<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RDT')</title>
    <style>
        body { margin: 0; padding: 0; background: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 32px 40px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 22px; margin: 0; font-weight: 700; letter-spacing: -0.5px; }
        .header p { color: rgba(255,255,255,0.8); font-size: 13px; margin: 6px 0 0; }
        .body { padding: 40px; color: #334155; line-height: 1.7; font-size: 15px; }
        .body h2 { color: #1e293b; font-size: 20px; margin: 0 0 16px; font-weight: 700; }
        .body p { margin: 0 0 14px; }
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .info-box .row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .info-box .row:last-child { border-bottom: none; }
        .info-box .label { color: #64748b; font-weight: 600; }
        .info-box .value { color: #1e293b; font-weight: 700; }
        .btn { display: inline-block; background: #4f46e5; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 14px; margin: 16px 0; }
        .footer { background: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #94a3b8; font-size: 12px; margin: 0; }
        .badge-success { display: inline-block; background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
        .badge-danger { display: inline-block; background: #fef2f2; color: #991b1b; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
        .badge-warning { display: inline-block; background: #fefce8; color: #854d0e; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Registre Dynamique de Travail</h1>
            <p>@yield('subtitle', 'Plateforme de gestion d\'entreprise')</p>
        </div>
        <div class="body">
            @yield('content')
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Registre Dynamique de Travail — Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
