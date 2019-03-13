# This Magento 1 extension is orphaned, unsupported and no longer maintained.

If you use it, you are effectively adopting the code for your own project.

Nicer Image Names
========================
Build the image file names from product attributes so they have neat descriptive image names.

Facts
-----
- version: check the [config.xml](https://github.com/Vinai/nicer-image-names/blob/master/app/code/community/Netzarbeiter/NicerImageNames/etc/config.xml)
- extension key: Netzarbeiter_NicerImageNames
- extension on Magento Connect: -
- Magento Connect 1.0 extension key: -
- Magento Connect 2.0 extension key: -
- [extension on GitHub](https://github.com/Vinai/nicer-image-names)
- [direct download link](https://github.com/Vinai/nicer-image-names/zipball/master)

Description
-----------
This small extension builds the image file names from product attributes, allowing Customers saving images then
always have neat descriptive image names, and you don't have to do the work before uploading them.

It also can automatically generate ALT and TITLE tag content for images where its not explicitly set. 

The way image names are built can be specified in the configuration under
System - Configuration - Catalog - Nicer Image Names [Netzarbeiter Extension]

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
the _ characters. e.g: short_description becomes shortDescription.

If you have an attribute codes that match another attribute completely, for example %text and %textColor, you might end up with the module always only parsing %text only.  
In that case you can use %{textColor} to enforce the full attribute code to be used.  

If product images are displayed in your skin's product listing then you will need to set the property "Used in product listing" to "Yes" under Catalog - Attributes - Manage Attributes for any attributes used.

In addition to product attributes you can also use %requestHost to specify the
domain name of the current request.

Here is an example image name template string:
%requestHost-%urlKey%manufacturer-%sku


Compatibility
-------------
- Magento >= 1.1

Installation Instructions
-------------------------
1. Install the extension by copying all the files from the repository to the appropriate folders.
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
(c) 2014 Vinai Kopp
