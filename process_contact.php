<?php
/**
 * Receives the contact form. No HTML here: validate, save, redirect.
 */
require_once __DIR__ . '/includes/forms.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('contact.php');
}

if (!verify_csrf()) {
    set_flash('danger', 'Your session expired. Please submit the form again.');
    redirect('contact.php');
}

// Spam trap
if (!empty($_POST['website'])) {
    set_flash('success', 'Thank you. We will be in touch.');
    redirect('index.php');
}

if (too_many_requests('contact_messages', 5)) {
    set_flash('warning', 'You have sent several messages recently. Please call us on ' . setting('phone') . '.');
    redirect('contact.php');
}

$data = [
    'name'    => clean($_POST['name'] ?? ''),
    'phone'   => clean($_POST['phone'] ?? ''),
    'email'   => clean($_POST['email'] ?? ''),
    'subject' => clean($_POST['subject'] ?? ''),
    'message' => clean($_POST['message'] ?? ''),
];

$errors = [];

if ($data['name'] === '' || mb_strlen($data['name']) > 120) {
    $errors[] = 'Please enter your name.';
}
if ($data['phone'] !== '' && !preg_match('/^\+?[0-9\s-]{7,20}$/', $data['phone'])) {
    $errors[] = 'Please enter a valid phone number.';
}
if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'The email address is not valid.';
}
if ($data['phone'] === '' && $data['email'] === '') {
    $errors[] = 'Please give us a phone number or an email so we can reply.';
}
if (mb_strlen($data['subject']) > 200) {
    $errors[] = 'The subject is too long.';
}
if (mb_strlen($data['message']) < 10 || mb_strlen($data['message']) > 3000) {
    $errors[] = 'Your message must be between 10 and 3000 characters.';
}

if ($errors) {
    set_old($data);
    set_errors($errors);
    redirect('contact.php');
}

$stmt = db()->prepare(
    'INSERT INTO contact_messages (name, email, phone, subject, message, ip_address)
     VALUES (?, ?, ?, ?, ?, ?)'
);
$stmt->execute([
    $data['name'],
    $data['email'] !== '' ? $data['email'] : null,
    $data['phone'] !== '' ? $data['phone'] : null,
    $data['subject'] !== '' ? $data['subject'] : null,
    $data['message'],
    client_ip(),
]);

send_notification(
    'New website message from ' . $data['name'],
    "Name: {$data['name']}\nPhone: {$data['phone']}\nEmail: {$data['email']}\n"
    . "Subject: {$data['subject']}\n\n{$data['message']}\n"
);

set_flash('success', 'Thank you, ' . $data['name'] . '. Your message has been sent and we will reply soon.');
redirect('contact.php');