#!/bin/bash
TASK="${BASH_SOURCE-$0}"
TASK=`dirname ${TASK}`
TASKDIR=`cd ${TASK}; pwd`
TASKLOGDIR=`cd ${TASK};cd logs; pwd`
sh ${TASKDIR}/stop.sh
sh ${TASKDIR}/start.sh