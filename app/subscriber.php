<?php
require 'vendor/autoload.php'; // Ensure the autoloader is included

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use PDO;

// MQTT broker settings
$broker = getenv('MQTT_BROKER') ?: 'host.docker.internal'; // Connect to WSL's Mosquitto broker
$port = 1883;  // Default MQTT Port
$clientId = 'phpSubscriberClient';

// Create an MQTT client instance
$mqtt = new MqttClient($broker, $port, $clientId);

// Establish connection to the MQTT broker
$mqtt->connect();

// Define the topic to subscribe to
$topic = 'user/messages';

// Subscribe to the topic
$mqtt->subscribe($topic, function ($topic, $message) {
    echo "Message received on topic '$topic': $message\n";

    // Database connection settings
    $dsn = 'mysql:host=' . getenv('MYSQL_HOST') . ';dbname=' . getenv('MYSQL_DATABASE');
    $username = getenv('MYSQL_USER');
    $password = getenv('MYSQL_PASSWORD');
    
    try {
        // Create a new PDO instance for database connection
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Prepare and execute the SQL insert statement
        $stmt = $pdo->prepare("INSERT INTO messages (topic, message) VALUES (:topic, :message)");
        $stmt->bindParam(':topic', $topic);
        $stmt->bindParam(':message', $message);
        $stmt->execute();
        
        echo "Message saved to database.\n";
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage() . "\n";
    }
});

// Keep the script running and listening for new messages
$mqtt->loop();
