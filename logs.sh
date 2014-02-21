#!/bin/bash
TASK="${BASH_SOURCE-$0}"
TASK=`dirname ${TASK}`
TASKDIR=`cd ${TASK}; pwd`
TASKLOGDIR=`cd ${TASK};cd logs; pwd`
YEAR=`date -d last-month +%Y`
MON=`date -d last-month +%m`
cd ${TASKLOGDIR}
rm -rf ${YEAR}/${MON}