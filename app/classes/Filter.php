<?php
class Filter {
    public static function filter_name($name) {
        if (!is_string($name)) {
            throw new InvalidArgumentException("Name must be a string.");
        }
        if (preg_match('/[^a-zA-Z\s]/', $name)) {
            throw new InvalidArgumentException('Invalid input detected. Only letters and spaces are allowed.');
        }
        return addslashes(htmlspecialchars(trim($name)));
    }
    // ??
    public static function filter_email($email) {
        if (!is_string($email)) {
            throw new InvalidArgumentException("Email must be a string.");
        }
        // Built-in validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format.");
        }
        return addslashes(htmlspecialchars(trim($email)));
    }
    // ??
    public static function filter_url($url) {
        if (!is_string($url)) {
            throw new InvalidArgumentException("URL must be a string.");
        }
        // Built-in validation
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid URL format.");
        }
        return addslashes(htmlspecialchars(trim($url)));
    }

    public static function filter_general($input) {
        if (!is_string($input)) {
            throw new InvalidArgumentException("Input must be a string.");
        }
        if (preg_match('/[^a-zA-Z\s]/', $input)) {
            throw new InvalidArgumentException('Invalid input detected. Only letters and spaces are allowed.');
        }
        return addslashes(htmlspecialchars(trim($input)));
    }
    
    public static function filter_type($type) {
        if (!is_string($type)) {
            throw new InvalidArgumentException("Type must be a string.");
        }
        // Ensure the type is either 'public' or 'private'
        $type = strtolower(htmlspecialchars(trim($type)));
        if (!in_array($type, ['public', 'private'])) {
            throw new InvalidArgumentException("Type must be either 'public' or 'private'.");
        }
        return $type;
    }
}
?>
