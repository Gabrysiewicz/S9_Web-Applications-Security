<table align='center'>
  <tr> <td colspan='3' align='center' width='884px'> Web Applications Security </td> </tr>
  <tr> <td colspan="3" align='center'> <img src='https://github.com/Gabrysiewicz/Programowanie-aplikacji-w-chmurze-obliczeniowe/blob/main/logo_politechniki_lubelskiej.jpg' width="400px" height="400px"></td> </tr>
  <tr> <td> Kamil Gabrysiewicz </td> <td> Index: 95400 </td> <td> Grupa: 2.1 </td> </tr>  
  <tr> <td> Wtorek 11:45-13:15 </td> <td> Semestr 2 </td> <td>Laboratorium 3</td></tr>  
</table>

# Task 3.1.
Based on the lecture materials and sources available on the Internet, develop several XSS
attacks on the application used during the laboratory. Present the attacks and their effects.
Evaluate how dangerous they may be for the application.
<hr/>
With the use of XSS the content and the visuals of the website could be changed in a way not intended by the developer.
Additional unitended content such as links can be added with a delivery to eitherway harmfull or suspicious website, 
like scam websites or downloadable content that might be malware.

![Task 3.1a](https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab3/img/Task3_1a.png)

![Task 3.1b](https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab3/img/Task3_1b.png)

There might be more harmfull ways of using XSS such as keylogger that in other scenario would send data to some endpoint.
With the use of XSS attacker might be able to steal cookie, or insert a form that might convince user into inserting sensitive data
into it.

![Task 3.1c](https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab3/img/Task3_1c.png)

![Task 3.1d](https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab3/img/Task3_1d.png)

# Task 3.2.
Verify the operation of the addslashes function in the context of protection against XSS
attacks. Check if this feature prevents HTML or JavaScript injection.
<hr/>

```
if (isset($_POST['add_message'])) {
    // whitelist
    $allowed_types = ['public', 'private'];

    try {
        $name = addslashes($_REQUEST['name']);
        $type = addslashes($_REQUEST['type']);
        $content = addslashes($_REQUEST['content']);

        if (!$db->addMessageBasic($name, $type, $content)) {
            echo "<p style='color:red;'>Adding new message failed.</p>";
        }
    } catch (InvalidArgumentException $e) {
        echo "<p style='color:red;'>{$e->getMessage()}</p>";
    }
}
```
```
public function addMessageBasic($name, $type, $content) {
        $name = addslashes($_REQUEST['name']);
        $type = addslashes($_REQUEST['type']);
        $content = addslashes($_REQUEST['content']);

        $sql = "INSERT INTO message (`name`, `type`, `message`, `deleted`) VALUES (:name, :type, :content, 0)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':content', $content);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Add message failed: " . $e->getMessage();
            return false;
        }
    }
```
![Task 3.2](https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab3/img/Task3_2.png)

The use of **addslashes()** partialy worked, it didnt allowed keyloger and other style based XSS but it still somehow allowed suspicious link to pass.
So as it solved some issues with XSS there are still some left to take care of.

# Task 3.3.
Protect the rest of your application against XSS attacks. Modify the Filter class created in
the previous lab so that it filters data not only for SQLI attacks but also for XSS attacks.
<hr/>

# Task 3.4.
Verify the vulnerability of the secured application to XSS attacks. Conduct several selected
attacks on the application and present their results.
<hr/>

