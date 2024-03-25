<?php




/**
 * Plugin Name: PiK Calculator
 * Plugin URI: http://pikevents.com
 * Description: Kalkulator wyceny imprezy u DJa dla WordPressa.
 * Version: 1.0
 * Author: Kacper Kasiubowski
 * Author URI:  http://pikevents.com
 */

include_once(plugin_dir_path(__FILE__) . 'admin.php');
 
 
// Zarejestruj skrypty i style
function pik_calculator_enqueue_scripts() {
    wp_enqueue_style('pik-calculator-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('pik-calculator-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), '', true);
}
add_action('wp_enqueue_scripts', 'pik_calculator_enqueue_scripts');


// Funkcja wyświetlająca formularz (możesz dodać tutaj logikę formularza)
function pik_calculator_show_form() {
    include('form.php');
}

// Rejestruj shortcode, aby można było umieścić formularz na stronie
function pik_calculator_shortcode() {
    ob_start();
    pik_calculator_show_form();
    return ob_get_clean();
}
add_shortcode('pik_calculator', 'pik_calculator_shortcode');


