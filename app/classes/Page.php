<?php
class Page {
    static function display_header($title) { ?>
        <html lang="en-GB">
        <head>
            <title><?php echo $title; ?></title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body>
    <?php
    }

    static function display_navigation($userRole) { ?>
        <a href="index.php">Index</a><br>
        <a href="messages.php">Messages</a><br>
        <a href="message_add.php">Add New Message</a><br>
        <?php
        // Display "Privileges" link only for moderators and admins
        if ($userRole === 'moderator' || $userRole === 'admin') { ?>
            <a href="privileges.php">Privileges</a><br>
        <?php } 
    }
}
?>
