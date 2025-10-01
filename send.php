<?php
// send.php - simple JSON email gateway for АвтоВердикт
header('Content-Type: application/json; charset=utf-8');

// SECURITY TIP: consider setting an allowed origin, captcha, rate-limit, etc.

$to = "auto.verdict24@gmail.com";
$subject = "Заявка с сайта АвтоВердикт";

// Read JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);
if (!$data) { http_response_code(400); echo json_encode(["error"=>"Invalid JSON"]); exit; }

// Validate
$name  = trim($data['name']  ?? "");
$phone = trim($data['phone'] ?? "");
$email = trim($data['email'] ?? "");
$desc  = trim($data['case']  ?? "");
if ($name === "" || $phone === "") { http_response_code(422); echo json_encode(["error"=>"Name and phone required"]); exit; }

// Compose
$body  = "Новая заявка с сайта АвтоВердикт\n\n";
$body .= "Имя: $name\n";
$body .= "Телефон: $phone\n";
if ($email) { $body .= "E-mail: $email\n"; }
$body .= "Описание: $desc\n";

// Headers
$domain = $_SERVER['HTTP_HOST'] ?? 'example.com';
$from = "no-reply@".$domain;
$reply = $email ?: $from;
$headers  = "From: $from\r\n";
$headers .= "Reply-To: $reply\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";

// Send
$ok = @mail($to, $subject, $body, $headers);
if ($ok) { echo json_encode(["success"=>true]); }
else { http_response_code(500); echo json_encode(["error"=>"Mail sending failed"]); }
?>
