<?php
if (!defined('ABSPATH')) {
    exit;
}

function pik_add_admin_menu() {
    add_menu_page(
        'PiK Calculator',
        'Manage Dates',
        'manage_options',
        'pik-manage-dates',
        'pik_manage_dates_page',
        'dashicons-calendar-alt',
        3
    );
}
add_action('admin_menu', 'pik_add_admin_menu');

function pik_manage_dates_page() {
    
    global $wpdb;
    
    $blocked_dates_json = file_get_contents(__DIR__ . '/blocked_dates.json');
    $blocked_dates = json_decode($blocked_dates_json, true)['blocked_dates'];
   $reserved_dates = $wpdb->get_results("SELECT * FROM event_requests", ARRAY_A);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['add_blocked_date'])) {
            $new_date = $_POST['new_blocked_date'];
            $blocked_dates[] = $new_date;
        }
        if (isset($_POST['remove_blocked_date'])) {
            $blocked_dates = array_filter($blocked_dates, function($date) {
                return $date != $_POST['remove_blocked_date'];
            });
        }
       if (isset($_POST['remove_reservation']) && !empty($_POST['reservation_id'])) {
        $reservation_id = intval($_POST['reservation_id']); // Zabezpieczenie przed SQL injection
        $wpdb->delete($wpdb->prefix . 'event_requests', array('id' => $reservation_id), array('%d'));
        
        // Odśwież stronę, aby pokazać aktualny stan
        wp_redirect(admin_url('admin.php?page=pik-manage-dates'));
        exit;
    }

        file_put_contents(__DIR__ . '/blocked_dates.json', json_encode(['blocked_dates' => array_values($blocked_dates)]));
        file_put_contents(__DIR__ . '/reserved_dates.json', json_encode(['reserved_dates' => array_values($reserved_dates)]));

        wp_redirect(admin_url('admin.php?page=pik-manage-dates'));
        exit;
    }
    ?>
   <div class="wrap">
        <h1>Manage Dates</h1>
        <form method="post">
            <h2>Zablokowane daty w kalendarzu</h2>
            <input type="date" name="new_blocked_date">
            <input type="submit" name="add_blocked_date" value="Add Blocked Date">
            <ul>
                <?php foreach ($blocked_dates as $date): ?>
                <li>
                    <?php echo $date; ?>
                    <button type="submit" name="remove_blocked_date" value="<?php echo $date; ?>">Usuń</button>
                </li>
                <?php endforeach; ?>
            </ul>

            <h2>Rezerwacje</h2>
            <ul>
                <?php foreach ($reserved_dates as $reservation): ?>
                <li>
                    <strong>Imię:</strong> <?php echo esc_html($reservation['name']); ?><br>
                    <strong>Typ imprezy:</strong> <?php echo esc_html($reservation['eventType']); ?><br>
                    <strong>Termin:</strong> <?php echo esc_html($reservation['eventDate']); ?><br>
                    <strong>Lokalizacja:</strong> <?php echo esc_html($reservation['location']); ?><br>
                    <strong>Godzina rozpoczęcia:</strong> <?php echo esc_html($reservation['startTime']); ?><br>
                    <strong>Godzina zakończenia:</strong> <?php echo esc_html($reservation['endTime']); ?><br>
                    <strong>Liczba godzin:</strong> <?php echo esc_html($reservation['totalHours']); ?><br>
                    <strong>Liczba gości:</strong> <?php echo esc_html($reservation['guestNumber']); ?><br>
                    <strong>Email:</strong> <?php echo esc_html($reservation['email']); ?><br>
                    <strong>Cena z obniżką:</strong> <?php echo esc_html($reservation['discountPrice']); ?><br>
                    <strong>Cena przed obniżką:</strong> <?php echo esc_html($reservation['price']); ?><br>
                    <strong>Czas wysłania rezerwacji:</strong> <?php echo esc_html($reservation['submissionDate']); ?><br>
                    <strong>IP klienta:</strong> <?php echo esc_html($reservation['clientIP']); ?><br>
                    <form method="post" style="display: inline;">
        <input type="hidden" name="reservation_id" value="<?php echo esc_attr($reservation['id']); ?>">
        <input type="submit" name="remove_reservation" value="Usuń">
    </form>
                </li>
                <?php endforeach; ?>
            </ul>
        </form>
    </div>
    <?php
}
?>
