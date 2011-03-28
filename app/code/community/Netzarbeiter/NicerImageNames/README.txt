
 **
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * package    Netzarbeiter_NicerImageNames
 * copyright  Copyright (c) 2010 Vinai Kopp http://netzarbeiter.com/
 * license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 **


Magento Module: Netzarbeiter/NicerImageNames
Author: Vinai Kopp <vinai@netzarbeiter.com>


USAGE

This small extension builds the image file names from product attributes,
allowing Customers saving images then always have neat
descriptive image names, and you don't have to do the work before
uploading them.

The way image names are build can be specified in the configuration under
System / Configurataion / Catalog / Nicer Image Names

It works like this example map:
  netzarbeiter.de-%manufacturer-%sku
The %manufacturer will be replaced with the products manufacturer, the %sku with
the products sku.
You can use any attribute that returns a scalar value (string, integer, float, boolean)
Simply prefix the attribute code with a %
All other parts of the map will be left just like they are.
If you want to add an attribute with an underscore in the attribute_code (e.g.
short_description), you have to capitalize the character after the underscore and
remove the _ characters. e.g: short_description becomes shortDescription.


Thanks to t3pke from http://www.keurigonline.nl/ for whom I created the extension for
letting me share it!

KNOWN BUGS:
- None! :D

If you have ideas for improvements or find bugs, please send them to vinai@netzarbeiter.com,
with Netzarbeiter_NicerImageNames as part of the subject line.

