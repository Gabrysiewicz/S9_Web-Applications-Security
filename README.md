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
Attempt of accessing the view that isnt made for unprivileged, unlogged user will wont work and view won't be even rendered for attacker.
<p align='center'>
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab6/img/Task6_1b.png">
</p>

# Task 6.2.
Implement the ability to view and edit your own messages in the application. Add a "my
messages" page. Use the existing function to edit messages. Only modify the access control
code there. Users with appropriate permissions and the message creator are to be authorized to
edit messages.

<hr/>
<h3> Logging is a user "test" with role user" </h3>
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
<h3> IN this example we can see how "test2" tries to access "My Messagess" view for "test" user, but because he isnt logged in as him, he sees just default "Messages.php" view that is available for all users </h3>
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


