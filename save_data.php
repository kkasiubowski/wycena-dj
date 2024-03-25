<?php

// Włącz wyświetlanie błędów PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Logowanie niestandardowe dla debugowania
function custom_log($message) {
    error_log($message); // Logowanie do domyślnego logu PHP
    file_put_contents('custom_debug_log.log', date('Y-m-d H:i:s') . ': ' . $message . PHP_EOL, FILE_APPEND); // Logowanie do niestandardowego pliku
}

custom_log('Rozpoczęcie skryptu.');

// Załaduj środowisko WordPressa i jego funkcjonalności
require_once(realpath(dirname(__FILE__) . '/../../../') . '/wp-load.php');

function change_wp_mail_from($original_email_address) {
    return 'kontakt@pikevents.com'; // Podmień na swój adres e-mail
}

function change_wp_mail_from_name($original_email_from) {
    return 'PiK Events'; // Podmień na swoją preferowaną nazwę nadawcy
}

add_filter('wp_mail_from', 'change_wp_mail_from');
add_filter('wp_mail_from_name', 'change_wp_mail_from_name');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $wpdb;

    custom_log('Metoda POST została wykryta.');

    // Odczytaj przesłane dane
    $json_input = file_get_contents('php://input');
    custom_log('Otrzymane dane: ' . $json_input);

    $formData = json_decode($json_input, true);

    // Weryfikacja danych wejściowych
    if (empty($formData['name']) || empty($formData['eventType']) || empty($formData['eventDate']) || empty($formData['location']) || empty($formData['startTime']) || empty($formData['endTime']) || !isset($formData['totalHours']) || !isset($formData['guestNumber']) || empty($formData['email']) || !isset($formData['price']) || !isset($formData['discountPrice'])) {
        custom_log('Wystąpił błąd: niekompletne dane.');
        echo json_encode(['error' => 'Wystąpił błąd: niekompletne dane.']);
        exit;
    }

    // Przygotuj dane do zapisania
    $data = array(
        'name' => $formData['name'],
        'eventType' => $formData['eventType'],
        'eventDate' => $formData['eventDate'],
        'location' => $formData['location'],
        'startTime' => $formData['startTime'],
        'endTime' => $formData['endTime'],
        'totalHours' => intval($formData['totalHours']),
        'guestNumber' => intval($formData['guestNumber']),
        'email' => $formData['email'],
        'price' => $formData['price'],
        'discountPrice' => $formData['discountPrice'],
        'submissionDate' => current_time('mysql', 1), // Użyj funkcji WordPressa do pobrania bieżącej daty i czasu
        'clientIP' => $_SERVER['REMOTE_ADDR'] // Adres IP użytkownika
    );

    // Nazwa tabeli
    $tableName = 'event_requests'; // Przykład: wp_event_requests

        $result = $wpdb->insert($tableName, $data);

    // Obsługa błędów podczas zapisu do bazy danych
    if ($result) {
        custom_log('Dane zostały zapisane.');
        echo 'Dane zostaly zapisane';
        
        
        $subject = "Witamy w PiK Events!";
$body = "<html><body>";
$body .= "<p><h2>Witaj <b>" . htmlspecialchars($formData['name']) . ",</b></h2></p>";
$body .= "<p>Dziękujemy za zainteresowanie naszą ofertą :) Otrzymaliśmy rezerwację na następujące dane:</p>";
$body .= "<ul>";
$body .= "<li>Rodzaj imprezy: " . htmlspecialchars($formData['eventType']) . "</li>";
$body .= "<li>Data: " . htmlspecialchars($formData['eventDate']) . "</li>";
$body .= "<li>Lokalizacja: " . htmlspecialchars($formData['location']) . "</li>";
$body .= "<li>Czas trwania: " . htmlspecialchars($formData['totalHours']) . " godzin</li>";
$body .= "<li>Liczba gości: " . htmlspecialchars($formData['guestNumber']) . "</li>";
$body .= "<li>Cena ze zgodą na udostępnienie filmiku: " . htmlspecialchars($formData['discountPrice']) . " zł</li>";
$body .= "<li>Cena bez zgody na udostępnienie filmiku: " . htmlspecialchars($formData['price']) . " zł</li>";
$body .= "</ul>";
$body .= "<p><h4>Przygotujemy dla Ciebie umowę.</h4>Należy ją wydrukować, podpisać i odesłać skan podpisanej umowy na ten adres e-mail <b>(kontakt@pikevents.com)</b>. Można też umówić się z nami na spotkanie w celu podpisania umowy. Przy podpisaniu umowy należy opłacić zaliczkę w wysokości 500 zł.</p>";
$body .= "<p>Prosimy o przesłanie potrzebnych danych w odpowiedzi na ten e-mail takich jak:</p>";
$body .= "<ul><h4>";
$body .= "<li>Imię i nazwisko</li>";
$body .= "<li>Adres zamieszkania</li>";
$body .= "<li>Numer telefonu</li>";
$body .= "<li>Adres lokalu, w którym odbywać się będzie impreza</li>";
$body .= "<li>Czy została wybrana opcja z udostępnieniem filmiku (" . htmlspecialchars($formData['discountPrice']) . " zł), czy opcja bez zgody na udostępnienie filmiku (" . htmlspecialchars($formData['price']) . " zł).</li>";
$body .= "</h4></ul>";
$body .= "<p>Jeśli masz jakiekolwiek pytania, prosimy śmiało pytać. Można z nami się skontaktować również poprzez nasze social media: <a href='https://www.facebook.com/pikeventslegnica'>Facebook</a> / <a href='https://www.instagram.com/pikevents'>Instagram</a>, a także pod numerami telefonów 533 852 222, 533 733 972.</p>";
$body .= "<p>Pozdrawiamy,<br>Łukasz i Kacper z PiK Events :)</p>";
$body .= "</body></html>";


    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    

    // Wysłanie e-maila do klienta
    wp_mail($formData['email'], $subject, $body, $headers);

    // Wysłanie kopii e-maila do firmy
    wp_mail('kontakt@pikevents.com', $subject, $body, $headers);
        
        
    } else {
        $error_msg = 'Błąd zapisu do bazy danych: ' . $wpdb->last_error;
        custom_log($error_msg);
        echo 'Wystąpił błąd przy przesyłaniu danych do bazy danych.';
    }
} else {
    custom_log('Nieobsługiwana metoda żądania.');
    echo json_encode(['error' => 'Nieobsługiwana metoda żądania.']);
}
?>