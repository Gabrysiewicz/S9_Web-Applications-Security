<table align='center'>
  <tr> <td colspan='3' align='center' width='884px'> Web Applications Security </td> </tr>
  <tr> <td colspan="3" align='center'> <img src='https://github.com/Gabrysiewicz/Programowanie-aplikacji-w-chmurze-obliczeniowe/blob/main/logo_politechniki_lubelskiej.jpg' width="400px" height="400px"></td> </tr>
  <tr> <td> Kamil Gabrysiewicz </td> <td> Index: 95400 </td> <td> Grupa: 2.1 </td> </tr>  
  <tr> <td> Wtorek 11:45-13:15 </td> <td> Semestr 2 </td> <td>Laboratorium 5</td></tr>  
</table>


# Task 5.1.
Develop functionalities for managing permissions:
- displaying a list of permissions in the system,
- displaying a list of user permissions with the option of adding and removing permissions,
- displaying a list of roles in the system with the option of adding or removing roles,
- displaying a list of permissions assigned to the role with the option of adding and removing permissions,
- displaying a list of user roles with t;he option of adding or removing roles for the user.

<hr/>

```
use mydb;

CREATE TABLE mydb.user (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(40) NOT NULL,
    phone VARCHAR(12),
    login VARCHAR(30) COLLATE utf8_polish_ci NOT NULL,
    email VARCHAR(60) COLLATE utf8_polish_ci NOT NULL,
    hash VARCHAR(255) COLLATE utf8_polish_ci NOT NULL COMMENT 'password hash or HMAC value',
    salt BLOB DEFAULT NULL COMMENT 'salt to use in password hashing',
    sms_code VARCHAR(6) COLLATE utf8_polish_ci DEFAULT NULL COMMENT 'security code sent via sms or e-mail',
    code_timelife TIMESTAMP NULL DEFAULT NULL COMMENT 'timelife of security code',
    security_question VARCHAR(255) COLLATE utf8_polish_ci DEFAULT NULL COMMENT 'additional security question used while password recovering',
    answer VARCHAR(255) COLLATE utf8_polish_ci DEFAULT NULL COMMENT 'security question answer',
    lockout_time TIMESTAMP NULL DEFAULT NULL COMMENT 'time to which user account is blocked',
    session_id BLOB DEFAULT NULL COMMENT 'user session identifier',
    id_status INT(11) NOT NULL COMMENT 'account status',
    password_form INT(11) NOT NULL DEFAULT 1 COMMENT '1- SHA512, 2-SHA512+salt, 3- HMAC'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;


CREATE TABLE mydb.privilege (
    id INT PRIMARY KEY,
    id_parent_privilege INT,
    name VARCHAR(100) NOT NULL,
    active TINYINT NOT NULL,
    asset_url VARCHAR(200),
    FOREIGN KEY (id_parent_privilege) REFERENCES mydb.privilege(id)
);

CREATE TABLE mydb.role (
    id SMALLINT PRIMARY KEY,
    role_name VARCHAR(30) NOT NULL,
    description TEXT
);

CREATE TABLE mydb.user_role (
    id INT PRIMARY KEY,
    id_role SMALLINT,
    id_user INT,
    issue_time DATE,
    expire_time DATE,
    FOREIGN KEY (id_role) REFERENCES mydb.role(id),
    FOREIGN KEY (id_user) REFERENCES mydb.user(id)
);


CREATE TABLE mydb.role_privilege (
    id INT PRIMARY KEY,
    id_role SMALLINT,
    id_privilege INT,
    issue_time DATE,
    expire_time DATE,
    FOREIGN KEY (id_role) REFERENCES mydb.role(id),
    FOREIGN KEY (id_privilege) REFERENCES mydb.privilege(id)
);

CREATE TABLE mydb.user_privilege (
    id INT PRIMARY KEY,
    id_user INT,
    id_privilege INT,
    FOREIGN KEY (id_user) REFERENCES mydb.user(id),
    FOREIGN KEY (id_privilege) REFERENCES mydb.privilege(id)
);


INSERT INTO mydb.privilege (id, name, active)
VALUES (100, 'add message', 1);

INSERT INTO mydb.privilege (id, name, active)
VALUES (102, 'delete message', 1);

INSERT INTO mydb.privilege (id, name, active)
VALUES (103, 'display private', 1);

INSERT INTO mydb.privilege (id, name, active)
VALUES (101, 'edit message', 1);


CREATE TABLE `message` (
 `id` int(11) NOT NULL,
 `name` varchar(255) COLLATE utf8_polish_ci NOT NULL COMMENT 'name of the message',
 `type` varchar(20) COLLATE utf8_polish_ci DEFAULT NULL COMMENT 'type of the message
(private/public)',
 `message` varchar(2000) COLLATE utf8_polish_ci NOT NULL COMMENT 'message text',
 `deleted` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'existing message - 0, deleted - 1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

INSERT INTO `message` (`id`, `name`, `type`, `message`, `deleted`) VALUES
(1, 'New Intel technology', 'public', 'Intel has announced a new processor for desktops', 0),
(2, 'Intel shares raising', 'private', 'brokers announce: Intel shares will go up!', 0),
(3, 'New graphic card from NVidia', 'public', 'NVidia has announced a new graphic card for desktops', 0),
(4, 'Airplane crash', 'public', 'A passenger plane has crashed in Europe', 0),
(5, 'Coronavirus', 'private', 'A new version of virus was found!', 0),
(6, 'Bitcoin price raises', 'public', 'Price of bitcoin reaches new record.', 0),
(9, 'New Windows announced', 'public', 'A new version of windows was announced. Present buyers of Widows
10 can update the system to the newest version for free.', 0);

ALTER TABLE `message`
 ADD PRIMARY KEY (`id`);
ALTER TABLE `message`
 MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
```

