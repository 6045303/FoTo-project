<?php
$title = $title ?? 'FoTo';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        :root{
            --gold: #D3B69C;
            --offwhite: #D2B48C;
            --deepblack: #0B0B45;
            --textblack: #111111;
            --textWhite: #FFFFFF;
        }
        body{ background: var(--offwhite); color: var(--textblack); }
        nav.site-nav{ background: var(--deepblack); }
        nav.site-nav a.logo{ color: var(--gold); }
        nav.site-nav a.link{ color: var(--textWhite); }
        .btn-primary{ background: var(--gold); color: var(--textWhite); }
        .btn-ghost{ background: transparent; color: var(--textWhite); border: 1px solid rgba(255,255,255,0.12); }
        footer{ color: var(--textblack); }
    </style>
    <title><?php echo htmlspecialchars($title); ?></title>
</head>
<body>
    <nav class="site-nav shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-3xl font-bold logo">FoTo</h1>
            <div class="flex gap-6 items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="index.php?page=invite" class="text-sm text-white hover:text-yellow-200 transition">Gast uitnodigen</a>
                    <a href="index.php?page=participants" class="text-sm text-white hover:text-yellow-200 transition">Deelnemerslijst</a>
                    <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'staff'])): ?>
                        <a href="index.php?page=admin" class="text-sm text-white hover:text-yellow-200 transition font-semibold">⚙️ Admin</a>
                    <?php endif; ?>
                    <span class="text-sm text-white">Welkom, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                    <a href="index.php?logout=1" class="btn-ghost px-4 py-2 rounded font-semibold text-white transition hover:bg-white hover:text-deepblack">Uitloggen</a>
                <?php else: ?>
                    <a href="index.php?page=login" class="btn-primary px-4 py-2 rounded font-semibold text-white transition hover:opacity-90">Inloggen</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="min-h-screen flex justify-center items-center px-4 py-8">
