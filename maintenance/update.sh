#!/bin/bash

DIR=$(dirname $(readlink -e $0))
BASEDIR=${DIR}/..

git -C ${BASEDIR} pull
git -C ${BASEDIR}/lib/parsedown pull