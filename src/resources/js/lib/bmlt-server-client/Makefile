openapi.json:
	curl http://localhost:8000/main_server/api/v1/openapi.json > openapi.json

generate: openapi.json
	openapi-generator-cli generate \
	    -i openapi.json \
	    -g typescript-fetch \
	    -o .

clean:
	rm -f openapi.json
	rm -rf src
