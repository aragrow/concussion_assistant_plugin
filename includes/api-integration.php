<?php
if (!defined('ABSPATH')) exit;

class WP_ConcussionAssistantAPIIntegration {

    public function __construct() {

    }

    function set_apiuri() {
        return get_option('CONCUSSION_ASSISTANT_URI');
    }

    function set_apikey() {
        return get_option('CONCUSSION_ASSISTANT_KEY');
    }

    function set_system_prompt() {
        return 
        "Given an image, perform the following steps:
            1. Draft a Description in one paragraph:
            Use vibrant, colorful, and descriptive language to highlight the key features and benefits of the forefront item.
            Maintain a professional, engaging tone that resonates with a wide audience.
            Emphasize the product's unique attributes, functionality, and value to customers.
            Avoid describing the background of the image unless it directly complements the item.
            2. Format the Output:
            Write an SEO-friendly description to enhance online visibility.
            Structure the output using basic HTML tags
            Exclude html tags in the response.
            Additional Considerations:
            Write descriptions tailored to inspire trust and excitement in potential buyers.
            Highlight why the product stands out from competitors.
            Use natural language that feels human and relatable.
        ";
    }

    function call_python_script($what, $text) {
        
        error_log("Exec->call_python_script()");

        $api_url = getenv('CONCUSSION_ASSISTANT_URI');
        if (!$api_url) {
            $api_url = $this->set_apiuri();
            if (!$api_url) 
                die("API_KEY eis not set.\n");
        }

        // Set the API key from the environment variable
        $api_key = getenv('CONCUSSION_ASSISTANT_KEY');
        if (!$api_key) {
            $api_key = $this->set_apikey();
            if (!$api_key) 
                die("API_KEY eis not set.\n");
        }

        $systemPrompt =  $this->set_system_prompt();
        $userPrompt = "'what': $what, 'text': $text";

        $requestPayload = [
            'systemPrompt' => $systemPrompt,
            'userPrompt' => $userPrompt
        ];


        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url); // Replace with the correct endpoint
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, true); // Specify GET method (optional)
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestPayload),);

        // Disable SSL verification (for local testing only)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute the request
        $response = curl_exec($ch);
        error_log('------ Response --------');
        error_log($response);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            error_log("cURL Error: " . curl_error($ch));
            curl_close($ch);
            return;
        }

        $responseJson = json_decode($response, true);

        return $responseJson;
    }


}