<?php
include_once "classes/Page.php";
include_once "classes/Db.php";
Page::display_header("Add Message");
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
Page::display_navigation();
?>
</body>
</html>
