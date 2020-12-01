php:
	docker run -it --rm \
		-v "`pwd`":/source \
		-w /source \
		--entrypoint bash \
		php:7.4
composer:
	docker run -it --rm \
		-v "`pwd`":/source \
		-w /source \
		--entrypoint bash \
		composer
