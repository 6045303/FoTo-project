# Migratie & Refactoring Guide

## 🔄 Wat is veranderd?

Dit project is volledig gerefactord van legacy declarative style naar **Object-Oriented PHP** met proper architecture.

### Oude Structuur
```
├── db.php                    # Direct database functions
├── class.php                 # Empty placeholder
├── register_send_to_db.php  # Direct DB manipulation
├── save_booking.php         # Direct DB manipulation
├── delete_booking.php       # Direct DB manipulation
└── index.php                # Inline queries
```

### Nieuwe Structuur
```
src/classes/
├── Database.php             # Singleton DB management
├── User.php                 # User operations
├── Invitation.php           # Invitations
├── Booking.php              # Bookings
├── Auth.php                 # Session management
└── ValidationHelper.php     # Validation logic
```

## 📝 Wijzigingen per File

### ✅ init.php
**Voor:**
```php
global $db;
$db = new PDO(...);
function get_db() { global $db; return $db; }
```

**Na:**
```php
spl_autoload_register(...);  // Autoloader
\App\Auth::startSession();
\App\Database::getInstance(); // Singleton
```

### ✅ login.php
**Voor:**
```php
$stmt = $db->prepare("SELECT ... FROM users WHERE email = ?");
if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
}
```

**Na:**
```php
$user = new \App\User();
$loggedInUser = $user->login($email, $password);
\App\Auth::login($loggedInUser);
```

### ✅ register.php
**Voor:**
```php
$password_hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $db->prepare('INSERT INTO users ...');
$_SESSION['user_id'] = $db->lastInsertId();
```

**Na:**
```php
$user = new \App\User();
$user->register($email, $firstName, $lastName, $password);
$loggedInUser = $user->login($email, $password);
\App\Auth::login($loggedInUser);
```

### ✅ invite.php
**Voor:**
```php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
}
$token = bin2hex(random_bytes(16));
$stmt = $db->prepare('INSERT INTO invitations ...');
```

**Na:**
```php
\App\Auth::requireLogin();
$invitation = new \App\Invitation();
$invData = $invitation->create($email, \App\Auth::getUserId());
```

### ✅ admin.php
**Voor:**
```php
if (!isset($_SESSION['user_id']) || !in_array(...)) {
    echo 'Access denied';
    return;
}
$stmt = $db->prepare('UPDATE users SET role = ?');
```

**Na:**
```php
\App\Auth::requireAdmin();
$user = new \App\User();
$user->updateRole($userId, $newRole);
```

### ✅ participants.php
**Voor:**
```php
$stmt = $db->query('SELECT ... FROM users');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

**Na:**
```php
$user = new \App\User();
$users = $user->getAll();
```

### ✅ save_booking.php
**Voor:**
```php
if (table_exists($mysqli, 'bookings')) {
    $stmt = $mysqli->prepare("INSERT INTO bookings ...");
    $stmt->bind_param(...);
}
```

**Na:**
```php
$booking = new \App\Booking();
$booking->create($data);
```

### ✅ delete_booking.php
**Voor:**
```php
if (table_exists($mysqli, 'bookings')) {
    $stmt = $mysqli->prepare('DELETE FROM bookings WHERE id = ?');
}
```

**Na:**
```php
$booking = new \App\Booking();
$booking->delete($id);
```

### ✅ index.php (root)
**Voor:**
```php
if (table_exists($mysqli, 'bookings')) {
    $res = $mysqli->query("SELECT ... FROM bookings");
    while ($row = $res->fetch_assoc()) { ... }
}
```

**Na:**
```php
$booking = new \App\Booking();
$bookings = $booking->getAll();
```

## 🗑️ Verwijderde/verouderde Files

- `register_send_to_db.php` - **Niet meer nodig** (replaced by User::register())
- `class.php` - **Was leeg**, nu hebben we `src/classes/`

## 🎯 Voordelen van Refactoring

| Feature | Voor | Na |
|---------|------|-----|
| Database Access | Direct in files | Singleton pattern |
| Authentication | $_SESSION globals | Auth helper class |
| User Management | Spread across files | User class |
| Validation | Inline check-code | ValidationHelper |
| Error Handling | Silenced errors | Exceptions |
| Code Reuse | Copy-paste | Class methods |
| Testing | Moeizaam | Unit testable |
| Maintainability | Hoog | Laag |

## 🔄 Migratie Checklist voor Toekomst

Wanneer je nieuwe features toevoegt:

- [ ] Create method in appropriate class (`User`, `Booking`, etc.)
- [ ] Use `\App\ClassName` namespace
- [ ] Add error handling with try-catch
- [ ] Use ValidationHelper voor input checks
- [ ] Add PHPDoc comments above methods
- [ ] Test with sample data

## 💡 Best Practices

### ✅ DOE
```php
// ✅ Good
$user = new \App\User();
$user->register($email, $firstName, $lastName, $password);
```

### ❌ NIET
```php
// ❌ Bad
global $db;
$stmt = $db->prepare("INSERT INTO users ...");
$hash = password_hash($password, PASSWORD_BCRYPT);
```

## 🔗 Gerelateerde documenten

- [README.md](README.md) - Setup & usage
- [STRUCTURE.md](STRUCTURE.md) - OOP architecture details
- [Classes overview](#) - Class documentation

## 📞 Vragen?

Raadpleeg STRUCTURE.md of check comments in individual class files.

---

**Last Updated:** 2024
**Status:** ✅ Complete Refactoring
