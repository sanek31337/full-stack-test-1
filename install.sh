#!/bin/bash

echo
echo "================= Updating Composer Dependencies ====================="
echo

composer install --no-dev --optimize-autoloader

npm install

npm run prod
