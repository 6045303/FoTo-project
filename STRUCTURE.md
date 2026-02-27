# FoTo Project - OOP Refactoring

## Huidige Structuur

Alle PHP classes zijn nu georganiseerd in de `src/classes/` directory. Dit maakt het project onderhoudsvriendelijk en schaalbaar.

```
FoTo-project/
├── src/
│   ├── classes/              # OOP Classes
│   │   ├── Database.php      # Database connection & operations
│   │   ├── User.php          # User authentication & management
│   │   ├── Invitation.php    # Invitation management
│   │   ├── Booking.php       # Activity bookings
│   │   ├── Auth.php          # Session & authentication helper
│   │   └── ValidationHelper.php # Input validation
│   ├── PHP/
│   │   ├── init.php          # Bootstrap & autoloader
│   │   ├── index.php         # Main routing
│   │   ├── login.php         # Login page
│   │   ├── register.php      # Registration page
│   │   ├── invite.php        # Invitation creation
│   │   ├── participants.php  # Participants list
│   │   ├── admin.php         # Admin panel
│   │   └── partials/         # HTML snippets
│   └── data/                 # SQLite database (fallback)
├── db/
│   └── db.php                # MySQL configuration
├── index.php                 # Root booking overview
├── save_booking.php          # Process bookings
├── delete_booking.php        # Delete bookings
├── buiten_activiteit.php     # Outdoor activity form
├── binnen_activiteit.php     # Indoor activity form
└── README.md
```

## Classes Beschrijving

### Database.php
**Singleton pattern** voor database connections. Ondersteunt MySQL en SQLite (fallback).
```php
$db = \App\Database::getInstance();
```

### User.php
Handles user authentication, registration, en role management.
```php
$user = new \App\User();
$user->register($email, $firstName, $lastName, $password);
$loggedInUser = $user->login($email, $password);
$user->updateRole($userId, 'admin');
```

### Invitation.php
Manages invitation creation en validation.
```php
$invitation = new \App\Invitation();
$result = $invitation->create($email, $inviterId, 'guest');
$invitation->markAsUsed($token);
```

### Booking.php
Handles activity bookings (binnen & buiten).
```php
$booking = new \App\Booking();
$booking->create($data);
$bookings = $booking->getAll();
$booking->delete($id);
```

### Auth.php
Session en authentication helper methods.
```php
\App\Auth::startSession();
\App\Auth::login($user);
\App\Auth::requireLogin();
\App\Auth::isAdmin();
```

### ValidationHelper.php
Input validation utilities.
```php
\App\ValidationHelper::validateEmail($email);
\App\ValidationHelper::validatePassword($password);
\App\ValidationHelper::validatePasswordsMatch($p1, $p2);
```

## Autoloader

In `init.php` is een autoloader gedefinieerd:
```php
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $file = __DIR__ . '/../classes/' . str_replace('App\\', '', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
```

## Usage Pattern

### Alle PHP files starten met:
```php
<?php
require __DIR__ . '/init.php';  // In src/PHP/
// of
require_once __DIR__ . '/src/PHP/init.php';  // In root
```

### Classes gebruiken:
```php
$user = new \App\User();
$booking = new \App\Booking();
$invitation = new \App\Invitation();

\App\Auth::login($userData);
\App\ValidationHelper::validateEmail($email);
```

## Database Support

### MySQL
Zet configuration in `db/db.php`:
```php
$pdo = new PDO("mysql:host=localhost;dbname=foto_project;charset=utf8mb4", "root", "");
```

### SQLite (Fallback)
Automatisch: `src/data/users.db`

## Voordelen OOP

✅ **Beter onderhoud**: Classes scheiden concerns
✅ **Reusability**: Methods kunnen overal gebruikt worden  
✅ **Type Safety**: Explicit method signatures
✅ **Error Handling**: Exceptions voor betere error handling
✅ **Scalability**: Makkelijker om features toe te voegen
✅ **Testing**: Classes kunnen unit tested worden

## Migratie van oud code

- ✅ `login.php` → gebruikt `\App\User` en `\App\Auth`
- ✅ `register.php` → gebruikt `\App\User` en `\App\Invitation`
- ✅ `admin.php` → gebruikt `\App\User` en `\App\Auth`
- ✅ `invite.php` → gebruikt `\App\Invitation`
- ✅ `participants.php` → gebruikt `\App\User`
- ✅ `save_booking.php` → gebruikt `\App\Booking`
- ✅ `delete_booking.php` → gebruikt `\App\Booking`
- ✅ `index.php` (root) → gebruikt `\App\Booking`
