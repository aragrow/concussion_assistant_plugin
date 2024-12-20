<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WP_ConcussionAssistantAdminUI {

    public function __construct() {

        // Add admin menu
        add_action('admin_menu', [$this, 'concussion_assistant_add_admin_menu']);

        // Register settings
        add_action('admin_init', [$this, 'concussion_assistant_settings_init']);
    }

    // Add the plugin settings page
    function concussion_assistant_add_admin_menu() {
        add_options_page(
            'Concussion Assistant',
            'ConcuAid',
            'manage_options',
            'concussion_assistant',
            [$this, 'consussion_assistant_settings_page']
        );
    }

    // Register plugin settings
    function concussion_assistant_settings_init() {
        register_setting('concussion_assistant_settings', 'CONCUSSION_ASSISTANT_PYTHON_URI');
        register_setting('concussion_assistant_settings', 'CONCUSSION_ASSISTANT_PYTHON_KEY');
    }

    // Display the settings page
    function consussion_assistant_settings_page() { ?>
        <div class="wrap">
        <h1>Concussion AI Assistant Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('concussion_assistant_settings'); ?>
                <?php do_settings_sections('concussion_assistant_settings'); ?>
                <h2>Concussion AI Assistant</h1>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="CONCUSSION_ASSISTANT_PYTHON_URI">URI *</label></th>
                        <td><input type="text" class="widefat" name="CONCUSSION_ASSISTANT_PYTHON_URI" id="CONCUSSION_ASSISTANT_PYTHON_URI"
                                value="<?php echo esc_attr(get_option('CONCUSSION_ASSISTANT_PYTHON_URI', '')); ?>" 
                                class="regular-text" required></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="CONCUSSION_ASSISTANT_PYTHON_KEY"> Key *</label></th>
                        <td><input type="text" class="widefat" name="CONCUSSION_ASSISTANT_PYTHON_KEY" id="CONCUSSION_ASSISTANT_PYTHON_KEY" 
                                value="<?php echo esc_attr(get_option('CONCUSSION_ASSISTANT_PYTHON_KEY', '')); ?>" 
                                class="regular-text" required></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>

        </div>
    <?php }

}

new WP_ConcussionAssistantAdminUI();