<?php
/**
 * FoTo Project - Unified Website
 * Single-file application with OOP classes
 * Open: http://localhost/FoTo-project/website.php
 */

// Start output buffering immediately to avoid "headers already sent" issues
if (!ob_get_level()) {
    ob_start();
}

require __DIR__ . '/src/PHP/init.php';
require_once __DIR__ . '/../../vendor/autoload.php';

// Get current page from URL parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$user_id = \App\Auth::getUserId();
$user_name = \App\Auth::getUserName();
$user_role = \App\Auth::getUserRole();

// Handle logout
if (isset($_GET['logout'])) {
    \App\Auth::logout();
    header('Location: website.php');
    exit;
}

// Determine page title

$titles = [
    'home' => 'Home',
    'login' => 'Inloggen',
    'register' => 'Registreren',
    'admin' => 'Admin Paneel',
    'invite' => 'Uitnodigingen',
    'participants' => 'Deelnemers',
    'bookings-overview' => 'Boekingen Overzicht',
    'booking-indoor' => 'Binnen Activiteit Boeken',
    'booking-outdoor' => 'Buiten Activiteit Boeken'
];

$title = $titles[$page] ?? 'FoTo Project';

// Allow only authorized pages for authenticated users
if ($user_id && in_array($page, ['login', 'register'])) {
    header('Location: website.php?page=home');
    exit;
}

// Require login for certain pages
$login_required_pages = ['admin', 'invite', 'participants'];
if (in_array($page, $login_required_pages) && !$user_id) {
    header('Location: website.php?page=login');
    exit;
}

// Require admin for admin pages
if ($page === 'admin' && !in_array($user_role, ['admin', 'staff'])) {
    header('Location: website.php?page=home');
    exit;
}

// Handle login POST early to ensure headers can be modified before output
if ($page === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        // set an error and continue rendering the form
        $login_error = 'Vul alle velden in.';
    } else {
        try {
            $user = new \App\User();
            $loggedInUser = $user->login($email, $password);

            if ($loggedInUser) {
                \App\Auth::login($loggedInUser);
                header('Location: website.php?page=home');
                exit;
            } else {
                $login_error = 'E-mailadres of wachtwoord is onjuist.';
            }
        } catch (\Exception $e) {
            $login_error = $e->getMessage();
        }
    }
}

// Prepare and handle register POST early as well
$register_error = '';
$token = $_GET['token'] ?? null;
$invitation = null;
$show_personal = false;
if ($token) {
    try {
        $invitationObj = new \App\Invitation();
        $invitation = $invitationObj->getByToken($token);
        if ($invitation) {
            $show_personal = true;
        }
    } catch (\Exception $e) {
        $register_error = 'Uitnodiging is niet geldig.';
    }
}

