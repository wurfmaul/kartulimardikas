#!/bin/bash

DIR=$(dirname $(readlink -e $0))

echo -n "Secure .git directory..."
echo "Require all denied" > $DIR/../.git/.htaccess
echo "DONE"

echo -n "Prepare config file....."
cp $DIR/../config/config.default.php $DIR/../config/config.php
echo "DONE"