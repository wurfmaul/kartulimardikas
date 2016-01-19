#!/bin/bash

echo -n "Secure .git directory..."
echo "Require all denied" > ../.git/.htaccess
echo "DONE"

echo -n "Prepare config file....."
cp ../config/config.default.php ../config/config.php
echo "DONE"