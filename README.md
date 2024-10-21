<table align='center'>
  <tr> <td colspan='3' align='center' width='884px'> Web Applications Security </td> </tr>
  <tr> <td colspan="3" align='center'> <img src='https://github.com/Gabrysiewicz/Programowanie-aplikacji-w-chmurze-obliczeniowe/blob/main/logo_politechniki_lubelskiej.jpg' width="400px" height="400px"></td> </tr>
  <tr> <td> Kamil Gabrysiewicz </td> <td> Index: 95400 </td> <td> Grupa: 2.1 </td> </tr>  
  <tr> <td> Wtorek 11:45-13:15 </td> <td> Semestr 2 </td> <td>Laboratorium 1</td></tr>  
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
<div align='center'>

  ![application](https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/main/ApplicationIsRunning.png)

</div>
         
## 2. Run ZAP application and use it to test web application installed in point 1 of this instruction:
   1. Start ZAP application
   2. Choose to not persist session at this moment 
   3. Select Quick Start tab and type the address of the application to scan
   4. Start verification by clicking Attack button

### Outcome: Zap is scanning the application
<div align='center'>

![zap](https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/main/ZapIsScanning.png)

</div>

## 3. Analyse obtained results. Please create a report containing the list of found vulnerabilities. Analyse the fixes proposed by ZAP program and propose a solution to each of found vulnerabilities on the base of information from ZAP program and the Internet.
<div align='center'>

![zap alert output](https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/main/ZapRaportAlerts.png)

</div>

Detected alerts:
- SQL Injection - MySQL (1)
  - Possibility of SQL Injection has been found in the application by ZAP.
  - The suggestion from ZAP is to never trust the client and client-side validation, there should be server-side validation to prevent unwanted and harming SQL Queries.
- Buffer overflow (1)
  - These errors end execution of the application in an unexpected way.
  - Rewrite the background program using proper return length checking. This will require a recompile of the background executable. 
- GET for POST (1)
  - It may facilitate simplification of other attacks. For example if the original POST is subject to Cross-Site Scripting (XSS), then this finding may indicate that a simplified (GET based) XSS may also be possible.
  - Ensure that only POST is accepted where POST is expected.
- User Agent Fuzzer (1)
  - Check for differences in response based on fuzzed User Agent. Compares the response statuscode and the hashcode of the response body with the original response.
  - Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)

## 4. Use ZAP program to verify your own application. Use an application you’ve already created. Put the analysis in the report. 
I have used one of my applications for subject "IT System integrations" that was supposed to be a `Summary of statistical data on games on Steam and Epic Games Store platforms`.
The app is built with `Laravel` with the use of `docker` containers.
![MyApp](https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab1/MyApplication.png)

<div align='center'>

![Zap for my App](https://github.com/Gabrysiewicz/S9_Web-Applications-Security/blob/lab1/MyAppZapScreen.png)

</div>

There were no error results from ZAP, it's probably due to the use of a framework that resolves many security issues, but nevertheless there was an alert:
- .htaccess Information Leak (1)
  - htaccess files can be used to alter the configuration of the Apache Web Server software to enable/disable additional functionality and features that the Apache Web Server software has to offer.
  - Ensure that the .htaccess file is not accessible. This can be achieved either way by setting the correct file ownership `chown 644 or 755 for .htaccess` or by modifying the `httpd.conf` file for the Apache server.
