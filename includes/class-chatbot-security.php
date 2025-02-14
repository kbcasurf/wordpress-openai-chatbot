<?php
class OAC_Chatbot_Security {
    public static function sanitize_settings($input) {
        $sanitized = [];

        if (isset($input['api_key'])) {
            $sanitized['api_key'] = sanitize_text_field($input['api_key']);
        }

        if (isset($input['assistant_id'])) {
            $sanitized['assistant_id'] = sanitize_text_field($input['assistant_id']);
        }

        return $sanitized;
    }

    public static function verify_nonce($nonce, $action) {
        return wp_verify_nonce($nonce, $action);
    }

    public static function validate_api_credentials($api_key, $assistant_id) {
        return !empty($api_key) && preg_match('/^sk-[a-zA-Z0-9]{48}$/', $api_key) &&
               !empty($assistant_id) && preg_match('/^asst_[a-zA-Z0-9]{24}$/', $assistant_id);
    }
}