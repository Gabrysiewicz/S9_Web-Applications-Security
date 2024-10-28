<?php
include_once "classes/Page.php";
include_once "classes/Db.php";

Page::display_header("Edit Message");

// Database connection
$db = new Db("mysql-db", "root", "rootpass", "mydb");

// Get the message ID from the query parameter
$message_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = null;

// Fetch the message details if a valid ID is provided
if ($message_id > 0) {
    $result = $db->select("SELECT * FROM message WHERE id = $message_id");
    if (!empty($result)) {
        $message = $result[0];
    } else {
        echo "<p>Message not found.</p>";
        Page::display_footer();
        exit();
    }
}
?>

<h2>Edit Message</h2>
<form method="post" action="messages.php">
    <input type="hidden" name="id" value="<?php echo $message_id; ?>" />
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
                    <option value="public" <?php if ($message->type == "public") echo "selected"; ?>>Public</option>
                    <option value="private" <?php if ($message->type == "private") echo "selected"; ?>>Private</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Message Content</td>
            <td>
                <textarea required name="content" rows="10" cols="40"><?php echo ($message->message); ?></textarea>
            </td>
        </tr>
    </table>
    <input type="submit" value="Update Message" name="update_message"/>
</form>

<hr>
<p>Navigation</p>
<?php
Page::display_navigation();
?>
