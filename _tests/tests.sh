#!/bin/bash


# predpokladejme testovani na testovaci databazi kterou si sami vytvorime

if [ -z "$1" ]; then
	HELP_PASSWORD='celer4000'
	read -t 15 -p "Vyplnte prosim sve heslo pro MYSQL ROOT USERA [${HELP_PASSWORD}]: " MYSQL_PASSWORD
	MYSQL_PASSWORD=${MYSQL_PASSWORD:-$HELP_PASSWORD}
else
	MYSQL_PASSWORD="$1"
fi

TEST_DATABASE="orm_test_db"

MYSQL_OPTS="-u root -p${MYSQL_PASSWORD}"
MYSQL_DAEMON=$(which mysql)

PHPUNIT_RUNNER=$(which phpunit)

SCRIPT_LOCATION="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd ${SCRIPT_LOCATION}

echo -e "Smazani a vytvoreni testovaci DB jmenem ${TEST_DATABASE} a vytvoreni testovaciho usera ormtestuser"

${MYSQL_DAEMON} ${MYSQL_OPTS} -Bse "DROP DATABASE IF EXISTS \`${TEST_DATABASE}\`;"
${MYSQL_DAEMON} ${MYSQL_OPTS} -Bse "CREATE DATABASE \`${TEST_DATABASE}\` COLLATE utf8_unicode_ci;"
${MYSQL_DAEMON} ${MYSQL_OPTS} -Bse "CREATE USER 'ormtestuser'@'localhost' IDENTIFIED BY 'testpass';"
${MYSQL_DAEMON} ${MYSQL_OPTS} -Bse "GRANT ALL PRIVILEGES ON \`${TEST_DATABASE}\`.* TO 'ormtestuser'@'localhost';"
${MYSQL_DAEMON} ${MYSQL_OPTS} -Bse "FLUSH PRIVILEGES;"
${MYSQL_DAEMON} ${MYSQL_OPTS} -u ormtestuser -ptestpass --database ${TEST_DATABASE} < ./database.sql

${PHPUNIT_RUNNER} --bootstrap ./_bootstrap.php ./

echo -e "Smazani testovaci db a usera"
${MYSQL_DAEMON} ${MYSQL_OPTS} -Bse "DROP USER 'ormtestuser'@'localhost';"
${MYSQL_DAEMON} ${MYSQL_OPTS} -Bse "DROP DATABASE IF EXISTS \`${TEST_DATABASE}\`;"