# CL-Test
Person-Address Test Project

All source files, including exported tables, are contained in the CL_Test folder.
This project uses 3 MySql tables, address_tbl, person_tbl, and person_address_rel, in a database named cl_test_db.
address_tbl holds the Address records, person_tbl holds the Person records, and person_address_rel holds the relations 
between the two.

The project was devloped and tested on an Ubuntu Linux system with a basic LAMP install.
The IDE used for development was Bluefish, and phpMyAdmin was used for the database creation.
index.html is the starting page.

Note on file management: I typically would not throw a whole project into just one folder. This project is rather small, 
- so I made an exception.

Basic Application Flow:
viewinterface.php gets the requests from index.html, accesses the necessary objects to fulfill requests and sends back content.
dbmanager.php creates, opens, and closes the database connection. The connection is passed to any objects requiring database
access.

dbfunctions.php manages add, update, and delete requests from viewinterface.php.
dbfunctions.php accesses objects in the following to carry out the add, delete, and updates:
addressadd.php, addressdelete.php, addressupdate.php, 
personadd.php, persondelete.php, personupdate.php,
relationadd.php, relationdelete.php, relationupdate.
dbfunctions.php accesses the Search object in search.php to do any utilty searches it needs.

Note on database delete functions: This projects utilized "soft delete" instead of "hard deletes" on the data base. 
- An "Active" column was included in each table, and record was considered "deleted" when set to 0 (false). This provided
- the bonus of "undeleting" a record if the user decided to add it again. 

tblrow.php contains an abstract class (TblRow) used to create Person and Address.

person.php, address.php, and search.php contain the objects Person, Address, and Search, respectively.

search.php handles all search requests and is accesed by viewinterface.php and dbfunctions.php.

w3.css was copied from w3Schools for some of the styles used in index.html.

unittest.php was used for some initial testing before viewinterface.php was developed and abandoned after that.
