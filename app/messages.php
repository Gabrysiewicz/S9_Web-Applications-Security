<?php
session_start();
error_reporting(0);

include_once "classes/Page.php";
include_once "classes/Pdo_.php";
include_once "classes/Filter.php";

Page::display_header("Messages");
$pdo=new Pdo_("mysql-db", "root", "rootpass", "mydb");
$pdo->session_check();


$pdo->refresh_session_expiration();
$pdo->check_session_expiration();
// Adding a new message
if (isset($_POST['add_message'])) {
    if(isset($_SESSION['login'])){
        $name = $_POST['name'];
        $type = $_POST['type'];
        $content = $_POST['content'];
        $user_id = $_SESSION['user_id'];
        // whitelist
        $allowed_types = ['public', 'private'];

        try {
            if (!$pdo->addMessage($name, $type, $content, $user_id)) {
                echo "<p style='color:red;'>Adding new message failed.</p>";
            }
        } catch (InvalidArgumentException $e) {
            echo "<p style='color:red;'>{$e->getMessage()}</p>";
        }
    }else{
        echo "<h1 color='red'> YOU MUST BE LOGGED IN TO POST MESSAGES </h1>";
    }
    
}

// Editing an existing message
if (isset($_POST['update_message'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $content = $_POST['content'];

    try {
        if ($pdo->updateMessage($id, $name, $type, $content)) {
            echo "<p>Message updated successfully.</p>";
        } else {
            echo "<p style='color:red;'>Updating message failed.</p>";
        }
    } catch (InvalidArgumentException $e) {
        echo "<p style='color:red;'>{$e->getMessage()}</p>";
    }
}
// Delete an existing message
if (isset($_GET['delete_message'])) {
    // Check if the user has the required role
    if ($_SESSION['role'] === 'moderator' || $_SESSION['role'] === 'admin' || (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $_GET['owner'])) {
        $id = (int)$_GET['delete_message']; // Cast to int for security

        try {
            $sql = "UPDATE message SET deleted = 1 WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$id])) {
                echo "<p style='color:green;'>Message deleted successfully.</p>";
            } else {
                echo "<p style='color:red;'>Failed to delete message.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p style='color:red;'>You do not have permission to delete messages.</p>";
    }
}


?>
<p> Messages </p>
<ol>
    <?php
    if(isset($_SESSION['login'])){
        echo "<a href='messages.php?from_user=".$_SESSION['user_id']."'> My Messages </a>";
    }
    if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == @$_GET['from_user']){
        $sql = "SELECT * FROM message WHERE deleted = 0 and user_id = ".$_SESSION['user_id'];
        $messages = $pdo->select($sql);
    }else{
        $sql = "SELECT * FROM message WHERE deleted = 0";
        $messages = $pdo->select($sql);
    }
    if(isset($messages) && $messages != null) {
        echo "<table>";
        foreach ($messages as $msg):
            echo "<tr>";
            echo "<td>" . htmlspecialchars($msg->name) . "</td>";
            echo "<td>" . $msg->message . "</td>";
            // Show delete link only for moderators or admins
            if ($_SESSION['role'] === 'moderator' || $_SESSION['role'] === 'admin' || @(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $_GET['from_user'])) {
                echo "<td><a href='message_edit.php?id=" . htmlspecialchars($msg->id) . "&owner=".htmlspecialchars($msg->user_id)."'>Edit</a></td>";
            }
            // Show delete link only for moderators or admins
            if ($_SESSION['role'] === 'moderator' || $_SESSION['role'] === 'admin' || @(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $_GET['from_user'])) {
                echo "<td><a href='?delete_message=" . htmlspecialchars($msg->id) . "&owner=".htmlspecialchars($msg->user_id)."' style='color:red;'>Delete</a></td>";
            }
            echo "</tr>";
        endforeach;
        
        echo "</table>";
    }else{
        echo "<h2> Nothing to display </h2>";
    }
    
    ?>
</ol>
<hr>
<p>Navigation</p>
<?php
Page::display_navigation($_SESSION['role']);
?>
</body>
</html>
