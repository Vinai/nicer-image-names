Nicer Image Names
========================
Build the image file names from product attributes so they have neat descriptive image names.

Facts
-----
- version: check the [config.xml](https://github.com/Vinai/nicer-image-names/blob/master/app/code/community/Netzarbeiter/NicerImageNames/etc/config.xml)
- extension key: Netzarbeiter_NicerImageNames
- [extension on Magento Connect](http://www.magentocommerce.com/magento-connect/netzarbeiter-nicerimagenames.html)
- Magento Connect 1.0 extension key: magento-community/Netzarbeiter_NicerImageNames
- Magento Connect 2.0 extension key: http://connect20.magentocommerce.com/community/Netzarbeiter_NicerImageNames
- [extension on GitHub](https://github.com/Vinai/nicer-image-names)
- [direct download link](https://github.com/Vinai/nicer-image-names/zipball/master)

Description
-----------
This small extension builds the image file names from product attributes, allowing Customers saving images then
always have neat descriptive image names, and you don't have to do the work before uploading them.

The way image names are build can be specified in the configuration under
System - Configurataion - Catalog - Nicer Image Names

It works like this example map:
```
  netzarbeiter.de-%manufacturer-%sku
```
The %manufacturer will be replaced with the products manufacturer, the %sku with the products sku.
You can use any attribute that returns a scalar value (string, integer, float, boolean).
Simply prefix the attribute code with a %
All other parts of the map will be left just like they are.
If you want to add an attribute with an underscore in the attribute_code (e.g. short_description), you have to
capitalize the character after the underscore and remove
the _ characters. For example, short_description becomes shortDescription.

In addition to product attributes you can also use %requestHost to specify the
domain name of the current request.

Here is an example image name template string:
%requestHost-%urlKey%manufacturer-%sku


Compatibility
-------------
- Magento >= 1.1

Installation Instructions
-------------------------
1. Install the extension via Magento Connect with the key shown above or copy all the files into your document root.
2. Clear the cache, logout from the admin panel and then login again.
3. Configure and activate the extension under System - Configurataion - Catalog - Nicer Image Names

Support
-------
If you have any issues with this extension, open an issue on GitHub (see URL above)

Contribution
------------
Any contributions are highly appreciated. The best way to contribute code is to open a
[pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Vinai Kopp
[http://www.netzarbeiter.com](http://www.netzarbeiter.com)
[@VinaiKopp](https://twitter.com/VinaiKopp)

Licence
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2012 Vinai Kopp