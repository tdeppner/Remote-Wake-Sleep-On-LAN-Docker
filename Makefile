UPSTREAM_IMAGES = $(shell grep ^FROM build/Dockerfile | cut -f2 -d' ')
DEV_IMAGE = $(shell grep image: docker-compose.yml | cut -f2- -d: | xargs)
DEV_PORT = $(shell grep APACHE2_PORT= docker-compose.yml | cut -f2 -d=)

build: dummy
	@echo pulling upstream images
	@for img in ${UPSTREAM_IMAGES}; do docker pull "$${img}"; done
	docker compose build
	make container_versions

rundev: dummy
	docker compose up -d --force-recreate
	open http://localhost:"${DEV_PORT}"

stopdev: dummy
	docker compose rm -fsv

taildev: dummy
	docker compose logs -f &
	sleep 2

loopdev: build rundev taildev

container_versions: dummy
	@docker run \
		--rm \
		--entrypoint /bin/bash \
		"${DEV_IMAGE}" \
		-c 'dpkg -l' > container_versions.txt

dummy:
