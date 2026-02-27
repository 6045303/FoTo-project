<?php
require __DIR__ . '/init.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php?page=login');
    exit;
}

$title = $page === 'login' ? 'Inloggen' : ($page === 'register' ? 'Registreren' : ($page === 'admin' ? 'Admin Paneel' : 'Home'));
require __DIR__ . '/partials/header.php';

if ($page === 'login') {
    include __DIR__ . '/login.php';
} elseif ($page === 'register') {
    include __DIR__ . '/register.php';
} elseif ($page === 'invite') {
    include __DIR__ . '/invite.php';
} elseif ($page === 'participants') {
    include __DIR__ . '/participants.php';
} elseif ($page === 'admin') {
    include __DIR__ . '/admin.php';
} else {
    echo '<div class="bg-white shadow-lg rounded-lg p-8 text-center w-full max-w-3xl">';
    echo '<h2 class="text-4xl font-bold text-gray-800 mb-4">Welkom bij FoTo!</h2>';
    echo '<p class="text-gray-600 text-lg mb-6">Je bent ingelogd als ' . htmlspecialchars($_SESSION['user_name'] ?? 'Gast') . '</p>';
    if (!isset($_SESSION['user_id'])) {
        echo '<div class="flex gap-4 justify-center"><a href="index.php?page=login" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Inloggen</a><a href="index.php?page=register" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">Registreren</a></div>';
    }
    echo '</div>';
}

require __DIR__ . '/partials/footer.php';
