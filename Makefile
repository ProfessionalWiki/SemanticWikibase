.PHONY: ci cs test phpunit psalm phpstan psalm-mw

test: phpunit
cs: phpstan psalm
ci: phpstan phpunit psalm
full-ci: ci psalm-mw

phpunit:
	php ../../tests/phpunit/phpunit.php -c phpunit.xml.dist

psalm:
	./vendor/bin/psalm -c psalm-full.xml

psalm-mw:
	./vendor/bin/psalm -c psalm-mw.xml

phpstan:
	./vendor/bin/phpstan analyse -c phpstan.neon --no-progress
