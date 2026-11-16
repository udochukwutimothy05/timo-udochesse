<?php
/**
 * Receives the booking form. No HTML here: validate, save, redirect.
 */
require_once __DIR__ . '/includes/forms.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('book.php');
}

// 1. Security check
if (!verify_csrf()) {
    set_flash('danger', 'Your session expired. Please submit the form again.');
    redirect('book.php');
}

// 2. Spam trap: bots fill the hidden field. Pretend it worked and drop it.
if (!empty($_POST['website'])) {
    set_flash('success', 'Thank you. We will be in touch.');
    redirect('index.php');
}

// 3. Too many submissions from one IP
if (too_many_requests('bookings', 5)) {
    set_flash('warning', 'You have sent several requests recently. Please call us on ' . setting('phone') . '.');
    redirect('book.php');
}

$pdo = db();

// 4. Collect and clean input
$allowedServices = $pdo->query('SELECT title FROM services WHERE is_active = 1')->fetchAll(PDO::FETCH_COLUMN);
$postedServices  = array_filter((array) ($_POST['services'] ?? []), 'is_string');
$services        = array_values(array_intersect($postedServices, $allowedServices));

$data = [
    'full_name'      => clean($_POST['full_name'] ?? ''),
    'phone'          => clean($_POST['phone'] ?? ''),
    'email'          => clean($_POST['email'] ?? ''),
    'event_type'     => clean($_POST['event_type'] ?? ''),
    'event_date'     => clean($_POST['event_date'] ?? ''),
    'guest_count'    => clean($_POST['guest_count'] ?? ''),
    'event_location' => clean($_POST['event_location'] ?? ''),
    'message'        => clean($_POST['message'] ?? ''),
    'services'       => $services,
];

// 5. Validate (the server is the real check; browser validation is only a convenience)
$errors = [];

if ($data['full_name'] === '' || mb_strlen($data['full_name']) > 120) {
    $errors[] = 'Please enter your full name.';
}

if (!preg_match('/^\+?[0-9\s-]{7,20}$/', $data['phone'])) {
    $errors[] = 'Please enter a valid phone number.';
}

if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'The email address is not valid.';
}

if (!in_array($data['event_type'], event_types(), true)) {
    $errors[] = 'Please choose a type of event.';
}

$date = DateTime::createFromFormat('Y-m-d', $data['event_date']);
if (!$date || $date->format('Y-m-d') !== $data['event_date']) {
    $errors[] = 'Please choose a valid event date.';
} elseif ($data['event_date'] < date('Y-m-d')) {
    $errors[] = 'The event date cannot be in the past.';
}

$guests = filter_var($data['guest_count'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100000]]);
if ($guests === false) {
    $errors[] = 'Please enter a valid number of guests.';
}

if ($data['event_location'] === '' || mb_strlen($data['event_location']) > 255) {
    $errors[] = 'Please tell us the event location.';
}

if (mb_strlen($data['message']) > 2000) {
    $errors[] = 'Your extra details are too long (maximum 2000 characters).';
}

if ($errors) {
    set_old($data);
    set_errors($errors);
    redirect('book.php');
}

// 6. Save (retry if the random reference happens to clash)
$stmt = $pdo->prepare(
    'INSERT INTO bookings
        (reference, full_name, phone, email, event_type, event_date, guest_count,
         event_location, services_needed, message, ip_address)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);

$reference = '';
$saved     = false;

for ($attempt = 0; $attempt < 5 && !$saved; $attempt++) {
    $reference = booking_reference();
    try {
        $stmt->execute([
            $reference,
            $data['full_name'],
            $data['phone'],
            $data['email'] !== '' ? $data['email'] : null,
            $data['event_type'],
            $data['event_date'],
            $guests,
            $data['event_location'],
            $services ? implode(', ', $services) : null,
            $data['message'] !== '' ? $data['message'] : null,
            client_ip(),
        ]);
        $saved = true;
    } catch (PDOException $e) {
        if ($e->getCode() !== '23000') { // 23000 = duplicate key, anything else is a real error
            throw $e;
        }
    }
}

if (!$saved) {
    set_old($data);
    set_flash('danger', 'Sorry, we could not save your request. Please call us on ' . setting('phone') . '.');
    redirect('book.php');
}

// 7. Optional email alert (works only if an email is saved in settings and mail is set up)
send_notification(
    "New quote request $reference",
    "Reference: $reference\n"
    . "Name: {$data['full_name']}\n"
    . "Phone: {$data['phone']}\n"
    . "Email: {$data['email']}\n"
    . "Event: {$data['event_type']} on {$data['event_date']}\n"
    . "Guests: $guests\n"
    . "Location: {$data['event_location']}\n"
    . 'Needs: ' . implode(', ', $services) . "\n"
    . "Message: {$data['message']}\n"
);

// 8. Done
$_SESSION['booking_success'] = ['ref' => $reference, 'name' => $data['full_name']];
redirect('book.php');