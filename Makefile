php:
	docker run -it --rm \
		-v "`pwd`":/source \
		-w /source \
		--entrypoint bash \
		php:8
composer:
	docker run -it --rm \
		-v "`pwd`":/source \
		-w /source \
		--entrypoint bash \
		composer
