Kartulimardikas
===============

Online collection of common algorithms.


Troubleshouting
---------------

###Error: only header is displayed
* **error.log**: *PHP Fatal error:  Call to undefined method mysqli_stmt::get_result() ...*
* **Problem**: mysql was not compiled with *mysqlnd*
* **Solution**: 
> **Ubuntu**: ([package details for 14.04 LTS](http://packages.ubuntu.com/trusty/php5-mysqlnd))  
>    # apt-get install php5-mysqlnd
>
>    # service apache2 restart