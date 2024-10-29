<?php
    include_once "classes/Page.php";
    include_once "classes/Db.php";
    Page::display_header("Messages");
    // $db = new Db("mysql-db", "root", "rootpass", "mydb");
    $db = new Db("mysql-db", "new_user", "user_password", "mydb");
    
    // Adding new message
    if (isset($_POST['add_message'])) {
        $name = htmlspecialchars(trim($_POST['name']));
        $type = htmlspecialchars(trim($_POST['type']));
        $content = htmlspecialchars(trim($_POST['content']));

        if (!$db->addMessage($name, $type, $content)) {
            echo "<p style='color:red;'>Adding new message failed.</p>";
        }
    }

    // Editing existing message
    if (isset($_POST['update_message'])) {
        $id = intval($_POST['id']);
        $name = htmlspecialchars(trim($_POST['name']));
        $type = htmlspecialchars(trim($_POST['type']));
        $content = htmlspecialchars(trim($_POST['content']));

        if ($db->updateMessage($id, $name, $type, $content)) {
            echo "<p>Message updated successfully.</p>";
        } else {
            echo "<p style='color:red;'>Updating message failed.</p>";
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
            echo $msg->name;
            echo "</td>";

            echo "<td>";
            echo $msg->message;
            echo "</td>";
            
            echo "<td> <a href='message_edit.php?id=";
            echo $msg->id;
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