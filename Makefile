up:
	docker-compose up -d

certs:
	bash -c 'openssl req -x509 -newkey rsa:2048 -keyout ~/.dinghy/certs/www.security.dev.key \
    	-out ~/.dinghy/certs/www.security.dev.crt -days 365 -nodes \
    	-subj "/C=PL/ST=Mazowieckie/L=Warsaw/O=Markiewicz/OU=Org/CN=*.security.dev" \
    	-config <(cat /etc/ssl/openssl.cnf <(printf "[SAN]\nsubjectAltName=DNS:*.security.dev")) \
    	-reqexts SAN -extensions SAN'
