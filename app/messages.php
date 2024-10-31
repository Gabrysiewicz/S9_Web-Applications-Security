<?php
include_once "classes/Page.php";
include_once "classes/Db.php";
include_once "classes/Filter.php";

Page::display_header("Messages");
$db = new Db("mysql-db", "root", "rootpass", "mydb");

// Adding a new message
if (isset($_POST['add_message'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $type = htmlspecialchars(trim($_POST['type']));
    $content = htmlspecialchars(trim($_POST['content']));

    // whitelist
    $allowed_types = ['public', 'private'];

    try {
        $filtered_name = Filter::filter_name($name);
        $filtered_type = Filter::filter_type($type);
        $filtered_content = Filter::filter_general($content);

        if (!$db->addMessage($filtered_name, $filtered_type, $filtered_content)) {
            echo "<p style='color:red;'>Adding new message failed.</p>";
        }
    } catch (InvalidArgumentException $e) {
        echo "<p style='color:red;'>{$e->getMessage()}</p>";
    }
}

// Editing an existing message
if (isset($_POST['update_message'])) {
    $id = htmlspecialchars(intval($_POST['id']));
    $name = htmlspecialchars(trim($_POST['name']));
    $type = htmlspecialchars(trim($_POST['type']));
    $content = htmlspecialchars(trim($_POST['content']));

    try {
        $filtered_name = Filter::filter_name($name);
        $filtered_type = Filter::filter_type($type);
        $filtered_content = Filter::filter_general($content);

        if ($db->updateMessage($id, $filtered_name, $filtered_type, $filtered_content)) {
            echo "<p>Message updated successfully.</p>";
        } else {
            echo "<p style='color:red;'>Updating message failed.</p>";
        }
    } catch (InvalidArgumentException $e) {
        echo "<p style='color:red;'>{$e->getMessage()}</p>";
    }
}
?>
<p> Messages </p>
<ol>
    <?php
    $sql = "SELECT * from message";
    $messages = $db->select($sql);
    echo "<table>";
    foreach ($messages as $msg):
        echo "<tr>";

        echo "<td>";
        echo htmlspecialchars($msg->name);
        echo "</td>";

        echo "<td>";
        echo htmlspecialchars($msg->message);
        echo "</td>";

        echo "<td> <a href='message_edit.php?id=";
        echo htmlspecialchars($msg->id);
        echo "'> Edit </a> </td>";
        echo "</tr>";
    endforeach;
    echo "</table>";
    ?>
</ol>
<hr>
<p>Navigation</p>
<?php
Page::display_navigation();
?>
</body>
</html>
