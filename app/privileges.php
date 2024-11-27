<?php
session_start();  // Initialize session at the beginning of the script
error_reporting(0);

include_once "classes/Page.php";
include_once "classes/Pdo_.php";
Page::display_header("Main page");
$pdo=new Pdo_("mysql-db", "root", "rootpass", "mydb");
$pdo->session_check();

// Check which form was submitted and set the message accordingly
if (isset($_POST['see_permissions_1'])) {
    $pdo->get_permissions("SELECT * FROM privilege"); 
}
if (isset($_POST['see_permissions_2'])) {
    $pdo->get_permissions("
    SELECT DISTINCT
        r.role_name,
        p.name
    FROM 
        role r
    JOIN 
        role_privilege rp ON r.id = rp.id_role
    JOIN 
        privilege p ON rp.id_privilege = p.id
    WHERE 
        LOWER(p.name) LIKE 'add%' OR LOWER(p.name) LIKE 'delete%' 
    ORDER BY 
        r.role_name, p.name"
    );
}
if (isset($_POST['see_permissions_3'])) {
    $pdo->get_permissions("
    SELECT DISTINCT
        r.role_name,
        r.description,
        p.name
    FROM 
        role r
    JOIN 
        role_privilege rp ON r.id = rp.id_role
    JOIN 
        privilege p ON rp.id_privilege = p.id
    WHERE 
        LOWER(p.name) LIKE 'add%role' OR LOWER(p.name) LIKE 'delete%role' 
    ORDER BY 
        r.role_name, p.name"
    );
}
if (isset($_POST['see_permissions_4'])) {
    $pdo->get_permissions(
        "SELECT 
            r.role_name,
            p.name
        FROM 
            role r
        JOIN 
            role_privilege rp ON r.id = rp.id_role
        JOIN 
            privilege p ON rp.id_privilege = p.id
        ORDER BY 
            r.role_name, p.name;
        ");
}
if (isset($_POST['see_permissions_5'])) {
    $pdo->get_permissions("
    SELECT DISTINCT
        r.role_name,
        r.description,
        p.name
    FROM 
        role r
    JOIN 
        role_privilege rp ON r.id = rp.id_role
    JOIN 
        privilege p ON rp.id_privilege = p.id
    WHERE 
        LOWER(p.name) LIKE 'add%role' OR LOWER(p.name) LIKE 'delete%role' 
    ORDER BY 
        r.role_name, p.name"
    );
}

function display_permissions_table_1($permissions) {
    echo "<table>
            <thead>
            <tr>
                <th>Name</th>
            </tr>
            </thead>";
    foreach ($permissions as $permission) {
        echo "<tr>
                <td>" . htmlspecialchars($permission->name) . "</td>
            </tr>";
    }
    echo "</table>";
}
function display_permissions_table_2($permissions) {
    echo "<table>
            <thead>
            <tr>
                <th>ROLE NAME</th><th>NAME</th>
            </tr>
            </thead>";
    foreach ($permissions as $permission) {
        echo "<tr>
                <td>" . htmlspecialchars($permission->role_name) . "</td>
                <td>" . htmlspecialchars($permission->name) . "</td>
            </tr>";
    }
    echo "</table>";
}
function display_permissions_table_3($permissions) {
    echo "<table>
            <thead>
            <tr>
                <th>ROLE NAME</th><th>DESCRIPTION</th><th>NAME</th>
            </tr>
            </thead>";
    foreach ($permissions as $permission) {
        echo "<tr>
                <td>" . htmlspecialchars($permission->role_name) . "</td>
                <td>" . htmlspecialchars($permission->description) . "</td>
                <td>" . htmlspecialchars($permission->name) . "</td>
            </tr>";
    }
    echo "</table>";
}
function display_permissions_table_4($permissions) {
    echo "<table>
            <thead>
            <tr>
                <th>ROLE</th><th>PRIVILEGE</th>
            </tr>
            </thead>";
    foreach ($permissions as $permission) {
        echo "<tr>
                <td>" . htmlspecialchars($permission->role_name) . "</td>
                <td>" . htmlspecialchars($permission->name) . "</td>
            </tr>";
    }
    echo "</table>";
}
function display_permissions_table_5($permissions) {
    echo "<table>
            <thead>
            <tr>
                <th>ROLE NAME</th><th>DESCRIPTION</th><th>NAME</th>
            </tr>
            </thead>";
    foreach ($permissions as $permission) {
        echo "<tr>
                <td>" . htmlspecialchars($permission->role_name) . "</td>
                <td>" . htmlspecialchars($permission->description) . "</td>
                <td>" . htmlspecialchars($permission->name) . "</td>
            </tr>";
    }
    echo "</table>";
}
?>
<html>
    <head>
    <style>
        main{
            display:flex;
            flex-direction:row;
            align-content: space-between;
        }
        section{
            padding: 15px;
        }
        main > section{
            box-shadow: inset 0 0 1px 1px black;
            flex-basis:50%;
        }
        main > section input[type="submit"]{
            width: 70px;
            height: 55px;
            background-color:white;
            font-size:120%;
            border: 0;
            box-shadow: 0 0 5px 1px black;
        }
        #display{
            box-shadow: inset 0 0 1px 1px black;
            flex-basis:50%;
        }
        #display table{
            width:100%;
            text-align:center;
        }
        #display table thead{
            background-color:black;
            color:white;
            border-collapse:collapse;
        }
        #display table tr{
            box-shadow: inset 0 0 5px 1px #ccc;
        }
    </style>
    </head>
