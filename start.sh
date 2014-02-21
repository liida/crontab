#!/bin/bash
TASK="${BASH_SOURCE-$0}"
TASK=`dirname ${TASK}`
TASKDIR=`cd ${TASK}; pwd`
TASKLOGDIR=`cd ${TASK};cd logs; pwd`
echo "oh yes!the script is task,its running!"
nohup php ${TASKDIR}/task.php 1>${TASKLOGDIR}/task.log 2>/dev/null &
