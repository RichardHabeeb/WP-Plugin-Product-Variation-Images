all: product-variation-images.php js/*.js
	rm -f Product-Variation-Images.zip
	zip -r Product-Variation-Images.zip . -x ".git/*"



