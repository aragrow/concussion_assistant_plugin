<?php

/*
Purpose: Prevents direct access to the file. The check ensures that the file is only executed within the WordPress environment 
(not directly accessed via the browser).
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WP_ConcuAid_ShortCodes {

    public function __construct() {

       // add_action('admin_post_save_injury_data', [$this,'save_injury_data']);
       // add_action('admin_post_nopriv_save_injury_data', [$this,'save_injury_data']);

       // Register the shortcode
        add_shortcode('concuaid', [$this, 'concuaid_shortcode_search']);
        // Register the AJAX action for logged-in users
        add_action('wp_ajax_concuaid_generate_response', [$this ,'concuaid_shortcode_search_result']);

        // Register the AJAX action for non-logged-in users
        add_action('wp_ajax_nopriv_concuaid_generate_response', [$this ,'concuaid_shortcode_search_result']);
    
    }


    function concuaid_shortcode_search($atts) {
        // Set the default parameters for the shortcode
        error_log('Exec => concuaid_shortcode_search');

        $python = [
            'uri' => esc_attr(get_option('CONCUSSION_ASSISTANT_PYTHON_URI', '')),
            'key' => esc_attr(get_option('CONCUSSION_ASSISTANT_PYTHON_KEY', '')),
        ];

        $postAtts = shortcode_atts(
            $atts,
            'concuaid'
        );
    
        // Initialize cURL session
        $ch = curl_init();
    
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $python['uri']);      // URL to request
        
        // Return the response instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    // Return response as a string
        
        // If the server says this URL has moved, go ahead and follow the new address automatically until you reach the final destination.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);    // Follow redirects if any

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); // Change to POST, PUT, etc., as needed

        // Set headers, including the Bearer token
        $headers = [
            "Authorization: Bearer {$python['key']}",
            "Content-Type: application/json" // Add more headers if necessary
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // Disable SSL verification (for local testing only)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute cURL and store the result
        $response = curl_exec($ch);
    
        // Check for cURL errors
        if(curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return "Error: " . $error_msg;
        }
    
        // Close cURL session
        curl_close($ch);
    
        // Return the response or process it (e.g., display as JSON)
        return $response;
    }

    function concuaid_shortcode_search_result($atts) {

        error_log('Exec => concuaid_shortcode_search_result');

        $python = [
            'uri' => esc_attr(get_option('CONCUSSION_ASSISTANT_PYTHON_URI', '')),
            'key' => esc_attr(get_option('CONCUSSION_ASSISTANT_PYTHON_KEY', '')),
        ];

        error_log(print_r($_POST,true));

        // Check if 'field_name' is set in the POST request
        if (isset($_POST['what'])) {
            // Retrieve and sanitize the value
            $what = sanitize_text_field($_POST['what']); // Example sanitization for WordPress

        } else {
            error_log('Exec => concuaid_shortcode_search_result -> No what received in the POST request.');
            echo "No what received in the POST request.";
            return;
        }
        if (isset($_POST['text'])) {
            // Retrieve and sanitize the value
            $text = sanitize_text_field($_POST['text']); // Example sanitization for WordPress

        } else {
            error_log('Exec => concuaid_shortcode_search_result -> No text received in the POST request.');
            echo "No text received in the POST request.";
            return;
        }

        $the_prompt = $this->set_concuaid_prompt();
        $the_context = ['what' => $what, 'text' => $text];

        // Initialize cURL session
        $ch = curl_init();
    
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $python['uri']);      // URL to request
        
        // Return the response instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    // Return response as a string
        
        // If the server says this URL has moved, go ahead and follow the new address automatically until you reach the final destination.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);    // Follow redirects if any
        
        // Set the HTTP method to POST
        curl_setopt($ch, CURLOPT_POST, true);
        // Set the POST fields
        $data = [
            "prompt" => $the_prompt,
            "context" => $the_context,
        ];

        error_log(print_r($data,true));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Send as application/x-www-form-urlencoded

        // Set headers, including the Bearer token
        $headers = [
            "Authorization: Bearer {$python['key']}",
            "Content-Type: application/json" // Add more headers if necessary
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // Disable SSL verification (for local testing only)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute cURL and store the result
        $response = curl_exec($ch);
    
        error_log(print_r($response,true));

        // Check for cURL errors
        if(curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            error_log($error_msg);
            return "Error: " . $error_msg;
        }
    
        // Close cURL session
        curl_close($ch);
    
        // Return the response or process it (e.g., display as JSON)
        return $response;
    }

    function set_concuaid_prompt() {

        error_log('Exec => set_concuaid_prompt');

        return 
        "You are a highly knowledgeable expert on concussion. 
        Your primary goal is to assist the user by accurately analyzing and answering their requests related to the book. 
        If the user provides additional context, you must carefully incorporate it to tailor your response, ensuring accuracy and relevance.
        When crafting your answer:
        Be thorough, clear, and insightful.
        Use evidence from the text where applicable.
        Adapt your tone and depth based on the user’s query, whether it is a detailed literary analysis, a concise summary, or a specific interpretation.
        Always strive to provide value by addressing the user’s needs with precision and depth.
        Use easy readable natural human language";
    }

}

/*
Purpose: Creates an instance of the WP_InShape_Admin_UI class.
*/
new WP_ConcuAid_ShortCodes();