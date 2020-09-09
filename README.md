# Poker_Hands

Steps to set up project

1.  clone git repo to local system using -----
2.  run "docker run --network local -p 3306:3306 --name pokerhandsDB -e MYSQL_ROOT_PASSWORD=root -d mariadb:latest" to spin up a MariaDB docker instance
3.  access database via an SQL workbench using "127.0.0.1" as the host, "root" for the username, and "root" for the password
4.  create a new table named "pokerhands"
5.  import pokerhands_2020-09-08.sql from the /sql_dump folder
6.  cd into the project file you cloned earlier and run "symfony start:server"
7.  Once the server is started move to your browser and load your localhost on http://127.0.0.1:8000
8.  login using "root@local.local" for the email and "root" for the password
9.  upload the txt file and view the answers given
