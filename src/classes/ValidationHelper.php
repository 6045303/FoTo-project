<?php

namespace App;

/**
 * ValidationHelper Class
 * Provides common validation methods
 */
class ValidationHelper
{
    /**
     * Validate email format
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password strength
     */
    public static function validatePassword(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Wachtwoord moet minimaal 8 karakters lang zijn.';
        }

        // Require at least 2 uppercase letters to match backend rules
        if (!preg_match('/[A-Z].*[A-Z]/', $password)) {
            $errors[] = 'Wachtwoord moet minimaal 2 hoofdletters bevatten.';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Wachtwoord moet minimaal 1 kleine letter bevatten.';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Wachtwoord moet minimaal 1 cijfer bevatten.';
        }

        return $errors;
    }

    /**
     * Validate date format
     */
    public static function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Validate phone number (basic)
     */
    public static function validatePhone(string $phone): bool
    {
        return preg_match('/^[+]?[(]?[0-9]{0,3}[)]?[-\s\.]?[0-9]{0,10}$/', $phone) === 1;
    }

    /**
     * Sanitize text input
     */
    public static function sanitizeText(string $text): string
    {
        return trim(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Validate required fields
     */
    public static function validateRequired(array $fields): array
    {
        $errors = [];

        foreach ($fields as $fieldName => $fieldValue) {
            if (empty($fieldValue)) {
                $errors[] = ucfirst($fieldName) . ' is verplicht.';
            }
        }

        return $errors;
    }

    /**
     * Validate passwords match
     */
    public static function validatePasswordsMatch(string $password1, string $password2): bool
    {
        return $password1 === $password2;
    }
}
