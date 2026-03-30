<?php
session_start();
$activePage = 'faq'; 


// PHPMailer required files
require 'lib/PHPMailer/src/Exception.php';
require 'lib/PHPMailer/src/PHPMailer.php';
require 'lib/PHPMailer/src/SMTP.php';


// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$message = '';
$status = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //Sanitize the form data
    $name = htmlspecialchars(strip_tags(trim($_POST['name'])));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $question = htmlspecialchars(strip_tags(trim($_POST['question'])));

    if (empty($name) || empty($email) || empty($question) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status = 'error';
        $message = "Invalid input detected. Please check your fields and try again.";
    } else {
        $mail = new PHPMailer(true);

        try {
            // smtp server configurations
            $mail->SMTPDebug = SMTP::DEBUG_OFF; 
            $mail->isSMTP();                                            
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;               
            
            // email details to send the reply
            $mail->Username   = 'overclocktech.dev@gmail.com'; 
            $mail->Password   = 'obmgvyoimkyvzted'; 
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587; 

            // From field
            $mail->setFrom('overclocktech.dev@gmail.com', 'OVERCLOCK/TECH Support'); 
            
            // To field
            $mail->addAddress($email, $name); 

            // Content in html with all the fancy styling
            $mail->isHTML(true); 
            $mail->Subject = 'Transmission Received // OVERCLOCK/TECH Support';
            
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #00e5ff; background: #050508; padding: 15px; text-align: center; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 2px;'>
                        OVERCLOCK/TECH
                    </h2>
                    
                    <p><strong>Hello {$name},</strong></p>
                    <p>This is an automated notification from the OVERCLOCK/TECH system.</p>
                    <p>We have successfully received your transmission. Our support deck is currently reviewing your query and will respond to this email address within 24 hours.</p>
                    
                    <div style='background: #f4f4f4; padding: 15px; border-left: 4px solid #00e5ff; margin: 25px 0;'>
                        <p style='margin-top: 0; font-size: 12px; color: #888; text-transform: uppercase;'>Log of your transmission:</p>
                        <p style='margin-bottom: 0; font-style: italic;'>" . nl2br($question) . "</p>
                    </div>
                    
                    <p>End of line,<br><strong>OVERCLOCK/TECH Automated System</strong></p>
                </div>
            ";
            
            $mail->AltBody = "Hello {$name},\n\nWe received your transmission: \n'{$question}'\n\nOur team will respond within 24 hours.\n\nEnd of line,\nOVERCLOCK/TECH";

            // Send the email
            $mail->send();
            $status = 'success';
            $message = 'Transmission successful. A confirmation receipt has been sent to your email.';
            
        } catch (Exception $e) {
            $status = 'error';
            $message = "Transmission failed. Mailer Error: {$mail->ErrorInfo}";
        }
    }
} else {
    header("Location: faq.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>OVERCLOCK/TECH — Transmission Status</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main class="result-wrapper">
    <div class="auth-card auth-result-card">
        
        <?php if ($status === 'success'): ?>
            <div class="result-icon result-success">
                <!-- Tick svg -->
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
            </div>
            <h2 class="auth-heading">Query Received</h2>
            <div class="success-box" style="margin-top: 1.5rem;">
                <?php echo $message; ?>
            </div>
        <?php else: ?>
            <div class="result-icon result-error">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </div>
            <h2 class="auth-heading">System Error</h2>
            <div class="error-box">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <a href="home.php" class="btn-auth">
            <span>Return to Home</span>
        </a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>