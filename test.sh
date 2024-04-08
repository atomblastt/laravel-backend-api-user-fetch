#!/bin/bash

# get env.testing
php artisan migrate:fresh --env=testing
php artisan db:seed --env=testing
if [[ -z $1 ]]; then
	./vendor/bin/pest --coverage

else
	./vendor/bin/pest
fi
