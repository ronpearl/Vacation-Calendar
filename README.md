# Vacation-Calendar
Calendar Program For Small Work Environment

Notes:
-
1. This project is currently in the process of being designed and you may encounter some flaws. 
2. It was designed to function on a small scale.
3. The database files were manually added, so you wil need to import them into your server.
4. Currently there is no admin section for adding users.  They will need to be added via the DB table "vacations_users".

Setup:
-
By default the program is set to use a database called "vacations".  The MySql export files are included in the download package. Once this is setup, you can edit the "includes/baseConnection.php" file with your servers connection information.

You will need to manually create users in the database table called "vacations_users".  This will be a functionality in the admin section at a later date.

The program settings are currently located in the "settings.php" file.  Here you can setup the email information for emails that are sent due to new vacation requests, select holiday days to show, and the ability to enable/disable weekly on call schedules.

Once these things are completed, your program should be functioning.

On Call:
-
This was setup to be a weekly schedule only.  Per day on call service has not been setup yet.
