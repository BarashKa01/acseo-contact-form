# acseo-contact-form


Change .env database configuration if needed (dont change dev to prod env because of dependencies missing or edit the composer.json).

composer install

php bin/console doctrine:database:create

php bin/console doctrine:migrations:migrate

php bin/console doctrine:fixtures:load



You can test it

PS : For security purpose, to connect to the back office, use admin & admin as password. Also, users requests are grouped by mail.
