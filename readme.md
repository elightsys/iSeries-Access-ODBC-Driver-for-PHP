## Installing iSeries Access ODBC Driver (64bit) for PHP8 on Ubuntu/Debian

> Prerequisites: You must have PHP 8.1.X installed and running; this is not covered in this tutorial.

> Tested Environment: Ubuntu 20.04.6 LTS.

### How do I install the DB2 ODBC Driver?

#### Step 1 – Install PHP ODBC and Alien
This step is the beginning of a few straightforward steps to get everything up and running. Open your terminal and run:

```sh
sudo apt update

# Example:
sudo apt install php8.1-odbc alien
```
> Note: We are installing 'alien' because IBM provides a "Linux compatible" version of the iSeries Access ODBC Driver.

#### Step 2 – Download the RPM

You need to register on the IBM website.
- IBM Website: [IBM Registration Page](https://www.ibm.com/)

Then download "IBM i Access for Linux V7R1" for "Intel-based Linux workstations".

- Download Link: [IBM i Access V7R1](https://www14.software.ibm.com/webapp/iwm/web/preLogin.do?source=ial)

#### Step 3 – Convert and install the RPM
First, install the necessary ODBC installer:
```sh
sudo apt install odbcinst
```
Create a directory for 64-bit libraries:
```sh
sudo mkdir /usr/lib64
```
Upload the **iSeriesAccess-7.1.0-1.0.x86_64.rpm** to your server and run:
```sh
sudo alien -i -c iSeriesAccess-7.1.0-1.0.x86_64.rpm
```
Create a symbolic link to make the missing parts of the ODBC driver library available:
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
Configure the **/etc/odbc.ini** file as follows:
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
**odbc.ini** details:
- Connection String Keywords: [IBM Documentation on Connection String Keywords](https://www.ibm.com/docs/en/i/7.3?topic=details-connection-string-keywords)

**Testing with a PHP Script**

Here’s a PHP script to test your setup:

```php
<?php
	// example odbc.php
	try {
		$conn = new PDO('odbc:myDBdev'); // The name matches what's in the ODBC.ini file
		$stmt = $conn->prepare("SELECT * FROM myTable WHERE id = :id");
		$stmt->execute(array('id' => 123));
		while ($row = $stmt->fetch()) {
			print_r($row);
			echo PHP_EOL;  // Improved line break for clarity
		}
	} catch (PDOException $e) {
		echo $e->getMessage();
	}
?>
```
    
- <i>Example output:</i>
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