![Task 5.1 a](https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_1a.png)

![Task 5.1 b](https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_1b.png)

![Task 5.1 c](https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_1c.png)

![Task 5.1 d](https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_1d.png)

![Task 5.1 e](https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_1e.png)



# Task 5.2.
Verify the list of effective user permissions - create a set of permissions for the logging in
user, resulting from his permissions and the permissions resulting from the roles assigned to
him. Save a list of permissions in the session. When displaying a page, only show the user the
items for which they have permission.
<h4> View for unlogged user, privileges are hidden </h4>
<p align="center">
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_2a.png" />
</p>

<h4> Logging as a "test" user which has a role of "user" which is default and has the least amount of privileges</h4>
<p align="center">
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_2b.png" />
</p>

<h4> Logged in as "user" role, the privileges navigation is hidden </h4>
<p align="center">
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_2c.png" />
</p>

<h4> The dafault "user" might want to try access localhost/privileges.php but the content is rendered only for privileged roles such as "moderator" and "admin" </h4>
<p align="center">
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_2d.png" />
</p>

<h4> Loggin in as a admin with "admin" role </h4>
<p align="center">
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_2e.png" />
</p>

<h4> Logged in as admin, the privileges navigation is visible and admin can acceess its content </h4>
<p align="center">
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_2f.png" />
</p>

<h4> Admin has access to privileges.php </h4>
<p align="center">
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_2g.png" />
</p>

<hr/>

# Task 5.3.
Add message editing and deleting functions to your application. Make these functionalities
available only to authorized users.

<hr/>
<h4> Messages view for user with role "user", delete buttons are hidden from unprivileged user </h4>
<p align="center">
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_3a.png" />
</p>

<h4> Still "user"  might want to delete message via url `localhost/messages.php?delete_message=1` but its also secured to check user role before commiting such action </h4>
<p align="center">
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_3b.png" />
</p>

<h4> Messages view for admin with role "admin" </h4>
<p align="center">
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_3c.png" />
</p>


<h4> View after successful delete of user's message </h4>
<p align="center">
  <img src="https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab5/img/Task5_3d.png" />
</p>
