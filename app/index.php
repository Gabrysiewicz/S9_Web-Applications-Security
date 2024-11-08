<?php
include_once "classes/Page.php";
include_once "classes/Pdo_.php";
Page::display_header("Main page");
$pdo=new Pdo_("mysql-db", "root", "rootpass", "mydb");

// adding new user
if (isset($_REQUEST['add_user'])) {
    $login = $_REQUEST['login'];
    $email = $_REQUEST['email'];
    $password = $_REQUEST['password'];
    $password2 = $_REQUEST['password2'];
    if ($password==$password2){
        $pdo->add_user($login, $email, $password);
    } else {
        echo 'Passwords doesn\'t match';
    }
}
// adding new user
if (isset($_REQUEST['log_user_in'])) {
    $login = $_REQUEST['login'];
    $password = $_REQUEST['password'];
    $pdo->log_user_in($login, $password);
}
// change passowrd
if( isset($_REQUEST['change_password'])){
    $login = $_REQUEST['login'];
    $old_password = $_REQUEST['old_password'];
    $new_password = $_REQUEST['new_password'];
    $new_password2 = $_REQUEST['new_password2'];
    if( $new_password != $new_password2){
        echo "Passwords doesn\'t match";
    }else{
        $pdo->change_password($login, $old_password, $new_password);
    }
}
?>
    <h2> Main page</h2>
    <hr/>
    <p> Register new user</p>
    <form method="post" action="index.php">
        <table>
            <tr>
                <td>login</td>
                <td>
                    <label for="name"></label>
                    <input required type="text" name="login" id="login" size="40"/>
                </td>
            </tr>
            <tr>
                <td>email</td>
                <td>
                    <label for="name"></label>
                    <input required type="text" name="email" id="email" size="40"/>
                </td>
            </tr>
            <tr>
                <td>password</td>
                <td>
                    <label for="name"></label>
                    <input required type="text" name="password" id="password" size="40"/>
                </td>
            </tr>
            <tr>
                <td>repeat password</td>
                <td>
                    <label for="name"></label>
                    <input required type="text" name="password2" id="password2" size="40"/>
                </td>
            </tr>
        </table>
        <input type="submit" id= "submit" value="Create account" name="add_user">
    </form>
    <hr>
    <p> Log in</p>
    <form method="post" action="index.php">
    <table>
        <tr>
            <td>login</td>
            <td>
                <label for="name"></label>
                <input required type="text" name="login" id="login" size="40" value="test123"/>
            </td>
        </tr>
        <tr>
            <td>password</td>
            <td>
                <label for="name"></label>
                <input required type="text" name="password" id="password" size="40" value="student"/>
            </td>
        </tr>
    </table>
    <input type="submit" id= "submit" value="Log in" name="log_user_in">
    </form>
    <hr>
    <p>Change Password</p>
    <form method="post" action="index.php">
        <table>
            <tr>
                <td>Login</td>
                <td><input required type="text" name="login" size="40"/></td>
            </tr>
            <tr>
                <td>Current Password</td>
                <td><input required type="password" name="old_password" size="40"/></td>
            </tr>
            <tr>
                <td>New Password</td>
                <td><input required type="password" name="new_password" size="40"/></td>
            </tr>
            <tr>
                <td>Repeat New Password</td>
                <td><input required type="password" name="new_password2" size="40"/></td>
            </tr>
        </table>
        <input type="submit" value="Change Password" name="change_password">
    </form>
    <?php
        Page::display_navigation();
    ?>
    </body>
</html>