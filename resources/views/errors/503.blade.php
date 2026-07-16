<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COOPA EBEN - Mise à niveau en cours</title>
    <style>
        :root {
            --bg-1: #f4f1ea;
            --bg-2: #e5d8c2;
            --card: #fffdf8;
            --ink: #1f2a37;
            --muted: #5b6878;
            --primary: #0a7c66;
            --primary-dark: #075f4e;
            --accent: #c08b2f;
            --border: #ecd9b2;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 15% 15%, #fff4da 0%, transparent 35%),
                radial-gradient(circle at 85% 80%, #dceee8 0%, transparent 40%),
                linear-gradient(135deg, var(--bg-1), var(--bg-2));
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .card {
            width: min(820px, 100%);
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 14px 40px rgba(20, 34, 54, 0.14);
            animation: rise 420ms ease-out;
        }

        .tag {
            display: inline-block;
            background: #f1f9f6;
            color: var(--primary-dark);
            border: 1px solid #caeadf;
            border-radius: 999px;
            padding: 7px 12px;
            font-weight: 600;
            font-size: 13px;
            letter-spacing: .2px;
        }

        h1 {
            margin: 14px 0 12px;
            line-height: 1.2;
            font-size: clamp(1.6rem, 2.8vw, 2.3rem);
        }

        p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
            font-size: clamp(1rem, 1.8vw, 1.05rem);
        }

        .notice {
            margin-top: 18px;
            background: #fff8ea;
            border: 1px solid #f0d9aa;
            border-left: 4px solid var(--accent);
            border-radius: 10px;
            padding: 12px 14px;
            color: #5b4a2f;
            font-weight: 600;
        }

        .actions {
            margin-top: 26px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn {
            text-decoration: none;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 700;
            transition: transform 120ms ease, box-shadow 120ms ease, background 120ms ease;
        }

        .btn-main {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 6px 16px rgba(10, 124, 102, 0.28);
        }

        .btn-main:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-ghost {
            color: var(--primary-dark);
            border: 1px solid #b8dfd4;
            background: #f6fcfa;
        }

        footer {
            margin-top: 22px;
            color: #7a8492;
            font-size: .92rem;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <main class="card">
        <span class="tag">Information service</span>
        <h1>Le système COOPA EBEN est en cours de mise à niveau</h1>
        <p>
            Nous déployons des améliorations techniques pour renforcer la stabilité,
            la sécurité et la performance globale de la plateforme.
        </p>

        <div class="notice">
            Certaines fonctionnalités peuvent être temporairement indisponibles pendant cette opération.
        </div>

        <div class="actions">
            {{-- Utilisation de la syntaxe Blade pour intercepter le paramètre 'acc' --}}
            @if(request()->get('acc') === '1')
                <a class="btn btn-main" href="/login">Se connecter</a>
            @endif
            
            <a class="btn btn-ghost" href="javascript:location.reload()">Actualiser la page</a>
        </div>

        <footer>
            Merci pour votre patience. La plateforme sera pleinement disponible après finalisation de la mise à niveau.
        </footer>
    </main>
</body>
</html>