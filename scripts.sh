#!/bin/bash
while true; do
    begin=`date +%s`
   cd /var/www/html/
   php artisan endOngoing
     end=`date +%s`
  if [ $(($end - $begin)) -lt 10 ]; then
     sleep $(($begin + 10 - $end))
 fi
done
