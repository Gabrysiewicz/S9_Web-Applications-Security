<table align='center'>
  <tr> <td colspan='3' align='center' width='884px'> Web Applications Security </td> </tr>
  <tr> <td colspan="3" align='center'> <img src='https://github.com/Gabrysiewicz/Programowanie-aplikacji-w-chmurze-obliczeniowe/blob/main/logo_politechniki_lubelskiej.jpg' width="400px" height="400px"></td> </tr>
  <tr> <td> Kamil Gabrysiewicz </td> <td> Index: 95400 </td> <td> Grupa: 2.1 </td> </tr>  
  <tr> <td> Wtorek 11:45-13:15 </td> <td> Semestr 2 </td> <td>Laboratorium 1</td></tr>  
</table>

# Tasks to do:
## Task 2.1.
Create a "news" database. Inside, create a "message" table and fill it with data. Use the
script in Listing 3.1 to create the table.
```
➜  ~ docker ps
CONTAINER ID   IMAGE            COMMAND                  CREATED         STATUS         PORTS                                     NAMES
19cb67f88ac5   php:8.2-apache   "docker-php-entrypoi…"   8 minutes ago   Up 8 minutes   0.0.0.0:8080->80/tcp, [::]:8080->80/tcp   php-app
7a69e8d62d1a   mysql:8.0        "docker-entrypoint.s…"   8 minutes ago   Up 8 minutes   3306/tcp, 33060/tcp                       mysql-db
```
```
mysql> SHOW TABLES;
+----------------+
| Tables_in_mydb |
+----------------+
| message        |
| user           |
+----------------+
```
```
mysql> SELECT id, login,email,hash FROM user LIMIT 3;
+----+-------+-----------------+----------------------------------+
| id | login | email           | hash                             |
+----+-------+-----------------+----------------------------------+
|  1 | john  | johny@gmail.com | 552d29f9290b9521e6016c2296fa4511 |
|  2 | susie | susie@gmail.com | 8c90f286786c7f3b96564e1e88e0ddab |
|  3 | anie  | anie@gmail.com  | dcb710a566c2a24c8bfaf83618e728f7 |
+----+-------+-----------------+----------------------------------+
```
```
mysql> SELECT * FROM message LIMIT 3;
+----+------------------------------+---------+------------------------------------------------------+---------+
| id | name                         | type    | message                                              | deleted |
+----+------------------------------+---------+------------------------------------------------------+---------+
|  1 | New Intel technology         | public  | Intel has announced a new processor for desktops     |       0 |
|  2 | Intel shares raising         | private | brokers announce: Intel shares will go up!           |       0 |
|  3 | New graphic card from NVidia | public  | NVidia has announced a new graphic card for desktops |       0 |
+----+------------------------------+---------+------------------------------------------------------+---------+
```

## Task 2.2.
Perform the attack from Figure 3.2 again. This time, however, include the user's login, hash and salt in the message body.
![Task2.2a]()

## Task 2.3.
Perform the attack from Figure 3.2 again. This time, however, include another user's details in the body of the message.
![Task2.3]()

## Task 2.4.
Add the ability to edit messages to the application. Verify what SQLI attacks are possible against the added module.

## Task 2.5.
Add a new database user. Define the minimum required set of permissions for it (show what set it is in the report). 
Use the newly created account to connect the application to thedatabase. 
Verify what has changed in terms of application security.
Tip. Try the attack in Figure 3.10 again

## Task 2.6.
Modify the application. Use only PDO to connect to the database. Place the code for
handling the database in the Db class. In each case, the data should be retrieved by a dedicated
function that is called in the PHP page code. The function should return the page a set of data
to display

## Task 2.7.
Include user input filtering in your application

## Task 2.8.
Apply whitelist to filter message type.
Tip: Notice that the message type is selected by the user from a list. This does not mean,
however, that you cannot insert any other string of characters there. This is possible using
developer tools that modify the content of the page (in the Chrome browser by pressing the
F12 button). Therefore, you need to verify whether the value transferred from this field is
included in the set (public, private)

## Task 2.9.
Modify the application. Develop comprehensive application security against SQLI attacks.
Create an additional Filter class. Include all the functionalities necessary to filter data. Add
filtering functions for data downloaded from the form. In the Db class, modify the functions
for adding data to the database so that each database function filters the data before using it.
The purpose of these modifications is to protect against programmer mistakes. He will not
have to remember to filter data. Each time the database function is called, the filter function
will be automatically invoked.
Tip: The presented approach to filtering and inserting data into the database may require
rebuilding database functions. The name, email address and URL will be filtered differently.
This problem can be solved in two ways:
1 Develop independent functions for different types of data:
  - addName
  - addURL
  - addEmail
2 Develop one generic function for inserting data and pass the type of filter that should be
used to the function as a parameter.

## Task 2.10.
Verify the vulnerability of the secured application to SQLI attacks. Conduct several
selected attacks on the application and present their results..
