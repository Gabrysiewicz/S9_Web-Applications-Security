<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message</title>
</head>
<body>

    <h1>Send Message to MQTT Broker</h1>
    
    <form action="publisher.php" method="post">
        <label for="message">Enter Message:</label>
        <input type="text" id="message" name="message" required>
        <input type="submit" value="Send Message">
    </form>

</body>
</html>
