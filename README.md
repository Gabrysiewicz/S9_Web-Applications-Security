<table align='center'>
  <tr> <td colspan='3' align='center' width='884px'> Web Applications Security </td> </tr>
  <tr> <td colspan="3" align='center'> <img src='https://github.com/Gabrysiewicz/Programowanie-aplikacji-w-chmurze-obliczeniowe/blob/main/logo_politechniki_lubelskiej.jpg' width="400px" height="400px"></td> </tr>
  <tr> <td> Kamil Gabrysiewicz </td> <td> Index: 95400 </td> <td> Grupa: 2.1 </td> </tr>  
  <tr> <td> Wtorek 11:45-13:15 </td> <td> Semestr 2 </td> <td>Laboratorium 6</td></tr>  
</table>


# Task 6.1.
Verify whether verification of authorizations to all functions is carried out correctly. Send
fabricated requests to functions without being logged in. If any irregularities are detected,
improve the security measures and re-verify their correct operation.
Tip. To fabricate requests, you can change the method of sending requests from forms to
"get" during testing. If you don't want to do this, use a request inspection and creation
program. It may be Fidler (https://www.telerik.com/fiddler).

<hr/>

Gladly in last laboratory I slightly overdone my app and all features of protection against fabricated request have been added.
Attempt of deleteing message that doesn't belong to us will lead to message of "You do not have permission to delete message".
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab6/img/Task6_1a.png">
</p>

<hr/>
Attempt of accessing the view that isnt made for unprivileged, unlogged user wont work and view won't be even rendered for attacker.
As we can see only some undisabled warnings are returned in body tag
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab6/img/Task6_1b.png">
</p>

# Task 6.2.
Implement the ability to view and edit your own messages in the application. Add a "my
messages" page. Use the existing function to edit messages. Only modify the access control
code there. Users with appropriate permissions and the message creator are to be authorized to
edit messages.

<hr/>
<h3> Logging in as user "test" with role user </h3>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab6/img/Task6_2a.png">
</p>

<hr/>
<h3> View messages.php will display all undeleted public messages for all user. The <em>My messages</em> hyperlink is only available for logged in users and leads to messages posted by that particular user. <em>Edit</em> and <em>Delete</em> button are no longer available in messages.php view and were moved to <em>My Messages</em> view. </h3>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab6/img/Task6_2b.png">
</p>

<hr/>
<h3> In <em>My Messages</em> user can see, edit and delete his own messages</h3>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab6/img/Task6_2c.png">
</p>

<hr/>
<h3> Edit for user that owns a message</h3>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab6/img/Task6_2d.png">
</p>

<hr/>
<h3> Successfull delete of user's own message </h3>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab6/img/Task6_2e.png">
</p>

<hr/>
<h3> Now to show more security features I will login as new "test2" user with default role "user" </h3>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab6/img/Task6_2f.png">
</p>

<hr/>
<h3> I quickly created new message as user test2, which can be seen in overall messages view </h3>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab6/img/Task6_2g.png">
</p>

<hr/>
<h3> View of <em>My Messages</em> for user test2 </h3>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab6/img/Task6_2h.png">
</p>

<hr/>
<h3> In this example we can see how "test2" tries to access "My Messagess" view for "test" user, but because he isnt logged in as him, he sees just default "Messages.php" view that is available for all users </h3>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab6/img/Task6_2i.png">
</p>

<hr/>
<h3> User "test2" tries to access edit form for message of id 3 that belongs to user of id 1 (test user) </h3>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab6/img/Task6_2j.png">
</p>

<hr/>
<h3> User "test2" tries to delete message of id 2 that belongs to user of id 1 (test user) via url </h3>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab6/img/Task6_2k.png">
</p>

<hr/>
<h3> Final view for admin or moderator that allows to see and access both edit and delete from "Messages.php" view </h3>
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab6/img/Task6_2l.png">
</p>

<hr/>

# Some Code snippets

message_edit access control:
```
<?php
    if ($_SESSION['role'] === 'moderator' || $_SESSION['role'] === 'admin' ||
      @(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $_GET['owner'])) {
?>
<form method="post" action="messages.php">
    /* SOME CODE */
</form>
<?php
    }else{
        echo "<h1 color='red'>Looks like you are not the owner of the post, so you cannot edit it. </h1>";
    }
?>
```

messages.php rendering content only for particular users:
```
if(isset($messages) && $messages != null) {
        echo "<table>";
        foreach ($messages as $msg):
            echo "<tr>";
            echo "<td>" . htmlspecialchars($msg->name) . "</td>";
            echo "<td>" . $msg->message . "</td>";
            // Show delete link only for moderators or admins
            if ($_SESSION['role'] === 'moderator' || $_SESSION['role'] === 'admin' ||
              @(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $_GET['from_user'])) {
                echo "<td><a href='message_edit.php?id=" . htmlspecialchars($msg->id) .
                      "&owner=".htmlspecialchars($msg->user_id)."'>Edit</a></td>";
            }
            // Show delete link only for moderators or admins
            if ($_SESSION['role'] === 'moderator' || $_SESSION['role'] === 'admin' ||
              @(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $_GET['from_user'])) {
                echo "<td><a href='?delete_message=" . htmlspecialchars($msg->id) .
                      "&owner=".htmlspecialchars($msg->user_id)."' style='color:red;'>Delete</a></td>";
            }
            echo "</tr>";
        endforeach;
        
        echo "</table>";
    }else{
        echo "<h2> Nothing to display </h2>";
    }
```

privileges.php render content only for particular users:
```
<body>
<?php
if ($_SESSION["role"] === 'moderator' || $_SESSION["role"] === 'admin') { ?>
    <main>
    <section>
        /* SOME CODE */
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
```
