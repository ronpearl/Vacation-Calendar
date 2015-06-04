# Vacation-Calendar
Calendar Program For Small Work Environment

What It Does:
-
Do you ever wonder who in your department has a certain day off?  Are you required to check with your manager to verify days off, but can't get ahold of them when you need them?  Well this calendar can help!

Working in a small team of people is fun, but when you need a vacation it's hard to know who will be out and when.  It's really nice to know when your peers are either going to be away from the office, on vacation or who is on call during a certain week.  This program will display a calendar, show who has what days off, display if they were approved or not, and also show a description of their leave.

There is an admin panel that allows for editing requests and managing on call personnel.

Sure, there are plenty of other options out there, but why not use something that was built specifically for a team??

Notes:
-
1. This is my first EVER GIT publication!!!   This project is currently in the process of being designed and you may/will encounter some flaws. 
2. It was designed to function on a small scale.
3. The database files were manually added, so you wil need to import them into your server.

Setup:
-
By default the program is set to use a database called "vacations".  The MySql export files are included in the download package. Once this is setup, you can edit the "includes/baseConnection.php" file with your servers connection information.

The program settings are currently located in the "settings.php" file.  Here you can setup the email information for emails that are sent due to new vacation requests, select holiday days to show, and the ability to enable/disable weekly on call schedules.

Once these things are completed, your program should be functioning.

On Call:
-
This was setup to be a weekly schedule only.  Per day on call service has not been setup yet.