if ($page === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['first_name'])) {
    $email = trim($_POST['email'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $invite_token = $_POST['invite_token'] ?? null;

    if ($email === '' || $first_name === '' || $last_name === '' || $password === '') {
        $register_error = 'Alle velden zijn verplicht.';
        $show_personal = true;
    } elseif (!\App\ValidationHelper::validateEmail($email)) {
        $register_error = 'E-mailadres is niet geldig.';
        $show_personal = true;
    } elseif (!\App\ValidationHelper::validatePasswordsMatch($password, $password_confirm)) {
        $register_error = 'Wachtwoorden komen niet overeen.';
        $show_personal = true;
    } else {
        $passwordErrors = \App\ValidationHelper::validatePassword($password);
        if (!empty($passwordErrors)) {
            $register_error = $passwordErrors[0];
            $show_personal = true;
        } else {
            try {
                $userObj = new \App\User();
                $invitationObj = new \App\Invitation();
                $role = 'klant';

                if (!empty($invite_token)) {
                    $inv = $invitationObj->getByToken($invite_token);
                    if ($inv) {
                        $role = $inv['role'] ?: 'guest';
                        $userObj->register($email, $first_name, $last_name, $password, $role);
                        $invitationObj->markAsUsed($invite_token);
                    } else {
                        $register_error = 'Uitnodiging is niet meer geldig.';
                        $show_personal = true;
                    }
                } else {
                    $userObj->register($email, $first_name, $last_name, $password, $role);
                }

                if (empty($register_error)) {
                    $newUser = $userObj->login($email, $password);
                    if ($newUser) {
                        \App\Auth::login($newUser);
                        header('Location: website.php?page=home');
                        exit;
                    }
                }
            } catch (\Exception $e) {
                $register_error = $e->getMessage();
                $show_personal = true;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="FoTo Project">
    <meta name="description" content="Activiteiten boekingssysteem">
    <title><?php echo htmlspecialchars($title); ?> - FoTo Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --deepblack: #0B0B45;
            --textWhite: #ffffff;
            --tan: #D3B69C;
            --gold: #D4A574;
            --cream: #Faebd7;
        }
        
        body {
            background-color: var(--tan);
            color: #111111;
        }
        
        .primary-btn {
            background-color: var(--deepblack);
            color: var(--textWhite);
        }
        .primary-btn:hover {
            background-color: #1a1a5e;
        }
        
        .secondary-btn {
            background-color: var(--tan);
            color: #111111;
            border: 2px solid var(--deepblack);
        }
        .secondary-btn:hover {
            background-color: #c9a580;
        }
        
        .gold-btn {
            background-color: var(--gold);
            color: var(--textWhite);
        }
        .gold-btn:hover {
            background-color: #c9945a;
        }
        
        .card {
            background-color: var(--textWhite);
            border-radius: 0.5rem;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .weather-dropdown {
            max-width: 100%;
            max-height: 300px;
            overflow-y: auto;
        }
        
        .weather-dropdown.hidden {
            display: none;
        }
        
        .weather-item {
            border-color: var(--tan);
            transition: background-color 0.2s;
        }
        
        .weather-item:hover {
            background-color: rgba(212, 165, 116, 0.1);
        }
        
        .header {
            background-color: var(--deepblack);
            color: var(--textWhite);
        }
        
        .footer {
            background-color: var(--deepblack);
            color: var(--textWhite);
        }
        
        .nav-link {
            color: var(--textWhite);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            transition: background-color 0.3s;
        }
        
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }
        
        .nav-link.active {
            background-color: var(--gold);
            color: var(--deepblack);
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 0.375rem;
            font-size: 1rem;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--deepblack);
            box-shadow: 0 0 0 3px rgba(11, 11, 69, 0.1);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
        
        .alert-error {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }
        
        .alert-success {
            background-color: #efe;
            border: 1px solid #cfc;
            color: #3c3;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- HEADER / NAVIGATION -->
    <header class="header">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-3xl font-bold">
                    <a href="website.php" class="text-white no-underline">FoTo Project</a>
                </h1>
                <div class="flex items-center gap-4">
                    <?php if ($user_id): ?>
                        <span class="text-sm">Welkom, <strong><?php echo htmlspecialchars($user_name); ?></strong></span>
                        <span class="text-xs px-2 py-1 rounded bg-yellow-300 text-black">
                            <?php echo ucfirst($user_role); ?>
                        </span>
                        <a href="website.php?logout=1" class="text-sm underline hover:no-underline">Uitloggen</a>
                    <?php else: ?>
                        <span class="text-sm">Niet ingelogd</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- NAVIGATION MENU -->
            <nav class="flex flex-wrap gap-2">
                <a href="website.php?page=home" class="nav-link <?php echo $page === 'home' ? 'active' : ''; ?>">Home</a>
                
                <?php if (!$user_id): ?>
                    <a href="website.php?page=login" class="nav-link <?php echo $page === 'login' ? 'active' : ''; ?>">Inloggen</a>
                    <a href="website.php?page=register" class="nav-link <?php echo $page === 'register' ? 'active' : ''; ?>">Registreren</a>
                <?php else: ?>
                    <a href="website.php?page=bookings-overview" class="nav-link <?php echo $page === 'bookings-overview' ? 'active' : ''; ?>">Boekingen</a>
                    <a href="website.php?page=booking-indoor" class="nav-link <?php echo $page === 'booking-indoor' ? 'active' : ''; ?>">Binnen</a>
                    <a href="website.php?page=booking-outdoor" class="nav-link <?php echo $page === 'booking-outdoor' ? 'active' : ''; ?>">Buiten</a>
                    <a href="website.php?page=invite" class="nav-link <?php echo $page === 'invite' ? 'active' : ''; ?>">Uitnodigen</a>
                    <a href="website.php?page=participants" class="nav-link <?php echo $page === 'participants' ? 'active' : ''; ?>">Deelnemers</a>
                    
                    <?php if (in_array($user_role, ['admin', 'staff'])): ?>
                        <a href="website.php?page=admin" class="nav-link <?php echo $page === 'admin' ? 'active' : ''; ?>">Admin</a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-1 w-full py-8 px-4">
        <div class="max-w-4xl mx-auto">
            <!-- HOME PAGE -->
            <?php if ($page === 'home'): ?>
                <div class="card text-center">
                    <h2 class="text-4xl font-bold mb-4" style="color: var(--deepblack);">Welkom bij FoTo</h2>
                    <p class="text-gray-600 text-lg mb-6">
                        Een professioneel activiteitenboekingssysteem voor binnen- en buitenactiviteiten.
                    </p>
                    
                    <?php if ($user_id): ?>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                            <a href="website.php?page=bookings-overview" class="p-4 border-2 rounded-lg hover:shadow-lg transition">
                                <h3 class="font-bold mb-2" style="color: var(--deepblack);">Boekingen</h3>
                                <p class="text-sm text-gray-600">Bekijk alle boekingen</p>
                            </a>
                            <a href="website.php?page=booking-indoor" class="p-4 border-2 rounded-lg hover:shadow-lg transition">
                                <h3 class="font-bold mb-2" style="color: var(--deepblack);">Binnen Boeken</h3>
                                <p class="text-sm text-gray-600">Binnen activiteit reserveren</p>
                            </a>
                            <a href="website.php?page=booking-outdoor" class="p-4 border-2 rounded-lg hover:shadow-lg transition">
                                <h3 class="font-bold mb-2" style="color: var(--deepblack);">Buiten Boeken</h3>
                                <p class="text-sm text-gray-600">Buiten activiteit reserveren</p>
                            </a>
                        </div>

                        <!-- calendar container -->
                        <?php
                        // fetch bookings for display
                        $bookingObj = new \App\Booking();
                        $bookings = $bookingObj->getAll();
                        $bookingsJson = json_encode($bookings);
                        ?>
                        <div class="mt-10">
                            <h3 class="text-2xl font-bold mb-4" style="color: var(--deepblack);">Kalender</h3>
                            <div class="flex items-center justify-between mb-2">
                                <button id="prevMonth" class="px-3 py-1 bg-gray-200 rounded">&#9664;</button>
                                <span id="monthLabel" class="font-semibold"></span>
                                <button id="nextMonth" class="px-3 py-1 bg-gray-200 rounded">&#9654;</button>
                            </div>
                            <div id="calendar" class="w-full"></div>
                        </div>

                        <script>
                            const bookings = <?php echo $bookingsJson; ?>;
                            let currentYear = (new Date()).getFullYear();
                            let currentMonth = (new Date()).getMonth();

                            function renderCalendar(year, month) {
                                const cal = document.getElementById('calendar');
                                cal.innerHTML = '';
                                document.getElementById('monthLabel').textContent =
                                    new Date(year, month).toLocaleString('nl-NL', { month: 'long', year: 'numeric' });

                                const firstDay = new Date(year, month, 1).getDay();
                                const daysInMonth = new Date(year, month + 1, 0).getDate();
                                const table = document.createElement('table');
                                table.className = 'w-full table-fixed border-collapse';
                                const header = document.createElement('tr');
                                ['Zo','Ma','Di','Wo','Do','Vr','Za'].forEach(d => {
                                    const th = document.createElement('th');
                                    th.className = 'border p-2 bg-gray-100';
                                    th.textContent = d;
                                    header.appendChild(th);
                                });
                                table.appendChild(header);

                                let row = document.createElement('tr');
                                for (let i = 0; i < firstDay; i++) {
                                    const td = document.createElement('td');
                                    td.className = 'border p-2 h-24 align-top';
                                    row.appendChild(td);
                                }
                                for (let day = 1; day <= daysInMonth; day++) {
                                    if ((firstDay + day -1) % 7 === 0 && day !== 1) {
                                        table.appendChild(row);
                                        row = document.createElement('tr');
                                    }
                                    const td = document.createElement('td');
                                    td.className = 'border p-2 h-24 align-top';
                                    const dateKey = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                                    td.innerHTML = `<strong>${day}</strong>`;
                                    bookings.forEach(b => {
                                        if (b.datum && b.datum.startsWith(dateKey)) {
                                            const div = document.createElement('div');
                                            div.style.cssText = 'font-size:0.75rem; margin-top:0.25rem; padding:0.25rem; background-color: rgba(212,165,116,0.2); border-radius:0.25rem;';
                                            div.textContent = b.naam || b.activity_type;
                                            td.appendChild(div);
                                        }
                                    });
                                    row.appendChild(td);
                                }
                                while (row.children.length < 7) {
                                    const td = document.createElement('td');
                                    td.className = 'border p-2 h-24';
                                    row.appendChild(td);
                                }
                                table.appendChild(row);
                                cal.appendChild(table);
                            }

                            document.getElementById('prevMonth').addEventListener('click', () => {
                                currentMonth--;
                                if (currentMonth < 0) {
                                    currentMonth = 11;
                                    currentYear--;
                                }
                                renderCalendar(currentYear, currentMonth);
                            });
                            document.getElementById('nextMonth').addEventListener('click', () => {
                                currentMonth++;
                                if (currentMonth > 11) {
                                    currentMonth = 0;
                                    currentYear++;
                                }
                                renderCalendar(currentYear, currentMonth);
                            });

                            renderCalendar(currentYear, currentMonth);
                        </script>
                    <?php else: ?>
                        <div class="flex gap-4 justify-center">
                            <a href="website.php?page=login" class="primary-btn px-8 py-3 rounded-lg font-semibold">Inloggen</a>
                            <a href="website.php?page=register" class="gold-btn px-8 py-3 rounded-lg font-semibold">Registreren</a>
                        </div>
                    <?php endif; ?>
                </div>

            <!-- LOGIN PAGE -->
            <?php elseif ($page === 'login'): ?>
                <?php $error = $login_error ?? ''; ?>
                <div class="card max-w-md mx-auto">
                    <h2 class="text-3xl font-bold mb-2" style="color: var(--deepblack);">Inloggen</h2>
                    <p class="text-gray-600 mb-6">Welkom terug!</p>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">E-mailadres</label>
                            <input type="email" name="email" required class="form-input" placeholder="je@voorbeeld.com">
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium mb-2">Wachtwoord</label>
                            <input type="password" name="password" required class="form-input" placeholder="••••••••">
                        </div>
                        
                        <button type="submit" class="primary-btn w-full py-3 rounded-lg font-semibold">Inloggen</button>
                    </form>
                    
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">Nog geen account?</p>
                        <a href="website.php?page=register" class="text-sm font-semibold" style="color: var(--deepblack);">Registreer hier</a>
                    </div>
                </div>

            <!-- REGISTER PAGE -->
            <?php elseif ($page === 'register'): ?>
                <?php $error = $register_error ?? ''; ?>
                <div class="card max-w-md mx-auto">
                    <h2 class="text-3xl font-bold mb-2" style="color: var(--deepblack);">Registreren</h2>
                    <p class="text-gray-600 mb-6">Maak een nieuw account aan</p>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <!-- Email Step -->
                    <div id="step-email" class="<?php echo $show_personal ? 'hidden' : ''; ?>">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">E-mailadres</label>
                            <input id="email_step" type="email" class="form-input" placeholder="je@voorbeeld.com">
                        </div>
                        <button type="button" id="btn-email-next" class="primary-btn w-full py-3 rounded-lg font-semibold">Verder</button>
                        <p class="text-center text-sm text-gray-600 mt-4">
                            Al account? <a href="website.php?page=login" style="color: var(--deepblack); font-weight: bold;">Inloggen</a>
                        </p>
                    </div>
                    
                    <!-- Personal Details Step -->
                    <form id="step-personal" method="POST" class="<?php echo $show_personal ? '' : 'hidden'; ?>">
                        <input type="hidden" name="invite_token" value="<?php echo htmlspecialchars($token ?? ''); ?>">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">E-mailadres</label>
                            <input id="email_final" name="email" type="email" required class="form-input" value="<?php echo htmlspecialchars($invitation['email'] ?? ($_POST['email'] ?? '')); ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Voornaam</label>
                            <input name="first_name" type="text" required class="form-input" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Achternaam</label>
                            <input name="last_name" type="text" required class="form-input" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Wachtwoord</label>
                            <input name="password" type="password" required class="form-input">
                            <small class="text-gray-500 block mt-1">Min. 8 karakters, 2 hoofdletters, 1 kleine letter, 1 cijfer</small>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium mb-2">Wachtwoord bevestigen</label>
                            <input name="password_confirm" type="password" required class="form-input">
                        </div>
                        
                        <button type="submit" class="gold-btn w-full py-3 rounded-lg font-semibold">Registreren</button>
                    </form>
                </div>

            <!-- BOOKINGS OVERVIEW -->
            <?php elseif ($page === 'bookings-overview'): ?>
                <?php
                $bookingObj = new \App\Booking();
                $bookings = $bookingObj->getAll();
                usort($bookings, function($a, $b){
                    $ta = strtotime($a['created_at'] ?? 0);
                    $tb = strtotime($b['created_at'] ?? 0);
                    return $tb <=> $ta;
                });
                ?>
                <div class="card">
                    <h2 class="text-3xl font-bold mb-6" style="color: var(--deepblack);">Alle Boekingen</h2>
                    
                    <?php if (count($bookings) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr style="background-color: var(--deepblack); color: white;">
                                        <th class="px-4 py-3">Type</th>
                                        <th class="px-4 py-3">Naam</th>
                                        <th class="px-4 py-3">Datum</th>
                                        <th class="px-4 py-3">Gasten</th>
                                        <th class="px-4 py-3">Acties</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <span class="text-sm px-2 py-1 rounded" 
                                                      style="background-color: <?php echo $booking['activity_type'] === 'buiten' ? '#efe' : '#ffe'; ?>">
                                                    <?php echo $booking['activity_type'] === 'buiten' ? 'Buiten' : 'Binnen'; ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3"><?php echo htmlspecialchars($booking['naam']); ?></td>
                                            <td class="px-4 py-3"><?php echo htmlspecialchars($booking['datum']); ?></td>
                                            <td class="px-4 py-3"><?php echo $booking['gasten']; ?></td>
                                            <td class="px-4 py-3 text-sm">
                                                <form method="POST" action="delete_booking.php" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?php echo $booking['id']; ?>">
                                                    <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Zeker?');">Verwijderen</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-gray-600 py-8">Nog geen boekingen.</p>
                    <?php endif; ?>
                </div>

            <!-- INDOOR BOOKING -->
            <?php elseif ($page === 'booking-indoor'): ?>
                <div class="card">
                    <h2 class="text-3xl font-bold mb-6" style="color: var(--deepblack);">Binnen Activiteit Boeken</h2>
                    
                    <form action="save_booking.php" method="post" class="space-y-4">
                        <input type="hidden" name="activity_type" value="binnen">
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Naam *</label>
                            <input name="naam" type="text" required class="form-input" placeholder="Jan Jansen">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">E-mail *</label>
                                <input name="email" type="email" required class="form-input" placeholder="jan@voorbeeld.com">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Telefoon</label>
                                <input name="telefoon" type="tel" class="form-input" placeholder="06-12345678">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Datum *</label>
                                <input name="datum" type="date" required class="form-input">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Tijd *</label>
                                <input name="tijd" type="time" required class="form-input">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Gasten</label>
                                <input name="gasten" type="number" min="1" value="1" class="form-input">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Opmerkingen</label>
                            <textarea name="opmerkingen" rows="4" class="form-input" placeholder="Bijv. dieetwensen..."></textarea>
                        </div>
                        
                        <div class="flex gap-3">
                            <button type="submit" class="primary-btn px-6 py-3 rounded-lg font-semibold">Boeken</button>
                            <a href="website.php?page=home" class="secondary-btn px-6 py-3 rounded-lg font-semibold">Annuleren</a>
                        </div>
                    </form>
                </div>

            <!-- OUTDOOR BOOKING -->
            <?php elseif ($page === 'booking-outdoor'): ?>
                <div class="card">
                    <h2 class="text-3xl font-bold mb-6" style="color: var(--deepblack);">Buiten Activiteit Boeken</h2>
                    
                    <form action="save_booking.php" method="post" class="space-y-4">
                        <input type="hidden" name="activity_type" value="buiten">
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Naam *</label>
                            <input name="naam" type="text" required class="form-input" placeholder="Jan Jansen">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">E-mail *</label>
                                <input name="email" type="email" required class="form-input" placeholder="jan@voorbeeld.com">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Telefoon</label>
                                <input name="telefoon" type="tel" class="form-input" placeholder="06-12345678">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Datum *</label>
                                <input name="datum" type="date" required class="form-input">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Tijd *</label>
                                <input name="tijd" type="time" required class="form-input">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Gasten</label>
                                <input name="gasten" type="number" min="1" value="1" class="form-input">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Locatie</label>
                            <input name="locatie" type="text" id="plaatsInput" class="form-input" placeholder="Plaats">
                            <!-- Weather info display unter het plaats veld -->
                            <div id="weatherCard" style="margin-top: 1rem; padding: 1.5rem; background: linear-gradient(180deg, #Faebd7 0%, #D3B69C 100%); border-radius: 0.5rem; border-top: 4px solid #D4A574; display: none;">
                                <!-- Weather content goes here -->
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Opmerkingen</label>
                            <textarea name="opmerkingen" rows="4" class="form-input" placeholder="Bijv. parkeerinstructies..."></textarea>
                        </div>
                        
                        <div class="flex gap-3">
                            <button type="submit" class="primary-btn px-6 py-3 rounded-lg font-semibold">Boeken</button>
                            <a href="website.php?page=home" class="secondary-btn px-6 py-3 rounded-lg font-semibold">Annuleren</a>
                        </div>
                    </form>
                </div>
                
                <script>
                    const apiKey = 'c6908961755f935a67c1027e0be07b50';
                    const plaatsInput = document.getElementById('plaatsInput');
                    const weatherCard = document.getElementById('weatherCard');
                    let weatherTimeout;

                    // Trigger weather lookup when typing in plaats field
                    plaatsInput.addEventListener('input', async event => {
                        clearTimeout(weatherTimeout);
                        const plaats = plaatsInput.value.trim();

                        if (plaats.length > 2) {
                            // Wait 500ms after user stops typing before fetching
                            weatherTimeout = setTimeout(async () => {
                                try {
                                    const weatherData = await getWeatherData(plaats);
                                    displayWeatherInfo(weatherData);
                                } catch (error) {
                                    console.error(error);
                                    // Silent fail - don't show error if city not found
                                    weatherCard.style.display = 'none';
                                }
                            }, 500);
                        } else {
                            weatherCard.style.display = 'none';
                        }
                    });

                    async function getWeatherData(city) {
                        const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}`;
                        const response = await fetch(apiUrl);

                        if (!response.ok) {
                            throw new Error("Kon weergegevens niet ophalen");
                        }

                        return await response.json();
                    }

                    function displayWeatherInfo(data) {
                        const { name: city, main: { temp, humidity }, weather: [{ description, id }] } = data;

                        weatherCard.innerHTML = "";
                        weatherCard.style.cssText = "margin-top: 1rem; padding: 1.5rem; background: linear-gradient(180deg, #Faebd7 0%, #D3B69C 100%); border-radius: 0.5rem; border-top: 4px solid #D4A574; display: block; text-align: center;";

                        const cityDisplay = document.createElement("h3");
                        const tempDisplay = document.createElement("p");
                        const humidityDisplay = document.createElement("p");
                        const descDisplay = document.createElement("p");
                        const weatherEmoji = document.createElement("p");

                        cityDisplay.textContent = city;
                        tempDisplay.textContent = `${(temp - 273.15).toFixed(1)}°C`;
                        humidityDisplay.textContent = `Vochtigheid: ${humidity}%`;
                        descDisplay.textContent = description.charAt(0).toUpperCase() + description.slice(1);
                        weatherEmoji.textContent = getWeatherEmoji(id);

                        cityDisplay.style.cssText = "font-size: 1.5rem; font-weight: bold; color: #0B0B45; margin: 0 0 0.5rem 0;";
                        tempDisplay.style.cssText = "font-size: 1.25rem; font-weight: bold; color: #0B0B45; margin: 0.25rem 0;";
                        humidityDisplay.style.cssText = "font-size: 0.95rem; color: #0B0B45; margin: 0.25rem 0;";
                        descDisplay.style.cssText = "font-size: 1rem; font-style: italic; color: #0B0B45; margin: 0.5rem 0 0.5rem 0;";
                        weatherEmoji.style.cssText = "font-size: 3rem; margin: 0.5rem 0 0 0; line-height: 1;";

                        weatherCard.appendChild(cityDisplay);
                        weatherCard.appendChild(weatherEmoji);
                        weatherCard.appendChild(tempDisplay);
                        weatherCard.appendChild(humidityDisplay);
                        weatherCard.appendChild(descDisplay);
                    }

                    function getWeatherEmoji(weatherId) {
                        switch (true) {
                            case (weatherId >= 200 && weatherId < 300):
                                return "⛈";
                            case (weatherId >= 300 && weatherId < 400):
                                return "🌧";
                            case (weatherId >= 500 && weatherId < 600):
                                return "🌧";
                            case (weatherId >= 600 && weatherId < 700):
                                return "❄";
                            case (weatherId >= 700 && weatherId < 800):
                                return "🌫";
                            case (weatherId === 800):
                                return "☀";
                            case (weatherId >= 801 && weatherId < 810):
                                return "☁";
                            default:
                                return "❓";
                        }
                    }
                </script>

            <!-- INVITE PAGE -->
            <?php elseif ($page === 'invite'): ?>
                <?php
                $error = '';
                $success = '';
                $link = '';
                
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $email = trim($_POST['email'] ?? '');
                    
                    if ($email === '' || !\App\ValidationHelper::validateEmail($email)) {
                        $error = 'Geef een geldig e-mailadres op.';
                    } else {
                        try {
                            $invitationObj = new \App\Invitation();
                            $invData = $invitationObj->create($email, \App\Auth::getUserId(), 'guest');
                            $link = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/FoTo-project/website.php?page=register&token=' . $invData['token'];
                            $success = 'Uitnodiging aangemaakt!';
                        } catch (\Exception $e) {
                            $error = $e->getMessage();
                        }
                    }
                }
                ?>
                <div class="card max-w-md mx-auto">
                    <h2 class="text-2xl font-bold mb-6" style="color: var(--deepblack);">Nodig een Gast Uit</h2>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">E-mailadres gast</label>
                            <input name="email" type="email" required class="form-input" placeholder="gast@voorbeeld.com">
                        </div>
                        <button type="submit" class="primary-btn w-full py-3 rounded-lg font-semibold">Uitnodiging maken</button>
                    </form>
                    
                    <?php if ($link): ?>
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <label class="block text-sm font-medium mb-2">Uitnodigingslink (kopieer):</label>
                            <input readonly value="<?php echo htmlspecialchars($link); ?>" class="form-input text-xs">
                            <small class="text-gray-600 block mt-2">Stuur deze link naar de gast</small>
                        </div>
                    <?php endif; ?>
                </div>

            <!-- PARTICIPANTS PAGE -->
            <?php elseif ($page === 'participants'): ?>
                <?php
                $userObj = new \App\User();
                $users = $userObj->getAll();
                ?>
                <div class="card">
                    <h2 class="text-3xl font-bold mb-6" style="color: var(--deepblack);">Deelnemers</h2>
                    
                    <?php if (count($users) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr style="background-color: var(--deepblack); color: white;">
                                        <th class="px-4 py-3">Naam</th>
                                        <th class="px-4 py-3">E-mail</th>
                                        <th class="px-4 py-3">Rol</th>
                                        <th class="px-4 py-3">Geregistreerd</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $u): ?>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3"><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></td>
                                            <td class="px-4 py-3"><?php echo htmlspecialchars($u['email']); ?></td>
                                            <td class="px-4 py-3">
                                                <?php
                                                $roles = [
                                                    'admin' => 'Admin',
                                                    'staff' => 'Medewerker',
                                                    'klant' => 'Klant',
                                                    'guest' => 'Gast'
                                                ];
                                                $role_info = $roles[$u['role']] ?? ucfirst($u['role']);
                                                ?>
                                                <span><?php echo $role_info; ?></span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600"><?php echo date('d-m-Y', strtotime($u['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-gray-600 py-8">Geen gebruikers gevonden.</p>
                    <?php endif; ?>
                </div>

            <!-- ADMIN PAGE -->
            <?php elseif ($page === 'admin'): ?>
                <?php
                $error = '';
                $success = '';
                $action = $_GET['action'] ?? null;
                
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'change_role') {
                    $user_id = $_POST['user_id'] ?? null;
                    $new_role = $_POST['role'] ?? null;
                    
                    if ($user_id && $new_role && in_array($new_role, ['admin', 'staff', 'klant', 'guest'])) {
                        try {
                            $userObj = new \App\User();
                            $userObj->updateRole((int)$user_id, $new_role);
                            $success = 'Rol succesvol gewijzigd!';
                        } catch (\Exception $e) {
                            $error = $e->getMessage();
                        }
                    }
                }
                
                $userObj = new \App\User();
                $users = $userObj->getAll();
                ?>
                <div class="card">
                    <h2 class="text-3xl font-bold mb-2" style="color: var(--deepblack);">Admin Paneel</h2>
                    <p class="text-gray-600 mb-6">Beheer gebruikers en hun rollen</p>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr style="background-color: var(--deepblack); color: white;">
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Voornaam</th>
                                    <th class="px-4 py-3">Achternaam</th>
                                    <th class="px-4 py-3">E-mail</th>
                                    <th class="px-4 py-3">Rol</th>
                                    <th class="px-4 py-3">Geregistreerd</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm"><?php echo $user['id']; ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($user['first_name']); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($user['last_name']); ?></td>
                                        <td class="px-4 py-3 text-sm"><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td class="px-4 py-3">
                                            <form method="POST" action="website.php?page=admin&action=change_role" class="flex gap-2">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <select name="role" class="form-input py-1 text-sm" onchange="this.form.submit()">
                                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                    <option value="staff" <?php echo $user['role'] === 'staff' ? 'selected' : ''; ?>>Medewerker</option>
                                                    <option value="klant" <?php echo $user['role'] === 'klant' ? 'selected' : ''; ?>>Klant</option>
                                                    <option value="guest" <?php echo $user['role'] === 'guest' ? 'selected' : ''; ?>>Gast</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo date('d-m-Y', strtotime($user['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- 404 PAGE -->
            <?php else: ?>
                <div class="card text-center">
                    <h2 class="text-3xl font-bold mb-4" style="color: var(--deepblack);">Pagina niet gevonden</h2>
                    <p class="text-gray-600 mb-6">De pagina die je zoekt bestaat niet.</p>
                    <a href="website.php?page=home" class="primary-btn px-6 py-3 rounded-lg font-semibold inline-block">Terug naar Home</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="footer mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-sm">
            <p>&copy; <?php echo date('Y'); ?> FoTo Project — Activiteitenboekingssysteem</p>
            <p class="text-xs mt-2 opacity-75">Alle rechten voorbehouden</p>
        </div>
    </footer>

    <!-- JAVASCRIPT -->
    <script>
        // Email to personal step
        document.getElementById('btn-email-next')?.addEventListener('click', function(){
            const email = document.getElementById('email_step').value.trim();
            if (!email) { alert('Vul een e-mailadres in.'); return; }
            const re = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
            if (!re.test(email)) { alert('Vul een geldig e-mailadres in.'); return; }
            
            document.getElementById('email_final').value = email;
            document.getElementById('step-email').classList.add('hidden');
            document.getElementById('step-personal').classList.remove('hidden');
            document.querySelector('input[name="first_name"]')?.focus();
        });
        
        // Allow Enter key on email step
        document.getElementById('email_step')?.addEventListener('keypress', function(e){
            if (e.key === 'Enter') {
                document.getElementById('btn-email-next').click();
            }
        });
    </script>
    <?php if (function_exists('ob_end_flush')) { @ob_end_flush(); } ?>
</body>
</html>
