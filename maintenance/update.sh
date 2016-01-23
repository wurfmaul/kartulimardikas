#!/bin/bash

DIR=$(dirname $(readlink -e $0))
BASEDIR=${DIR}/..

echo "Update kartulimardikas:"
git -C ${BASEDIR} pull

# PARSEDOWN
echo ""
echo "Update library 'parsedown':"
git -C ${BASEDIR}/lib/parsedown pull