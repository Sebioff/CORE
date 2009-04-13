#!/bin/bash

if [ "$1" != "" ]
then
    DIR=$1
else
    DIR="."
fi

for i in `ls $DIR`
do
    echo $i;
  if [ `stat -c "%Y" $i` \< `date +%s` ]
  then
    rm -f $i
  fi
done
