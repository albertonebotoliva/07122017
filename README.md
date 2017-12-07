Instructions:

- Clone this repository in a PHP/MYSQL environment. I used XAMPP for the development.

- Import nps.sql in your local db.
	
	You will get:
		comments - The initial table with comments. With Fake data
		entities - Empty table to keep the results

- Install the Client libraries

	Install Composer from https://getcomposer.org/
	Run: 
		composer require google/cloud-language

- Authorization to the API:

	Install the gcloud sdk console from https://cloud.google.com/sdk/
	Run:
		gcloud init - configure your environment
		gcloud auth application-default login - login into the platform

- Run index.php

	Will show the 25 most popular entities after processing the comments.


- Extra:

	Keep track of the sentiments