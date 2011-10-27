*********
REST Test
*********

This contains the FLOW3 package "Wwwision.Rest" that demonstrates a simple REST API.

============
Installation
============

1. Clone this package to the ``Packages/Application`` of your FLOW3 distribution:

::

	git clone git://github.com/bwaidelich/REST-Test.git Wwwision.RestTest

2. Activate the package:

::

	./flow3 package:activate Wwwision.RestTest

3. Apply Doctrine migrations:

::

	./flow3 doctrine:migrate

4. Activate the package SubRoutes by copying the following snippet on top of your ``Configuration/Routes.yaml`` file:

::

	##
	# Wwwision.RestTest SubRoutes
	#
	-
	 name: 'Wwwision.RestTest'
	 uriPattern: '<RestTestSubroutes>'
	 subRoutes:
	   RestTestSubroutes:
		 package: Wwwision.RestTest


=====
Usage
=====

You can test the REST API by pointing your browser to:

::

	http://localhost/products

The example allows you to simple CRUD operations on "products" via XML, JSON or the Non-Ajax-Fallback.
For this example, jQuery is used to fire the GET/POST/PUT/DELETE requests, but you can of course use any REST-capable client.

Just make sure to set the right **request headers:**
If you want to retrieve the result of an operation as XML (recommended format), you should set the ``Accept`` header to ``application/xml`` (or any other xml mime type). For json set it to ``application/json`` (or any other json mime type).

Also you have to specify the ``Content-Type`` header!
Set it to ``application/xml`` if you want to push content as XML or ``application/json`` if you prefer JSON.
If the request type can't be determined the input stream is expected to be url encoded (``application/x-www-form-urlencoded``).