<?php
session_start();

include_once "classes/Page.php";
include_once "classes/Pdo_.php";
include_once "classes/Filter.php";

Page::display_header("Edit Message");

$pdo=new Pdo_("mysql-db", "root", "rootpass", "mydb");
// SESSION EXPIRATION CHECK
if( isset($_SESSION['session_expiration'])){
    echo "session_expiration: ".$_SESSION['session_expiration']."<br/>";
}else{
    echo "session_expiration: null <br/>";
}

if( isset($_SESSION['logged_in'])){
    echo "Logged in: ".$_SESSION['logged_in']."<br/>";
}else{
    echo "Logged in: null <br/>";
}
$pdo->refresh_session_expiration();
$pdo->check_session_expiration();

// Get the message ID from the query parameter and ensure it's valid
$message_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = null;

// Fetch the message details if a valid ID is provided
if ($message_id > 0) {
    $result = $pdo->select("SELECT * FROM message WHERE id = :id", [':id' => $message_id]);
    if (!empty($result)) {
        $message = $result[0];
    } else {
        echo "<p style='color:red;'>Message not found.</p>";
        exit();
    }
} else {
    echo "<p style='color:red;'>Invalid message ID.</p>";
    exit();
}
?>

<h2>Edit Message</h2>
<form method="post" action="messages.php">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($message_id); ?>" />
    <table>
        <tr>
            <td>Name</td>
            <td>
                <input type="text" name="name" value="<?php echo htmlspecialchars($message->name); ?>" size="56" required />
            </td>
        </tr>
        <tr>
            <td>Type</td>
            <td>
                <select name="type">
                    <option value="public" <?php if ($message->type === "public") echo "selected"; ?>>Public</option>
                    <option value="private" <?php if ($message->type === "private") echo "selected"; ?>>Private</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Message Content</td>
            <td>
                <textarea required name="content" rows="10" cols="40"><?php echo htmlspecialchars($message->message); ?></textarea>
            </td>
        </tr>
    </table>
    <input type="submit" value="Update Message" name="update_message"/>
</form>

<hr>
<p>Navigation</p>
<?php
Page::display_navigation($_SESSION['role']);
?>
