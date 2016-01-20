Kartulimardikas
===============

Online collection of common algorithms.

Prerequisites
-------------

The following requirements must be met in order to install the application:

* PHP 5.5 or higher
* MySQL 5.5 or higher
* Server software (apache, nginx, ...)
* (Git for recommended deployment) 

Installation
------------

The easiest way to install the application is Git. In order to fetch the latest stable version, use the following command in the installation directory:
 
> \# git clone https://github.com/wurfmaul/kartulimardikas.git

For configuration, change to the `kartulimardikas/` directory and run the following script:

> \# bash maintenance/bootstrap.sh

This will secure the `.git/` directory and create a file `config.php` in the directory `config/`.

Adopt at least the following settings of `config/config.php` to your database configuration:

```
define('DB_HOST',     'localhost');
define('DB_NAME',     'kartulimardikas');
define('DB_USER',     'root');
define('DB_PASSWORD', '');
```

After configuring the database run the following script:

> \# php maintenance/dbsetup.php

This will establish the basic database structure and prepare credentials for an administration user:

```
Username: admin
Password: admin
```

The setup is done. The application can be used right away. Make sure your web server software is configured correctly. Please change the administrator password as soon as possible! 

If you want to pre-populate the system with some data, you can use the file `data.sql` in the `maintenance/` directory.

Update
------

The easiest way to keep the system up-to-date is by calling the following script periodically:

> \# bash maintenance/update.sh

Troubleshouting
---------------

### Error: only header is displayed
* **error.log**: *PHP Fatal error:  Call to undefined method mysqli_stmt::get_result() ...*
* **Problem**: mysql was not compiled with *mysqlnd*
* **Solution**:

> **Ubuntu**: ([package details for 14.04 LTS](http://packages.ubuntu.com/trusty/php5-mysqlnd))  
> \# apt-get install php5-mysqlnd
> \# service apache2 restart