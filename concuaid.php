<?php
/*
Plugin Name: ConcuAid
Description: A plugin to help anwser questions about concussions
Version: 1.1
Author: David Arago - ARAGROW, LLC
*/

// Ensure the genai library is installed and the gemini-pro-vision model is accessible.
// Replace "Write a short, engaging blog post based on this picture" with the specific prompt for your use case.

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Define a constant for the plugin's base directory. This makes the code more readable and easier to maintain.
defined( 'CONCUSSION_ASSISTANT_BASE_DIR' ) or define( 'CONCUSSION_ASSISTANT_BASE_DIR', plugin_dir_path( __FILE__ ) );
defined( 'CONCUSSION_ASSISTANT_BASE_URI' ) or define( 'CONCUSSION_ASSISTANT_BASE_URI', plugin_dir_url( __FILE__ ) );

require_once CONCUSSION_ASSISTANT_BASE_DIR . 'includes/api-integration.php';
require_once CONCUSSION_ASSISTANT_BASE_DIR . 'includes/admin-ui.php';
require_once CONCUSSION_ASSISTANT_BASE_DIR . 'includes/concuaid_shortcodes.php';

// Register the block
//add_action('init', 'register_projects_dynamic_block_top_10');

// Enqueue JavaScript for AJAX functionality
add_action('wp_enqueue_scripts', 'concuaid_enqueue_scripts');
function concuaid_enqueue_scripts($hook) {

        wp_enqueue_style('concuaid-style', plugins_url('dist/css/styles.css', __FILE__));
        wp_enqueue_script('concuaid-ajax-script', CONCUSSION_ASSISTANT_BASE_URI . 'dist/js/gemini.js', ['jquery'], '1.0', true);
        // Localize the script with data
        wp_localize_script(
            'concuaid-ajax-script', // Script handle
            'concuaid_ajax_object', // Object name in JS
            ['ajaxurl' => admin_url('admin-ajax.php')] // URL for AJAX requests
        );

}

// Enqueue JavaScript for AJAX functionality
add_action('admin_enqueue_scripts', 'concuaid_enqueue_admin_scripts');
function concuaid_enqueue_admin_scripts($hook) {
    wp_enqueue_style('concuaid-style', plugins_url('assets/css/admin-styles.css', __FILE__));
    wp_enqueue_script('concuaid-ajax-script', CONCUSSION_ASSISTANT_BASE_URI . 'assets/js/admin-scripts.js', ['jquery'], '1.0', true);
   
}

// Handle AJAX request for generating descriptions
add_action('wp_ajax_concussion_assistant_generate_response', 'concuaid_generate_response');
function concuaid_generate_response() {
    error_log(print_r($_POST,true));
    $what = sanitize_text_field($_POST['what']);
    $text = sanitize_text_field($_POST['text']);
    $description = (new ConcussionAssistantAPIIntegration)->call_python_script($what, $text);

}