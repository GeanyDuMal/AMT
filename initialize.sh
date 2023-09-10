composer install
php bin/console doctrine:database:drop --if-exists --force
php bin/console doctrine:database:create
php bin/console doctrine:migration:migrate --all-or-nothing
php bin/console doctrine:fixture:load --append
