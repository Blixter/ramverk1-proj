#!/usr/bin/env bash

# Create a database file and change the mode
echo "Creating database file: 'data/db.sqlite'..."
touch data/db.sqlite
chmod 666 data/db.sqlite

echo "Creating tables for database..."
# Create the database tables
sqlite3 data/db.sqlite < sql/ddl/post_sqlite.sql
sqlite3 data/db.sqlite < sql/ddl/user_sqlite.sql

echo "Inserting default data...."
# Inserting data
sqlite3 data/db.sqlite < sql/ddl/insert.sql

echo ""
echo "Database successfully initialized."

