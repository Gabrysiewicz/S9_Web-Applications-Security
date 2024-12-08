<?php
require('./vendor/autoload.php');
use \PhpMqtt\Client\MqttClient;
use \PhpMqtt\Client\ConnectionSettings;

$server = 'localhost';
$port = 1883;
$clientId = 1;
$username = 'test_sender';
$password = null;
$clean_session = false;
$connectionSettings = new ConnectionSettings();
$connectionSettings
	->setUsername($username)
	->setPassword(null)
	->setKeepAliveInterval(60)
	->setLastWillTopic('security')
	->setLastWillMessage('client disconnect')
	->setLastWillQualityOfService(1);


$mqtt = new MqttClient($server, $port, $clientId);
$mqtt->connect($connectionSettings, $clean_session);

printf("client connected\n");

$mqtt->subscribe('security', function ($topic, $message) {
	printf("Received message on topic [%s]: %s\n", $topic, $message);
}, 0);
for ($i = 0; $i< 10; $i++) {
	$payload = array(
	'source' => 'php_app',
	'date' => date('Y-m-d H:i:s')
);

$mqtt->publish(
	// topic
	'security',
	// payload
	json_encode($payload),
	// qos
	0,
	// retain
	true
);
	printf("msg $i send\n");
	sleep(1);
}
?>
