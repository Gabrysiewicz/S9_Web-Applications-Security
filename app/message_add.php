<?php
session_start();

include_once "classes/Page.php";
include_once "classes/Pdo_.php";
Page::display_header("Add Message");
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

if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == @$_GET['from_user']){
    $pdo->log_user_activity($_SESSION['user_id'], 'view', 'message_add', null);
}
$pdo->refresh_session_expiration();
$pdo->check_session_expiration();
?>

<hr>
<p>Add Message</p>
<form method="post" action="messages.php">
    <table>
        <tr>
            <td>Name</td>
            <td>
                <label for="name"></label>
                <input required type="text" name="name" id="name" size="56" />
            </td>
        </tr>
        <tr>
            <td>Type</td>
            <td>
                <label for="type"></label>
                <select name="type" id="type">
                    <option value="public">Public</option>
                    <option value="private">Private</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Message Content</td>
            <td>
                <label for="content"></label>
                <textarea required name="content" id="content" rows="10" cols="40"></textarea>
            </td>
        </tr>
    </table>
    <input type="submit" id="submit" value="Add Message" name="add_message" />
</form>
<hr>
<p>Navigation</p>
<?php
Page::display_navigation($_SESSION['role']);
?>
</body>
</html>
