<?php
require 'vendor/autoload.php';
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

// Get the message from the form
$message = $_POST['message'] ?? 'Default Message';

// MQTT broker settings
$broker = getenv('MQTT_BROKER') ?: 'host.docker.internal'; // Connect to WSL's Mosquitto broker
$port = 1883;  // Default MQTT Port
$clientId = 'phpPublisherClient';

// Create an MQTT client instance
$mqtt = new MqttClient($broker, $port, $clientId);

// Establish connection to the MQTT broker
$mqtt->connect();

// Publish the message to a topic
$topic = 'user/messages';
$mqtt->publish($topic, $message, 0);

// Disconnect from the MQTT broker
$mqtt->disconnect();

echo "Message sent: $message";
?>