<body>
<?php
if ($_SESSION["role"] === 'moderator' || $_SESSION["role"] === 'admin') { ?>
    <main>
    <section>
        <table>
            <tr> 
                <td>displaying a list of permissions in the system </td> 
                <td>
                    <form method="post" action="privileges.php">
                        <input type="submit" value="See" id="see" name="see_permissions_1">
                    </form>
                </td>
            </tr>
            <tr> 
                <td>displaying a list of user permissions with the option of adding and removing permissions </td> 
                <td>
                    <form method="post" action="privileges.php">
                        <input type="submit" value="See" id="see" name="see_permissions_2">
                    </form>
                </td>
            </tr>
            <tr> 
                <td>displaying a list of roles in the system with the option of adding or removing roles </td>
                <td>
                    <form method="post" action="privileges.php">
                        <input type="submit" value="See" id="see" name="see_permissions_3">
                    </form>
                </td>
            </tr>
            <tr> 
                <td>displaying a list of permissions assigned to the role with the option of adding and removing permissions </td>
                <td>
                    <form method="post" action="privileges.php">
                        <input type="submit" value="See" id="see" name="see_permissions_4">
                    </form>
                </td>
            </tr>
            <tr> 
                <td>displaying a list of user roles with the option of adding or removing roles for the user </td>
                <td>
                    <form method="post" action="privileges.php">
                        <input type="submit" value="See" id="see" name="see_permissions_5">
                    </form>
                </td>
            </tr>
        </table>
    </section>
    <section id="display">
    <?php 
        if (isset($_POST['see_permissions_1'])) {
            display_permissions_table_1($_SESSION["permissions"]);
        } else if (isset($_POST['see_permissions_2'])) {
            display_permissions_table_2($_SESSION["permissions"]);
        } else if (isset($_POST['see_permissions_3'])) {
            display_permissions_table_3($_SESSION["permissions"]);
        } else if (isset($_POST['see_permissions_4'])) {
            display_permissions_table_4($_SESSION["permissions"]);
        } else if (isset($_POST['see_permissions_5'])) {
            display_permissions_table_5($_SESSION["permissions"]);
        } else {
            echo "<p>No permissions available to display.</p>";
        }

    ?>
</section>

    </main>
    <?php
        Page::display_navigation($_SESSION['role']);
}
    ?>

</body>
</html>