<?php
/**
 * Form helpers: remembered input, error lists, spam limits, email alerts.
 */
require_once __DIR__ . '/functions.php';

/* Options for the "event type" dropdown */
function event_types(): array
{
    return [
        'Wedding', 'Birthday', 'Burial / Funeral', 'Corporate Event',
        'Naming Ceremony', 'Anniversary', 'Graduation', 'Church Event', 'Other',
    ];
}

function client_ip(): string
{
    return substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45);
}

/* Remember what the visitor typed when a form fails validation */
function set_old(array $data): void
{
    $_SESSION['old'] = $data;
}

function pull_old(): array
{
    $old = $_SESSION['old'] ?? [];
    unset($_SESSION['old']);
    return $old;
}

/* Error list shown above the form */
function set_errors(array $errors): void
{
    $_SESSION['form_errors'] = $errors;
}

function pull_errors(): array
{
    $errors = $_SESSION['form_errors'] ?? [];
    unset($_SESSION['form_errors']);
    return $errors;
}

/* True if this IP already sent $max forms in the last hour */
function too_many_requests(string $table, int $max = 5): bool
{
    if (!in_array($table, ['bookings', 'contact_messages'], true)) {
        return false;
    }
    $stmt = db()->prepare(
        "SELECT COUNT(*) FROM $table
          WHERE ip_address = ? AND created_at > (NOW() - INTERVAL 1 HOUR)"
    );
    $stmt->execute([client_ip()]);
    return (int) $stmt->fetchColumn() >= $max;
}

/* Optional email alert to the owner (only if an email is saved in settings) */
function send_notification(string $subject, string $body): bool
{
    $to = setting('email');
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $subject = str_replace(["\r", "\n"], ' ', $subject); // block header injection
    $host    = parse_url(BASE_URL, PHP_URL_HOST) ?: 'localhost';
    $headers = "From: no-reply@{$host}\r\nContent-Type: text/plain; charset=UTF-8";

    return (bool) @mail($to, $subject, $body, $headers);
}