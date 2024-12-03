<?php
session_start();

include_once "classes/Page.php";
include_once "classes/Pdo_.php";
include_once "classes/Filter.php";

Page::display_header("Messages");
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
?>
<P> Messages</P>
    <?php
    $where_clause="";
    // filtering messages
    if (isset($_REQUEST['filter_messages'])) {
        $string = $_REQUEST['string'];
        $where_clause= " and name LIKE '%" . $string . "%'";
    }
    $sql = "SELECT * from message WHERE deleted=0 " . $where_clause;
    echo $sql;
    echo "<BR/><BR/>";
    $messages = $pdo->select($sql);
    if (count($messages)){
        echo '<table>';
        $counter=1;
        foreach ($messages as $msg)://returned as objects
?>
    <tr>
        <td><?php echo $counter++ ?></td>
        <td><?php echo $msg->name ?></td>
        <td><?php echo $msg->message ?></td>
        <form method="post" action="message_action.php">
        <input type="hidden" name="message_id" id="message_id" value="<?php echo $msg->id?>"/>
        <?php 
            if(isset($_SESSION['delete message']))
                echo '<td><input type="submit" id= "submit" value="Delete" name="delete_message"></td>';
            if(isset($_SESSION['edit message']))
                echo '<td><input type="submit" id= "submit" value="Edit" name="edit_message"></td>'; 
        ?>
        </form>
    </tr>
<?php
        endforeach;
        echo '</table>';
    } else{
        echo "No messages available";
    }