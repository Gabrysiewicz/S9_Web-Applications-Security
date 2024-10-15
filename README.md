<table align='center'>
  <tr> <td colspan='3'> <img width="884px" > </td> </tr>
  <tr> <td colspan="3" align='center'> <img src='https://github.com/Gabrysiewicz/Programowanie-aplikacji-w-chmurze-obliczeniowe/blob/main/logo_politechniki_lubelskiej.jpg' width="400px" height="400px"></td> </tr>
  <tr> <td> Kamil Gabrysiewicz </td> <td> Index: 95400 </td> <td> Grupa: 2.1 </td> </tr>  
  <tr> <td> Semestr 2 </td> <td colspan='2' align='center'> Web Applications Security </td> </tr>  
</table>

<h1> Tasks to do: </h1>

## 1. Prepare an environment to verify web application security:
   1. Create a virtual machine and install operating system
   2. Install XAMPP or any other package that allows to run PHP application with MySQL database (https://www.apachefriends.org/pl/download.html)
   3. Install OWASP ZAP application (https://www.zaproxy.org/gettingstarted/)
   4. Install and run application delivered for laboratory 1 (available on moodle course: Laboratory 01 – additional files)
      1. Download and decompress file
      2. Copy APPv1 folder to XAMPP/htdocs folder
      3. With phpMyadmin create database named “public”
      4. Copy the content of the db_create_v3.sql file and execute it in the created database. Verify if tables were created and contain data.
      5. Run installed application by typing in a browser http://localhost/APPv1/index.php. Check if application is working.
      

### Outcome: Application is running
<img src='https://github.com/Gabrysiewicz/Programowanie-aplikacji-w-chmurze-obliczeniowe/blob/main/logo_politechniki_lubelskiej.jpg' width="600px" height="600px">
      
         
## 2. Run ZAP application and use it to test web application installed in point 1 of this instruction:
   1. Start ZAP application
   2. Choose to not persist session at this moment 
   3. Select Quick Start tab and type the address of the application to scan
   4. Start verification by clicking Attack button

### Outcome: Zap is scanning the application
<img src='https://github.com/Gabrysiewicz/Programowanie-aplikacji-w-chmurze-obliczeniowe/blob/main/logo_politechniki_lubelskiej.jpg' width="600px" height="600px">


## 3. Analyse obtained results. Please create a report containing the list of found vulnerabilities. Analyse the fixes proposed by ZAP program and propose a solution to each of found vulnerabilities on the base of information from ZAP program and the Internet.

   
## 4. Use ZAP program to verify your own application. Use an application you’ve already created. Put the analysis in the report. 
