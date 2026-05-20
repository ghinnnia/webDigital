<?php
// Set header untuk JSON response
header('Content-Type: application/json');

// Fungsi untuk mengirim notifikasi response
function sendResponse($success, $message) {
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

// Cek apakah request method adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Method not allowed');
}

// Ambil data dari form
 $name = isset($_POST['name']) ? trim($_POST['name']) : '';
 $email = isset($_POST['email']) ? trim($_POST['email']) : '';
 $message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validasi input
if (empty($name) || empty($email) || empty($message)) {
    sendResponse(false, 'Mohon lengkapi semua field yang diperlukan.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse(false, 'Mohon masukkan alamat email yang valid.');
}

// Konfigurasi email
 $to = 'aseprinda212008@gmail.com';
 $subject = 'Pesan Baru dari Form Kontak Website - ' . $name;

// Headers email
 $headers = "MIME-Version: 1.0" . "\r\n";
 $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
 $headers .= 'From: ' . $name . ' <' . $email . '>' . "\r\n";
 $headers .= 'Reply-To: ' . $email . "\r\n";
 $headers .= 'X-Mailer: PHP/' . phpversion();

// Body email dengan desain yang lebih baik
 $emailBody = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pesan Kontak</title>
    <style>
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #0f172a, #1e293b); color: white; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 30px 20px; }
        .field { margin-bottom: 20px; }
        .label { font-weight: bold; color: #0f172a; margin-bottom: 5px; display: block; }
        .value { background-color: #f8fafc; padding: 15px; border-left: 4px solid #0f172a; border-radius: 4px; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pesan Baru dari Form Kontak</h1>
        </div>
        <div class="content">
            <div class="field">
                <span class="label">Nama Lengkap:</span>
                <div class="value">' . htmlspecialchars($name) . '</div>
            </div>
            <div class="field">
                <span class="label">Email Address:</span>
                <div class="value">' . htmlspecialchars($email) . '</div>
            </div>
            <div class="field">
                <span class="label">Pesan:</span>
                <div class="value">' . nl2br(htmlspecialchars($message)) . '</div>
            </div>
        </div>
        <div class="footer">
            <p>Email ini dikirim dari form kontak website pada ' . date('d F Y, H:i') . '</p>
        </div>
    </div>
</body>
</html>';

// Kirim email menggunakan fungsi mail() PHP
// Pastikan konfigurasi email di server sudah benar
if (mail($to, $subject, $emailBody, $headers)) {
    sendResponse(true, 'Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.');
} else {
    // Log error untuk debugging
    error_log('Email sending failed. Check server mail configuration.');
    sendResponse(false, 'Maaf, terjadi kesalahan saat mengirim pesan. Silakan coba lagi atau hubungi langsung ke email kami.');
}
?>