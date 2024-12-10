<table align='center'>
  <tr> <td colspan='3' align='center' width='884px'> Web Applications Security </td> </tr>
  <tr> <td colspan="3" align='center'> <img src='https://github.com/Gabrysiewicz/Programowanie-aplikacji-w-chmurze-obliczeniowe/blob/main/logo_politechniki_lubelskiej.jpg' width="400px" height="400px"></td> </tr>
  <tr> <td> Kamil Gabrysiewicz </td> <td> Index: 95400 </td> <td> Grupa: 2.1 </td> </tr>  
  <tr> <td> Wtorek 11:45-13:15 </td> <td> Semestr 2 </td> <td>Laboratorium 8</td></tr>  
</table>

# Task 10.1.
### Create two recipients and a sender. Post messages to the feed they subscribe to. 

### Disable one recipient. Post some messages. Re-enable the second recipient. Repeat the exercise for different values of QOS (0,1,2). Determine the impact of QOS on message delivery.

<hr/>

To create a recipient I used the commad `mosquitto_sub` for 3 different recipients
```
mosquitto_sub -h localhost -t "test/topic" -q 0 -c -i "subscriber qos 0" 
mosquitto_sub -h localhost -t "test/topic" -q 1 -c -i "subscriber qos 1" 
mosquitto_sub -h localhost -t "test/topic" -q 2 -c -i "subscriber qos 2" 
```
```
-h localhost: Specifies the MQTT broker's host.
-t "test/topic": Specifies the topic to which the subscriber is subscribing.
-q 0: Specifies the Quality of Service (QoS) level for the subscription
   0: At most once (no confirmation of delivery).
   1: At least once (guarantees message delivery, but duplicates may occur).
   2: Exactly once (ensures message delivery without duplicates).
-c: Enables clean session to be disabled, persistence is enabled
-i "subscriber qos 0": Specifies the client ID for the subscriber.
```

To send a message I used the command `mosquitto_pub` with 3 different QoS every time.
```
mosquitto_pub -h localhost -t "test/topic" -m "Message at QoS 0" -q 0
mosquitto_pub -h localhost -t "test/topic" -m "Message at QoS 1" -q 1
mosquitto_pub -h localhost -t "test/topic" -m "Message at QoS 2" -q 2
```

In the first example, the clients don't have the `-c` option, so persistence is disabled. The messages that were sent while the clients were disconnected aren't stored and resent. Three messages were sent in a series of five, and each subscriber was disconnected for one series. We can see that all subscribers are missing one series of messages that weren't resent.
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab8/img/Task8.1.png">
</p>

In the second example, the clients have the `-c` option, so persistence is now enabled. The messages that were sent while the clients were disconnected and had QoS > 0 were stored and resent to clients with QoS > 0.
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab8/img/Task8.1a.png">
</p>



# Task 10.2.
### Using MQTT communication, establish communication between two application modules. One module will retrieve messages from the user and send them to the MQTT broker. The second module will save these messages in the database.
<hr/>

Once again I am using docker for apche-php and mysql-db. I also tried plenty of times adding mosquitto to this but it failed miserably. So mosquitto is just running in WSL while apache and database are in docker
```
version: '3.8'

services:
  # PHP Application Service
  php:
    image: php:8.2-apache               
    build:
      context: .
      dockerfile: Dockerfile
    container_name: php-app             
    volumes:
      - ./app:/var/www/html             
    ports:
      - "8080:80"                       
    depends_on:
      - db                              
    networks:
      - app-network
    environment:
      MYSQL_HOST: db                    
      MYSQL_DATABASE: lab8
      MYSQL_USER: root
      MYSQL_PASSWORD: rootpass

  # MySQL Database Service
  db:
    image: mysql:8.0                   
    container_name: mysql-db            
    restart: always                     
    environment:
      MYSQL_ROOT_PASSWORD: rootpass     
      MYSQL_DATABASE: lab8         
      MYSQL_USER: root           
      MYSQL_PASSWORD: rootpass       
    volumes:
      - db_data:/var/lib/mysql          
    networks:
      - app-network

# Named volume to persist data
volumes:
  db_data:

# Custom network for communication between services
networks:
  app-network:
    driver: bridge
```

```
FROM php:8.2-apache

# Install necessary packages and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Copy your application code to the container
COPY ./app /var/www/html

# Set the working directory
WORKDIR /var/www/html
```
<hr/>
<h3> I Have simple view for message form </h3>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab8/img/Task8.2b.png">
</p>

<hr/>
<h3> After sending a message there is prompt informing user of the state </h3>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab8/img/Task8.2c.png">
</p>

<hr/>
<ul>
<li> At the upper left, we've got a terminal window with the Docker database. We can see the table schema and the message successfully saved to the database. </li>
<li> The upper right terminal shows the running Mosquitto server in WSL.</li>
<li> The lower left terminal window has started the MQTT subscriber inside a Docker container. </li>
<li> The lower right terminal window does nothing, but it also wanted to participate in the process. </li>
</ul>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab8/img/Task8.2.png">
</p>

<hr/>

publisher.php
```
<?php
require 'vendor/autoload.php';
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

// Get the message from the form
$message = $_POST['message'] ?? 'Default Message';

// MQTT broker settings
$broker = getenv('MQTT_BROKER'); // Connect to WSL's Mosquitto broker
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
```

<hr/>

subscriber.php
```
<?php
require 'vendor/autoload.php';

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use PDO;

// MQTT broker settings
$broker = getenv('MQTT_BROKER') ?: 'host.docker.internal';
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
```
