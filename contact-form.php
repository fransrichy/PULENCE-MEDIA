<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set response header to JSON
header('Content-Type: application/json');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get form data
$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
$company = filter_var($_POST['company'], FILTER_SANITIZE_STRING);
$service = filter_var($_POST['service'], FILTER_SANITIZE_STRING);
$budget = filter_var($_POST['budget'], FILTER_SANITIZE_STRING);
$message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

// Validate required fields
if (empty($name) || empty($email) || empty($service) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

// Email configuration
$to_info = 'info@opulencemedia.com'; // General inquiries
$to_projects = 'projects@opulencemedia.com'; // Project inquiries

// Determine which email to use based on service type
$to = (in_array($service, ['digital-media', 'motion-graphics', 'photography', 'branding', 'graphic-design', 'web-design', 'print-services'])) 
    ? $to_projects 
    : $to_info;

$subject = "New Contact Form Submission - " . htmlspecialchars($service);
$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

// Create email body
$email_body = "
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .header { background: #FF7A00; color: white; padding: 20px; }
        .content { padding: 20px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #FF7A00; }
    </style>
</head>
<body>
    <div class='header'>
        <h2>New Contact Form Submission</h2>
        <p>From: " . htmlspecialchars($name) . "</p>
    </div>
    <div class='content'>
        <div class='field'>
            <span class='label'>Name:</span> " . htmlspecialchars($name) . "
        </div>
        <div class='field'>
            <span class='label'>Email:</span> " . htmlspecialchars($email) . "
        </div>
        <div class='field'>
            <span class='label'>Phone:</span> " . htmlspecialchars($phone) . "
        </div>
        <div class='field'>
            <span class='label'>Company:</span> " . htmlspecialchars($company) . "
        </div>
        <div class='field'>
            <span class='label'>Service:</span> " . htmlspecialchars($service) . "
        </div>
        <div class='field'>
            <span class='label'>Budget:</span> " . htmlspecialchars($budget) . "
        </div>
        <div class='field'>
            <span class='label'>Message:</span><br>
            " . nl2br(htmlspecialchars($message)) . "
        </div>
    </div>
</body>
</html>
";

// Send email
if (mail($to, $subject, $email_body, $headers)) {
    // Also send a confirmation email to the user
    $user_subject = "Thank you for contacting Opulence Media";
    $user_headers = "From: info@opulencemedia.com\r\n";
    $user_headers .= "MIME-Version: 1.0\r\n";
    $user_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    $user_email_body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .header { background: #FF7A00; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .footer { background: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>Thank You for Contacting Opulence Media</h2>
        </div>
        <div class='content'>
            <p>Dear " . htmlspecialchars($name) . ",</p>
            <p>Thank you for your interest in Opulence Media. We have received your inquiry regarding our <strong>" . htmlspecialchars($service) . "</strong> services and will get back to you within 24 hours.</p>
            <p>In the meantime, feel free to explore our <a href='https://opulencemedia.com/portfolio'>portfolio</a> to see examples of our work.</p>
            <p>Best regards,<br>The Opulence Media Team</p>
        </div>
        <div class='footer'>
            <p>Opulence Media | 788 Samuel Maharero St, Academia, Windhoek | +264 85 561 6423</p>
        </div>
    </body>
    </html>
    ";
    
    mail($email, $user_subject, $user_email_body, $user_headers);
    
    echo json_encode(['success' => true, 'message' => 'Thank you for your message! We will get back to you within 24 hours.']);
} else {
    echo json_encode(['success' => false, 'message' => 'There was an error sending your message. Please try again or contact us directly at info@opulencemedia.com.']);
}
?>