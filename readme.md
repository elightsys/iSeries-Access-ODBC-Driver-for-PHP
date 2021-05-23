## Installing iSeries Access ODBC Driver (64bit) for PHP7 on Ubuntu

> Prerequisites: You've got to have PHP7.X installed and running; this isn’t part of the tutorial.

> I used Ubuntu 15.XX LTS.

### How to I do install DB2 ODBC?

#### Step 1 – Install PHP ODBC and Alien
This is the first of the many (seriously, there aren’t THAT many steps) steps to get it running. Get into BASH and then run

```sh
# Example: php7.2-odbc
sudo apt-get install php7.x-odbc alien
```
> We’re installing alien because IBM provide a “Linux compatible” version of the iSeries Access ODBC Driver

#### Step 2 – Download the RPM

You have to registry on the IBM page.
- *https://www.ibm.com/

Than you have to use IBM i Access for Linux V7R1, and then the “For Intel-based Linux workstations"
- https://www14.software.ibm.com/webapp/iwm/web/preLogin.do?source=ial

#### Step 3 – Convert and install the RPM
Run this
```sh
sudo apt-get install odbcinst
```
Create a DIR (64bit)
```sh
sudo mkdir /usr/lib64
```
Upload the **iSeriesAccess-7.1.0-1.0.x86_64.rpm** to your server and run this.
```sh
sudo alien -i -c iSeriesAccess-7.1.0-1.0.x86_64.rpm
```
CREATE mySymLink
```sh
sudo ln -s /usr/lib64/libcw* /usr/lib
```
Than restart your Apache server:
```sh
sudo service apache2 restart
```
#### Step 4 – Create the DSN
Create an **odbc.ini** file
```sh
sudo nano /etc/odbc.ini
```
I set up the /etc/odbc.ini file thusly
```sh
[myDBdev]
Description = iSeries Access ODBC Driver DSN for iSeries
Driver = iSeries Access ODBC Driver
System = 192.168.X.X
UserID = *myDbUser*
Password = *myDbPassword*
Naming = 1
DefaultLibraries = *myLIB*
Database = *myDATABASE*
```
**odbc.ini** details
- *https://www.ibm.com/docs/en/i/7.3?topic=details-connection-string-keywords*

a PHP test script

```php
    <?php
       // odbc.php
		try {
			$conn = new PDO('odbc:myDBdev');
			// The name is the same as what's in our square brackets in ODBC.ini
			$stmt = $conn->prepare("SELECT * FROM myTable WHERE id = :id");
			$stmt->execute(array('id' => 123));
			while ($row = $stmt->fetch()) {
				print_r($row);
				echo '';
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
    ?>
```
    
Then:
```sh
Array(
[ID] => 123,
[0] => 123,
[CURDSC] => My description,
[1] => My description,
[SOMEVAL] => Some value,
[2] => Some value
)
```
