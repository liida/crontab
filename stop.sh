#!/bin/bash 
echo "oh yes!the script is task,its exit!";
pid=`ps -ef|grep task.php|grep -v grep|awk '{print $2}'`
for i in `ps -ef|grep $pid|grep -v grep |awk '{print $2}'`
do
  kill -9 $i
done
kill -9 $pid