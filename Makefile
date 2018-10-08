install:
	composer install
lint:
	composer run-script phpcs -- --standard=PSR2 app bootstrap config public routes tests
test:
	composer run-script test
run:
	php -S localhost:8000 -t public
logs:
	tail -f storage/logs/lumen.log
