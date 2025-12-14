
<?php
include ('cfg.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

define('O2_HOST', 'poczta.o2.pl');
define('O2_USERNAME', 'smtp_placeholder@o2.pl'); 
define('O2_PASSWORD', 'PlaceholderPassword123');

$odbiorca = 'admin_placeholder@example.com';

function PokazKontakt(): void
{
    $id_kontaktu = $_GET['id'] ?? 7; 
    $action_url = "?id={$id_kontaktu}"; 

    $komunikat = '';
    if (isset($_SESSION['mail_status_message'])) {
        $komunikat = $_SESSION['mail_status_message'];
        unset($_SESSION['mail_status_message']);
    }
    echo $komunikat;
    
    $formularz = '
    <div class="kontakt-main">
        <h2>Wyślij do nas wiadomość!</h2>
        <p>Jeśli masz pytania lub jakieś sugestie - Napisz do nas! Chętnie odpowiemy!</p>

        <form method="post" action="' . htmlspecialchars($action_url) . '" class="kontakt">
            
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>

            <label for="temat">Temat:</label>
            <input type="text" id="temat" name="temat" required>

            <label for="wiadomosc">Wiadomość:</label>
            <textarea id="wiadomosc" name="tresc" required></textarea>

            <button type="submit" name="wyslij_kontakt">Wyślij!</button>
        </form>
    </div>';
    
    echo $formularz;
}

function WyslijMailKontakt(string $odbiorca): void
{

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        $_SESSION['mail_status_message'] = '<p style="color: red;">[nie_wypelniles_pola]: Musisz wypełnić wszystkie pola formularza.</p>';
    } else {
		
        $mail = new PHPMailer(true);
        $adres_uzytkownika = $_POST['email'];

        try {
            $mail->isSMTP();
            $mail->Host       = O2_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = O2_USERNAME;
            $mail->Password   = O2_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(O2_USERNAME, 'Formularz Kontaktowy (Z: ' . $adres_uzytkownika . ')');
            $mail->addAddress($odbiorca);
            $mail->addReplyTo($adres_uzytkownika, 'Nadawca formularza'); 

            $mail->isHTML(false);
            $mail->Subject = '[PENNE KONTAKT] ' . $_POST['temat'] . ' (Od: ' . $adres_uzytkownika . ')';
            $mail->Body    = "Wiadomość z formularza od: {$adres_uzytkownika}\n\nTreść:\n{$_POST['tresc']}";
			
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->send();
			
            $_SESSION['mail_status_message'] = '<p style="color: green;">[wiadomosc_wyslana]: Twoja wiadomość została wysłana pomyślnie.</p>';
			
        } catch (Exception $e) {
            $_SESSION['mail_status_message'] = '<p style="color: red;">Błąd wysyłki: ' . $mail->ErrorInfo . '</p>';
        }
    }

    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?id=' . ($_GET['id'] ?? 7));
    exit();
}

function PrzypomnijHaslo(string $odbiorca, string $haslo): void
{

    if (session_status() == PHP_SESSION_NONE)
    {
        session_start();
    }
    
    $mail = new PHPMailer(true);
    
    try 
    {

        $mail->isSMTP(); 
        $mail->Host       = O2_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = O2_USERNAME;
        $mail->Password   = O2_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465; 
        $mail->CharSet    = 'UTF-8';
        
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom(O2_USERNAME, 'System CMS - Przypomnienie Hasla'); 
        $mail->addAddress($odbiorca);
        

        $mail->isHTML(false);
        $mail->Subject = "Przypomnienie Hasła do Panelu CMS";
        $mail->Body    = "Witaj,\n\nPoniżej jest Twoje hasło do Panelu Administracyjnego:\n\nHasło: {$haslo}\n\n";
        
        $mail->send();
        
        $_SESSION['password_status_message'] = '<p style="color: green;">Hasło zostało wysłane na adres e-mail admina: ' . htmlspecialchars($odbiorca) . '.</p>';

    } catch (Exception $e) {
        $_SESSION['password_status_message'] = "<p style='color: red;'>Wystąpił błąd podczas wysyłania hasła: {$mail->ErrorInfo}</p>";
    }

    header('Location: admin.php');
    exit();
}

if (isset($_POST['wyslij_kontakt'])) {
    global $odbiorca;
    WyslijMailKontakt($odbiorca);
}
	
?>