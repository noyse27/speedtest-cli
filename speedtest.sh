#!/bin/bash
NOW=$(date +"%a %d-%m-%Y %H:%M:%S")
FILENAME=speedcli__$(date +"%d%m%Y").log
FILENAME2=speedcli.csv
FILEPATH=/volume1/logs/$FILENAME
FILEPATH2=/volume1/logs/$FILENAME2
echo ==================================== $NOW >> ${FILEPATH}
/opt/bin/python /volume1/web/speedtest-cli/speedtest-cli >> ${FILEPATH}
speedtestentry="$(/opt/bin/python /volume1/web/speedtest-cli/speedtest-cli --csv)"
/usr/local/mariadb10/bin/mysql -u speedtest --password='n@4z7jVjh$ZT' -D tools -e "INSERT INTO speedtest Values (0,${speedtestentry},NULL,NULL,NULL,NULL);"
${speedtestentry} >> ${FILEPATH2}
chmod 755 ${FILEPATH} ${FILEPATH2}
chown admin.users ${FILEPATH} ${FILEPATH2}