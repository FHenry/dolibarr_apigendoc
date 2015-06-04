API Gendoc
=========

This module provide a REST API do launch dolibarr PDF generation by URL call

Licence
-------

GPLv3 or (at your option) any later version.

See COPYING for more information.

INSTALL
-------

- Make sure Dolibarr (v >= 3.3) is already installed and configured on your server.

- In your Dolibarr installation directory, edit the htdocs/conf/conf.php file

- Find the following lines:

		//$=dolibarr_main_url_root_alt ...
		//$=dolibarr_main_document_root_alt ...

- Uncomment these lines (delete the leading "//") and assign a sensible value according to your Dolibarr installation

	For example :

	- UNIX:

			$dolibarr_main_url_root = 'http://localhost/Dolibarr/htdocs';
			$dolibarr_main_document_root = '/var/www/Dolibarr/htdocs';
			$dolibarr_main_url_root_alt = 'http://localhost/Dolibarr/htdocs/custom';
			$dolibarr_main_document_root_alt = '/var/www/Dolibarr/htdocs/custom';

	- Windows:

			$dolibarr_main_url_root = 'http://localhost/Dolibarr/htdocs';
			$dolibarr_main_document_root = 'C:/My Web Sites/Dolibarr/htdocs';
			$dolibarr_main_url_root_alt = 'http://localhost/Dolibarr/htdocs/custom';
			$dolibarr_main_document_root_alt = 'C:/My Web Sites/Dolibarr/htdocs/custom';

	For more information about the conf.php file take a look at the conf.php.example file.

	*Note that in the upcoming Dolibarr 3.5, the $dolibarr\_main\_url\_root\_alt will become a relative path*

- Clone the repository in $dolibarr\_main\_document\_root\_alt/mymodule

	*(You may have to create the custom directory first if it doesn't exist yet.)*

	```
	git clone --recursive git@github.com:rdoursenaud/dolibarr-module-template.git mymodule
	```

	**The template now uses a git submodule to fetch the PHP Markdown library.**

	If your git version is less than 1.6.5, the --recursive parameter won't work.

	Please use this instead to fetch the latest version:

		git clone git@github.com:FHenry/dolibarr_api_gendoc.git dolibarr_api_gendoc
		cd dolibarr_api_gendoc
		git submodule update --init
		php composer.php install
		php composer.php update

- From your browser:

	- log in as a Dolibarr administrator

	- go to "Setup" -> "Modules"

	- the module is under tabs "module interface"

	- Find module API GenDoc and activate it

	- Go to module configuration and generate a secret key and click on save button

	- After that from your browser goto your dolibarr, 'let's take previous exemple
		http://localhost/Dolibarr/htdocs/custom/apigendoc/restapi/restapi.php/gendoc/DOCTYPE/DOCID?key=SECRETKEY

		Actually only DOCTYPE "facture" works
		DOCID is the rowid from document
		SECRETKEY is the secret key define in config module

Contributions
-------------

Feel free to contribute and report defects at <https://github.com/FHenry/dolibarr_api_gendoc>

Other Licences
--------------

Uses [Michel Fortin's PHP Markdown](http://michelf.ca/projets/php-markdown/) Licensed under BSD to display this README in the module's about page.
