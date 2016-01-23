#!/bin/bash

DIR=$(dirname $(readlink -e $0))
BASEDIR=${DIR}/..

echo -n "Secure .git directory....."
if [ ! -f ${BASEDIR}/.git/.htaccess ]; then
    echo "Require all denied" > ${BASEDIR}/.git/.htaccess
    echo "DONE"
else
    echo "SKIPPED"
fi

echo -n "Prepare config file......."
if [ ! -f ${BASEDIR}/config/config.php ]; then
    cp ${BASEDIR}/config/config.default.php ${BASEDIR}/config/config.php
    echo "DONE"
else
    echo "SKIPPED"
fi

echo "Load external libraries:"
if [ ! -d ${BASEDIR}/lib ]; then
    mkdir ${BASEDIR}/lib
    echo "Require all denied" > ${BASEDIR}/lib/.htaccess
fi

# PARSEDOWN
if [ ! -d ${BASEDIR}/lib/parsedown ]; then
    git -C ${BASEDIR}/lib clone https://github.com/erusev/parsedown
    echo "Library parsedown........DONE"
else
    echo "Library parsedown........SKIPPED"
fi