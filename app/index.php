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
    <?php
        Page::display_navigation();
    ?>
    </body>
</html>