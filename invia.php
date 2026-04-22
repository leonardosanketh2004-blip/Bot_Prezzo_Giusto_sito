<?php
header('Content-Type: application/json');

// Configurazione Database basata sul file .sql fornito
$host = "localhost";
$username = "root"; // Default XAMPP
$password = "";     // Default XAMPP
$dbname = "contatti_db";

// Crea connessione
$conn = new mysqli($host, $username, $password, $dbname);

// Verifica connessione
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connessione al database fallita.']);
    exit;
}

// Controlla se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recupero e sanificazione dati per sicurezza
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $messaggio = $conn->real_escape_string($_POST['messaggio']);

    // Query di inserimento nella tabella 'messaggi'
    $sql = "INSERT INTO messaggi (nome, email, messaggio) VALUES ('$nome', '$email', '$messaggio')";

    if ($conn->query($sql) === TRUE) {
        
        // --- INVIO EMAIL ---
        $to = "dev@prezzogiusto.it"; // La tua email di destinazione
        $subject = "Nuovo Messaggio dal Sito: " . $nome;
        $body = "Dettagli della segnalazione:\n\n" .
                "Nome: $nome\n" .
                "Email: $email\n\n" .
                "Messaggio:\n$messaggio";
        $headers = "From: noreply@prezzogiusto.it\r\n" .
                   "Reply-To: $email\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        // Tenta l'invio dell'email
        @mail($to, $subject, $body, $headers);

        // Feedback JSON all'utente
        echo json_encode(['success' => true, 'message' => 'Messaggio ricevuto correttamente! Ti risponderemo presto.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Errore durante il salvataggio: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito.']);
}

$conn->close();
?>