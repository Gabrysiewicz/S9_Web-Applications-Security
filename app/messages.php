<?php
include_once "classes/Page.php";
include_once "classes/Db.php";
Page::display_header("Messages");
$db = new Db("mysql-db", "root", "rootpass", "mydb");
// adding new message
if (isset($_REQUEST['add_message'])) {
    $name = $_REQUEST['name'];
    $type = $_REQUEST['type'];
    $content = $_REQUEST['content'];
    if (!$db->addMessage($name,$type,$content))
        echo "Adding new message failed";
}
?>
<P> Messages</P>
<ol>
    <?php
    $sql = "SELECT * from message";
    $messages = $db->select($sql);
    foreach ($messages as $msg)://returned as objects
        echo "<li>";
        echo $msg->message ;
        echo "</li>";
    endforeach;
    ?>
</ol>
    <!---------------------------------------------------------------------→
    <hr>
    <P>Navigation</P>
    <?php
        Page::display_navigation();
    ?>
 </body>
</html>