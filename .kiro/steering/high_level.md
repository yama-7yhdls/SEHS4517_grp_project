---
inclusion: always
---
<!------------------------------------------------------------------------------------
   Add rules to this file or a short description and have Kiro refine them for you.
   
   Learn about inclusion modes: https://kiro.dev/docs/steering/#inclusion-modes
-------------------------------------------------------------------------------------> 

# Project Specification: 
Design and develop a reservation system for a facility of an organization (e.g., a room in a hotel ) 

## Develop 5 web pages with your own design as follows: 
- The first web page is an “introduction” web page which introduces your  organization (e.g.,  hotel).  The  number  of  words  for  this  introduction should NOT exceed 600 words. This web page contains a logo of your organization created by you and hyperlinks/buttons to connect to the second or third web page. 

- The second web page is a “member registration” page which requires the user to enter the  last  name,  first  name,  mailing  address,  contact  phone  number, email  address  and password. This page contains a button labelled with “Register” for the user to press and send the user input to a PHP program which is used to store the user input in a table in MySQL database. This page also contains a button labelled with “Clear” which is used to clear the user input and allow the user to re-enter the particulars. This second web page contains hyperlinks/buttons to connect to the first or third web page.

- The third web page is a “login to reserve” page which requires the user to enter the email address and password which are then sent to a PHP program that checks with MySQL database if the email address and password are correct. If they are incorrect, the PHP program generates a web page showing the “sorry, login failed” message created by you with a button for the user to press and go back to the first web page. If the email address and password are correct, the PHP program generates the fourth web page.

- The fourth web page is a “reservation” web page which asks the user to enter the date and time slot for reserving a facility. Then, this page shows the facility items available for the user to reserve according to the user-entered date and time. Then, the user can choose a particular item (e.g., a particular hotel room, music hall, classroom, seat(s) in cinema, theatre, storeroom, container, car, fitness equipment, …) and press a button labelled with “Reserve”. After that, the user-entered date and time and the chosen facility item are sent to a PHP program which stores the reservation information in a table in MySQL database and sends the user’s email address and the reservation information to a Node.js server program using Express.js. This Express.js program generates the fifth web page. Or, the user presses a button labelled with “Clear” which is used to clear the user input and the user can enter the date and time for reservation again. Or, the user presses a button labelled with “Cancel” which links to the first web page.

- The fifth web page just shows “thank you” message, the user’s email address and the reservation information. This is a button labelled with “OK” on this page for the user to press and go back to the first web page.

# Project Implementation:
- Install Apache, Node.js, and Express.js.
- Use HTML5 that follows XHTML syntax rules, JavaScript, jQuery to develop client-side web pages.
- Use PHP and Node.js with Express.js to develop server-side programs.
- Test your web pages and programs with Apache server and Node.js server.

