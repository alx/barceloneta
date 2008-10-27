<?php
/*
This is a language file for the WP Shopping Cart plugin version 3.6.8
*/

//http://www.instinct.co.nz/wordpress_2.6/wp-admin/

define('TXT_WPSC_PAYMENT_GATEWAYS', 'Payment Gateways');

define('TXT_WPSC_PRODUCTCOUNT_SINGULAR','product');
define('TXT_WPSC_PRODUCTCOUNT_PLURAL','products');
define('TXT_WPSC_GROUPCOUNT_SINGULAR','group');
define('TXT_WPSC_GROUPCOUNT_PLURAL','groups');
define('TXT_WPSC_SALECOUNT_SINGULAR','sale');
define('TXT_WPSC_SALECOUNT_PLURAL','sales');	
define('TXT_WPSC_PENDINGCOUNT_SINGULAR','transaction');
define('TXT_WPSC_PENDINGCOUNT_PLURAL','transactions');

define('TXT_WPSC_SALES_DASHBOARD', 'You have <a href=\'admin.php?page=wp-shopping-cart/display-items.php\'>:productcount:</a>, contained within <a href=\'admin.php?page=wp-shopping-cart/display-category.php\'>:groupcount:</a>. This month you made :salecount: and generated a total of :monthtotal: and your total sales ever is :overaltotal:. You have :pendingcount: awaiting approval.');

define('TXT_WPSC_YOUAREUSING', 'You are using the :theme: style. This is WP e-Commerce :versionnumber:.');
define('TXT_WPSC_NO_SHORTCODE', 'This item product is still waiting to be added to your catalogue. There are no shortcodes or tags available at this point.');
define('TXT_WPSC_SKU', 'SKU');
define('TXT_WPSC_YOUR_ORDER', 'Your Order');
define('TXT_WPSC_ABOUT_THIS_PAGE', 'About This Page');
define('TXT_WPSC_ORDER_PENDING_PAYMENT_REQUIRED', 'Order Pending: Payment Required');
define('TXT_WPSC_SELECTALLCATEGORIES', 'Show All Products');

define('TXT_WPSC_EDITING_GROUP', 'You are editing the [categorisation] group');
define('TXT_WPSC_ADDING_TO_GROUP', 'You are adding a new item to the [categorisation] group');
define('TXT_WPSC_EDITING_IN_GROUP', 'You are editing an item in the [categorisation] group');


define('TXT_WPSC_PURCHASE_UNAVAILABLE', 'Purchase unavailable options');
define('TXT_WPSC_WE_RECOMMEND', 'We Recommend');
define('TXT_WPSC_SELECT_PRODUCT_GROUP', 'Select a Group');
define('TXT_WPSC_EDIT_THIS_GROUP', 'Edit This Group');

define('TXT_WPSC_USPS_USERID', 'USPS ID');
define('TXT_WPSC_PRODUCT_ID', 'Product ID');
define('TXT_WPSC_USPS_PASSWORD', 'USPS Password');
define('TXT_WPSC_OPTIONS_GENERAL_TAB', 'General');
define('TXT_WPSC_OPTIONS_PRESENTATION_TAB', 'Presentation');
define('TXT_WPSC_OPTIONS_SHIPPING_TAB', 'Shipping');
define('TXT_WPSC_OPTIONS_PAYMENT_TAB', 'Payment');
define('TXT_WPSC_OPTIONS_ADMIN_TAB', 'Admin');
define('TXT_WPSC_OPTIONS_MARKETING_TAB', 'Marketing');
define('TXT_WPSC_DOWNLOAD_ATTACHED_FILES', 'Download attached files');

define('TXT_WPSC_ADMIN_SEARCH_PRODUCTS', 'Search for a Product');
define('TXT_WPSC_CHOOSE_PAYMENT_GATEWAYS', 'Choose the payment options that you want to make available to your customers.');

define('TXT_WPSC_CONFIGURE_PAYMENT_GATEWAY', ' Select a payment option below to configure it.');


// to come: "Click on a payment option to configure it"

define('TXT_WPSC_SHOW_BREADCRUMBS', 'Show Breadcrumbs');
define('TXT_WPSC_PAYMENT_INSTRUCTIONS_DESCR', 'Enter the payment instructions that you wish to display to your customers when they make a purchase');
define('TXT_WPSC_PAYMENT_INSTRUCTIONS_BELOW_DESCR', 'For example, this is where you the Shop Owner might enter your bank account details or address so that your customer can make their manual payment.');

define('TXT_WPSC_ALLOW_UPLOADING_IMAGE', 'Users can upload an image when ordering this product.');

define('TXT_WPSC_OPTIONS_GENERAL_HEADER', 'General Settings');
define('TXT_WPSC_OPTIONS_PRESENTATION_HEADER', 'Presentation Settings');
define('TXT_WPSC_OPTIONS_SHIPPING_HEADER', 'Shipping Settings');
define('TXT_WPSC_OPTIONS_PAYMENT_HEADER', 'Payment Settings');
define('TXT_WPSC_OPTIONS_ADMIN_HEADER', 'Admin Settings');
define('TXT_WPSC_OPTIONS_MARKETING_HEADER', 'Marketing Settings');
define('TXT_WPSC_UPLOAD_IMAGE_FOR', 'Upload Image for');

define('TXT_WPSC_THUMBNAILSETTINGS', 'Thumbnail Settings');

define('TXT_WPSC_CLEAR_IP_LOCKS', 'Free downloads locked to an IP number.');
define('TXT_WPSC_THANKS_UNLOCKED', 'Thanks, the downloads that were locked to this users IP number have been freed.');


define('TXT_WPSC_SELECTGATEWAY', 'Select a payment gateway');
define('TXT_WPSC_VARIATION_LIST', 'Select a Variation');

define('TXT_WPSC_CATEGORISATION_GROUPS_DESCR', 'Groups help your visitors find your products. If you sell t-shirts, hats, and trousers you might want to setup a new group called clothing and then add the categories t-shirts, hats, and trousers to that group. Now you can associate individual products to their respective clothing group categories when you add or edit products.');
define('TXT_WPSC_CATEGORISATION_DESCR', 'Product Grouping Widget');
define('WPSC_DOWNLOAD_INVALID', 'This download is no longer valid.');
define('TXT_WPSC_LOCK_DOWNLOADS_TO_IP', 'Lock downloads to IP address');
define('TXT_WPSC_CLEAR_IP_LOCKS', 'Unlock any downloads locked to an IP address for this order');
define('TXT_WPSC_THANKS_UNLOCKED', 'Thanks, the downloads that were locked to this users IP number have been freed.');
define('TXT_WPSC_USER_UNLOCKED_EMAIL', 'The administrator has unlocked your file');
define('TXT_WPSC_USER_UNLOCKED_EMAIL_MESSAGE', 'Dear Customer

We are pleased to advise you that your order has been updated and your downloads are now active.
Please download your purchase using the links provided below.

[download_links]
Thank you for your custom.');


define('TXT_WPSC_ADMIN_EMPTY_CATEGORY', 'This group is set as your default product group, you should either add some items to it or switch your default product group to one that does contain items.');

define('TXT_WPSC_USING_EXAMPLE_CATEGORY', 'You are using the example product group as your default group and it has no products in it, you should set the default group to something else, you can do so from your Shop Settings page.');

define('TXT_WPSC_NO_DEFAULT_PRODUCTS', 'Your "products page" is not currently set to display any products. You need to select a product grouping to display by default. <br /> This is set in the Shop Settings page.');

define('TXT_WPSC_FIX_CATEGORY_PERMALINKS', 'Fix Product Group Permalinks');
define('TXT_WPSC_ADVANCED_OPTIONS', 'Advanced Options');
define('TXT_WPSC_INVALID_COUPON', 'Invalid Coupon');
define('TXT_WPSC_RSSNOTE', '<strong>Note:</strong> Not only can people use this RSS to keep update with your product list but you can also use this link to promote your products in your facebook profile. <br>Just add the <a href="http://apps.facebook.com/getshopped">getshopped! facebook application</a> to your facebook profile and follow the instructions.');
define('TXT_WPSC_GOOGLEFINANCESTATUS', 'Financial Status');
define('TXT_WPSC_GOOGLEFULFILLMENTSTATUS', 'Fulfillment Status');
define('TXT_WPSC_SURETODELETECATEGORISATION', 'Are you sure you want to delete this product group? All categories it contains will be deleted too.');

define('TXT_WPSC_CATEGORISATION', 'Groups');
define('TXT_WPSC_CATEGORISATIONHASBEENADDED', 'The group has been added.');
define('TXT_WPSC_CATEGORISATIONHASBEENEDITED', 'The group has been edited.');
define('TXT_WPSC_ADD_CATEGORISATION', 'Add New Group');
define('TXT_WPSC_EDIT_CATEGORISATION', 'Edit Group');
define('TXT_WPSC_ADDDETAILS', 'Add Details');
define('TXT_WPSC_EDITDETAILS', 'Edit Details');
define('TXT_WPSC_ADDNEWCATEGORY', 'Add New Item &raquo;');
define('TXT_WPSC_VALUE', 'Value');
define('TXT_WPSC_ADD_CUSTOM_FIELD', 'Add Custom Field');
define('TXT_WPSC_EDIT_CUSTOM_FIELDS', 'Edit Custom Fields');
define('TXT_WPSC_SAVE', 'Save');



define('TXT_WPSC_DISPLAY_PRODUCT_CATEGORIES', 'Display [categorisation]'); // The shortcode [categorisation] is replaced with the name of the currently select categorisation

define('TXT_WPSC_PRODUCT_CATEGORIES', 'Select [categorisation]'); // The shortcode [categorisation] is replaced with the name of the currently select categorisation

define('TXT_WPSC_MANAGE_CATEGORISATION', 'Manage [categorisation]'); // The shortcode [categorisation] is replaced with the name of the currently select categorisation



define('TXT_WPSC_ANYONEREGISTER', 'If yes then you must also turn on the wordpress option "Any one can register"');
define('TXT_WPSC_CVN', 'Credit Verification');
define('TXT_WPSC_AVS', 'Address Verification');
define('TXT_WPSC_DISPLAYVARIATIONSDESCRIPTION', 'A variation can be anything "optional" about a product. ie: Size, Color, etc <br />For example: if you are selling t-shirts you might setup a variation set called size with the values small, medium, large...');

define('TXT_WPSC_PERTICKED', 'Apply On Every Product');
define('TXT_WPSC_EMAILALLOWED', 'Email Allowed');
define('TXT_WPSC_EMAILNOTALLOWED', 'Email NOT Allowed');

 
define('TXT_WPSC_GOOGLERISK', 'Eligible for Protection');
define('TXT_WPSC_PAYPALNOTE', '<strong>Note:</strong> Due to a current limitation of PayPal, if your user makes a purchase and uses a coupon, we can not send a list of items through to paypal for processing.<br>Rather, we must send the total amount of the purchase, so that within PayPal the user who purchases a product will see your shop name and the total amount of their purchase.');

define('TXT_WPSC_GOOGLEMARKETINGPREFERENCE', 'Google User Marketing Preference');


define('TXT_WPSC_LATEST_PRODUCTS', 'Latest Products');
define('TXT_WPSC_PICKUP', 'Pick up');
define('TXT_WPSC_DELIVERY', 'Delivery');
define('TXT_WPSC_MENU', 'e-Commerce Admin Menu');
define('TXT_WPSC_GOOGLESTORELOCATOR', 'Enable Google Store Locator');
define('TXT_WPSC_GOOGLESHIPPING', 'Google Shipping Country');
define('TXT_WPSC_POST_DEFAULT_MEMBERS_ONLY', 'This post is for members only, you must log in to view this post');
define('TXT_WPSC_BASESHIPPING', 'Base Shipping');
define('TXT_WPSC_RSS_ADDRESS', 'RSS Address');
define('TXT_WPSC_STORENAME', 'Store Name');
define('TXT_WPSC_CHECKBOX_VARIATIONS', 'Display Variations in Checkboxes');
define('TXT_WPSC_DISPLAY_PLUSTAX', 'Display "+tax"');
define('TXT_WPSC_DISPLAYHOWCUSTOMERFINDUS', 'Display How Customer Found Us Survey');
define('TXT_WPSC_HOWCUSTOMERFINDUS', 'How The Customer Found Us');
define('TXT_WPSC_ENGRAVE', 'Engrave text');
define('TXT_WPSC_FREETRIAL', 'One Month Free Trial');
define('TXT_WPSC_PROHIBITED', 'Prohibited');
define('TXT_WPSC_ADMINNOTES', 'Merchant Notes');
define('TXT_WPSC_HIDEADDNAMELINK', 'Hide Product Name Links');
define('TXT_WPSC_ADMINMENU', 'Admin Menu');
define('TXT_WPSC_USEONLYEXTERNALLINK', 'Note: Use only if you have external links');
define('TXT_WPSC_EXTERNALLINK', 'External Link');
define('TXT_WPSC_BUTTONTYPE', 'Button Type');
define('TXT_WPSC_BUYNOW', 'Buy Now');
define('TXT_WPSC_ASSOCIATEWITHFILE', 'Associate With File?(Tick one please)');
define('TXT_WPSC_SHOWLIVESEARCH', 'Use Live Search');
define('TXT_WPSC_USPS_USERID', 'USPS ID');
define('TXT_WPSC_USPS_PASSWORD', 'USPS Password');

define('TXT_WPSC_SHIPPING_DETAILS', 'Shipping Details');
define('TXT_WPSC_SHOWADVANCEDSEARCH', 'Show Advanced Search');
define('TXT_WPSC_GOOGLEMECHANTKEY', 'Google Merchant Key');
define('TXT_WPSC_GOOGLEMECHANTID', "Google Merchant ID");
define('TXT_WPSC_IMAGESIZEINFO', 'Note: if this is blank, the image will not be resized');
define('TXT_WPSC_ENDLESSSUBSCRIPTION', 'Permanent subscription?');
define('TXT_WPSC_RECEIVED', 'Order Received');
define('TXT_WPSC_ORDER_STATUS', 'Order Status');
define('TXT_WPSC_ORDER_SUMMARY', 'Order Summary');
define('TXT_WPSC_LANGUAGE', 'Language');
define('TXT_WPSC_ACCEPTED_PAYMENT', 'Accepted Payment');
define('TXT_WPSC_JOB_DISPATCHED', 'Job Dispatched');
define('TXT_WPSC_PROCESSED', 'Closed Order');
define('TXT_WPSC_ECOMMERCE', 'e-Commerce');
define('TXT_WPSC_OPTIONS', 'Settings');
define('TXT_WPSC_ADDPRODUCTS', 'Add Products');
define('TXT_WPSC_SENDSMS', 'Send SMS');
define('TXT_WPSC_PRODUCTS', 'Products');
define('TXT_WPSC_ADDCATEGORY', 'Add Product Group');
define('TXT_WPSC_CATEGORIES', 'Categories');
define('TXT_WPSC_BRANDS', 'Brands');
define('TXT_WPSC_VARIATIONS', 'Variations');
define('TXT_WPSC_PURCHASELOG', 'Sales');
define('TXT_WPSC_ORDER_LOG', 'Orders');
define('TXT_WPSC_OLDER_ORDERS', 'Older Orders');
define('TXT_WPSC_ORDER', 'Order');
define('TXT_WPSC_TOTAL_THIS_MONTH', 'Total Money Earnt this Month');
define('TXT_WPSC_MONTHLY_REPORT', 'Six Monthly Report');
define('TXT_WPSC_TOTAL_INCOME', 'Total Income');
define('TXT_WPSC_ACCEPTED_PAYMENTS', '(accepted payments)'); //text has changed slightly
define('TXT_WPSC_PAYMENTGATEWAYOPTIONS', 'Payment Options');
define('TXT_WPSC_HELPINSTALLATION', 'Help/Upgrade');
define('TXT_WPSC_EXAMPLECATEGORY', 'Example category');
define('TXT_WPSC_EXAMPLEDETAILS', 'Example details');
define('TXT_WPSC_EXAMPLEBRAND', 'Example Brand');
define('TXT_WPSC_PRODUCTIMAGEWIDTH', 'product image width');
define('TXT_WPSC_PRODUCTIMAGEHEIGHT', 'product image height');
define('TXT_WPSC_CATEGORYIMAGEWIDTH', 'product group image width');
define('TXT_WPSC_CATEGORYIMAGEHEIGHT', 'product group image height');
define('TXT_WPSC_PRODUCTLISTURL', 'The location of the product list');
define('TXT_WPSC_SHOPPINGCARTURL', 'The location of the shopping cart');
define('TXT_WPSC_CHECKOUTURL', 'The location of the checkout page');
define('TXT_WPSC_TRANSACTURL', 'The location of the transaction detail page');
define('TXT_WPSC_PAYMENTGATEWAY', 'The payment gateway to use');
define('TXT_WPSC_CARTLOCATION', 'Cart Location');
define('TXT_WPSC_SHOWCATEGORYBRANDS', 'Display categories or brands or both');
define('TXT_WPSC_CURRENCYTYPE', 'Currency type');
define('TXT_WPSC_CURRENCYSIGNLOCATION', 'Currency sign location');
define('TXT_WPSC_GSTRATE', 'the GST rate');
define('TXT_WPSC_MAXDOWNLOADS', 'the download limit');
define('TXT_WPSC_DISPLAYPNP', 'Display or hide postage and packaging');
define('TXT_WPSC_DISPLAYSPECIALS', 'Display or hide specials on the sidebar');
define('TXT_WPSC_POSTAGEAND_PACKAGING', 'Default postage and packaging');
define('TXT_WPSC_PURCHLOGEMAIL', 'Email address that purchase log is sent to');
define('TXT_WPSC_RETURNEMAIL', 'Email address that purchase reports are sent from');
define('TXT_WPSC_TERMSANDCONDITIONS', 'Checkout terms and conditions');
define('TXT_WPSC_DEFAULTBRAND', 'Default Brand');


//define('TXT_WPSC_DEFAULTCATEGORY', 'Default Category');

define('TXT_WPSC_DEFAULTCATEGORY', 'Select what product group you want to display on the products page by default.');

define('TXT_WPSC_PAYPALBUSINESS', 'paypal business');
define('TXT_WPSC_PAYPALURL', 'paypal url');
define('TXT_WPSC_SHOWPRODUCTRATINGS', 'Show Product Ratings');
define('TXT_WPSC_PRODUCTSPAGE', 'Products Page');
define('TXT_WPSC_CHECKOUT', 'Verify your Order');
define('TXT_WPSC_ENTERDETAILS', 'Enter Your Details');
define('TXT_WPSC_TRANSACTIONRESULTS', 'Transaction Results');
define('TXT_WPSC_SELECTACATEGORY', 'Select a Product Group');
define('TXT_WPSC_SELECTABRAND', 'Select a Brand');
define('TXT_WPSC_PRODUCTNAME', 'Product Name');
define('TXT_WPSC_PRODUCTDESCRIPTION', 'Product Description');
define('TXT_WPSC_ADDITIONALDESCRIPTION', 'Additional Description');
define('TXT_WPSC_PRICE', 'Price');
define('TXT_WPSC_TAXALREADYINCLUDED', 'Do not include tax (tax is set in shop config)'); // this text has changed and needs updating
define('TXT_WPSC_SPECIAL', 'Special / Sale Price'); // this text has changed and needs updating
define('TXT_WPSC_PRODUCT_DISPLAY', 'Product Display');

define('TXT_WPSC_PRODUCTSTOCK', 'Product Stock');

define('TXT_WPSC_UNTICKBOX', 'I have a limited number of this item in stock. If the stock runs out, this product will not be available on the shop unless you untick this box or add more stock.');
define('TXT_WPSC_LIMITED_STOCK', 'Limited Stock');

define('TXT_WPSC_CATEGORY', 'Category');
define('TXT_WPSC_BRAND', 'Brand');
define('TXT_WPSC_PRODUCT_VARS', 'Product Variations');
define('TXT_WPSC_ADD_VAR', 'Add Variation Set');
define('TXT_WPSC_EDIT_VAR', 'Edit Variation Set');
define('TXT_WPSC_SELECTAVARIATION', 'Select a Variation');
define('TXT_WPSC_NEW_VARIATION', 'Create a new Variation');

define('TXT_WPSC_PRODUCTATT', 'Product Attachments');
define('TXT_WPSC_DOWNLOADABLEPRODUCT', 'Upload File');
define('TXT_WPSC_FILETOBEPRODUCT', 'Note: if this is filled in, the file uploaded will be the product to be purchased.');
define('TXT_WPSC_RESIZEIMAGE', 'Resize Thumbnail');
define('TXT_WPSC_DONOTRESIZEIMAGE', 'do not resize thumbnail.');
// define('TXT_WPSC_USEDEFAULTHEIGHTANDWIDTH', 'use default height and width');
// define('TXT_WPSC_USE', 'use');
// define('TXT_WPSC_PXHEIGHTBY', 'px height by');
// define('TXT_WPSC_PXWIDTH', 'px width.');

// pe.{
//define('TXT_WPSC_USEDEFAULTHEIGHTANDWIDTH', 'use default height and width');
// }.pe
define('TXT_WPSC_SEPARATETHUMBNAIL', 'use separate thumbnail');  // Needs Translation
define('TXT_WPSC_USE', 'use'); // Needs Translation
// pe.{
define('TXT_WPSC_USEDEFAULTSIZE', 'use default size'); // Needs Translation
define('TXT_WPSC_USESPECIFICSIZE', 'use specific size'); // Needs Translation
define('TXT_WPSC_PXHEIGHT', 'px height'); // Needs Translation
define('TXT_WPSC_PXWIDTH', 'px width'); // Needs Translation
// }.pe


define('TXT_WPSC_UPLOADNEWIMAGE', 'Replace Image');
define('TXT_WPSC_DELETEIMAGE', 'Delete Image');
define('TXT_WPSC_EDIT', 'Edit');
define('TXT_WPSC_DELETE', 'Delete');
define('TXT_WPSC_REMOVE', 'Remove');
define('TXT_WPSC_NAME', 'Name');
define('TXT_WPSC_TYPE', 'Type');
define('TXT_WPSC_MANDATORY', 'Mandatory');
define('TXT_WPSC_DISPLAY_IN_LOG', 'Display<br /> in Log');
define('TXT_WPSC_DESCRIPTION', 'Description');
define('TXT_WPSC_CATEGORY_PARENT', 'Category Parent');
define('TXT_WPSC_IMAGE', 'Image');
define('TXT_WPSC_HEIGHT', 'Height');
define('TXT_WPSC_WIDTH', 'Width');
define('TXT_WPSC_PLEASEENTERAVALID', 'Please enter a valid');
define('TXT_WPSC_PLEASEENTERAVALIDNAME', 'Please enter a valid name');
define('TXT_WPSC_PLEASEENTERAVALIDSURNAME', 'Please enter a valid surname');
define('TXT_WPSC_PLEASEENTERAVALIDEMAILADDRESS', 'Please enter a valid email address');
define('TXT_WPSC_PLEASEENTERAVALIDADDRESS', 'Please enter a valid address');
define('TXT_WPSC_PLEASEENTERAVALIDPHONENUMBER', 'Please enter a valid phone number');
define('TXT_WPSC_TERMSANDCONDITIONS', 'You have not agreed to the terms and conditions');
define('TXT_WPSC_NOTHINGINYOURSHOPPINGCART', 'There is nothing in your shopping cart');

define('TXT_WPSC_PROCESSING_PROBLEM', 'There was a problem with processing the puchase, please email the site owner.');


define('TXT_WPSC_SPECIALS', 'Specials');
define('TXT_WPSC_BUY', 'Buy');
define('TXT_WPSC_SHOPPINGCART', 'Shopping Cart');
define('TXT_WPSC_NUMBEROFITEMS', 'Number of items');
define('TXT_WPSC_NOMOREAVAILABLE', 'This Product is out of stock.');
define('TXT_WPSC_EMPTYYOURCART', 'Empty your cart');
define('TXT_WPSC_GOTOCHECKOUT', 'Go to Checkout');
define('TXT_WPSC_CONTINUESHOPPING', 'Continue shopping');
define('TXT_WPSC_YOURSHOPPINGCARTISEMPTY', 'Your shopping cart is empty');
define('TXT_WPSC_VISITTHESHOP', 'Visit the shop');
define('TXT_WPSC_PAGES', 'Pages');
define('TXT_WPSC_OUTOF', 'Out of');
define('TXT_WPSC_VOTES', 'votes.');
define('TXT_WPSC_CLICKSTARSTORATE', 'Click stars to rate');
define('TXT_WPSC_AVERAGERATINGOF', 'Average rating of');
define('TXT_WPSC_YOURVOTE', 'Your vote');
define('TXT_WPSC_AVERAGEVOTE', 'Average vote');
define('TXT_WPSC_YOUHAVEVOTED', 'You have voted');
define('TXT_WPSC_NOVOTES', 'No Votes');
define('TXT_WPSC_1VOTE', '1 Vote');
define('TXT_WPSC_VOTES2', 'Votes');
define('TXT_WPSC_PERSONGIVEN', 'person has given this image');
define('TXT_WPSC_PERSONGIVEN2', 'stars.');
define('TXT_WPSC_PEOPLEGIVEN', 'people have given this image');
define('TXT_WPSC_PEOPLEGIVEN2', 'stars.');
define('TXT_WPSC_ITEMHASBEENADDED', 'The item has been added');
define('TXT_WPSC_ITEMHASNOTBEENADDED', 'The item has not been added');
define('TXT_WPSC_ADDNEWCATEGORY', 'Add New Product Group');
define('TXT_WPSC_SUBMIT', 'Submit');
define('TXT_WPSC_SELECTAVALIDCATEGORY', 'Please select a valid Product Group');
define('TXT_WPSC_PRODUCTNAME', 'Product Name');
define('TXT_WPSC_PRODUCTDESCRIPTION', 'Product Description');
define('TXT_WPSC_ADDNEWPRODUCT', 'Add Product');
define('TXT_WPSC_PRODUCTQUANTITY', 'Productquantity');
define('TXT_WPSC_PRODUCTIMAGE', 'Product Image');
define('TXT_WPSC_PRODUCTPRICE', 'Product Price');
define('TXT_WPSC_NOTAX', 'No tax');
define('TXT_WPSC_ABOUT', 'About');
define('TXT_WPSC_ABOUTCONTENT', 'Welcome to the e-commerce panel. <br /><br /><strong>Note:</strong> on some setups, the shopping cart may empty on every page load, if this happens, you will have to add this line:<br /><br />session_start();<br /><br />to the index.php file in the base wordpress directory.<br />');
define('TXT_WPSC_CONTACTDETAILS', 'Please enter your contact details:');
define('TXT_WPSC_CREDITCARDHANDY', 'Note, Once you press submit, you will need to have your Credit card handy.');
define('TXT_WPSC_ASTERISK', 'Fields marked with an asterisk must be filled in.');
define('TXT_WPSC_FIRSTNAME', 'First Name');
define('TXT_WPSC_LASTNAME', 'Last Name');
define('TXT_WPSC_EMAIL', 'Email');
define('TXT_WPSC_ADDRESS', 'Address');
define('TXT_WPSC_ADDRESS1', 'Address 1');
define('TXT_WPSC_ADDRESS2', 'Address 2');
define('TXT_WPSC_CITY', 'City');
define('TXT_WPSC_STATE', 'State');
define('TXT_WPSC_COUNTRY', 'Country');
define('TXT_WPSC_PHONE', 'Phone');
define('TXT_WPSC_POSTAL_CODE', 'Postal Code');
define('TXT_WPSC_TERMS1', 'I agree to The ');
define('TXT_WPSC_TERMS2', 'Terms and Conditions');


define('TXT_WPSC_TEXT', 'Text');
define('TXT_WPSC_TEXTAREA', 'Textarea');
define('TXT_WPSC_HEADING', 'Heading');

define('TXT_WPSC_MAKEPURCHASE', 'Make Purchase');
define('TXT_WPSC_BUYPRODUCTS', 'Please buy some products before using this page');
define('TXT_WPSC_BRANDHASBEENEDITED', 'The brand has been edited.');
define('TXT_WPSC_SURETODELETEPRODUCT', 'Are you sure you want to delete this product?');
define('TXT_WPSC_ADDBRAND', 'Add Brand');
define('TXT_WPSC_DISPLAYBRANDS', 'Display Brands');
define('TXT_WPSC_EDITBRAND', 'Edit Brand');
define('TXT_WPSC_ADD', 'Add');
define('TXT_WPSC_ADD_NEW_FORM', 'Add New Form Field');
define('TXT_WPSC_SAVE_CHANGES', 'Save Changes');

define('TXT_WPSC_CATEGORYHASBEENEDITED', 'The product group has been edited.');
define('TXT_WPSC_DISPLAYCATEGORIES', 'Display Product Groups');
define('TXT_WPSC_ADDCATEGORY', 'Add Product Group');
define('TXT_WPSC_EDITCATEGORY', 'Edit Product Group');
define('TXT_WPSC_ALLCATEGORIES', 'All Product Groups');

define('TXT_WPSC_DISPLAYVARIATIONS', 'Display Variations');
define('TXT_WPSC_ADDVARIATION', 'Add Variation Set');
define('TXT_WPSC_EDITVARIATION', 'Edit Variation Set');
define('TXT_WPSC_VARIATIONHASBEENEDITED', 'The variation has been edited.');
define('TXT_WPSC_VARIATION_VALUES', 'Variation Values');
define('TXT_WPSC_REMOVE_SET', 'Remove This Set'); //this needs to be translated

define('TXT_WPSC_SELECT_PARENT', 'Select Parent');
define('TXT_WPSC_PRODUCTHASBEENEDITED', 'The product has been edited.');
define('TXT_WPSC_ADDPRODUCT', 'Add Product');
define('TXT_WPSC_DISPLAYPRODUCTS', 'Display Products');
define('TXT_WPSC_PLEASESELECTACATEGORY', 'Please Select a Product Group');
define('TXT_WPSC_STOCK', 'Stock');
define('TXT_WPSC_PNP', 'PnP');
define('TXT_WPSC_EDITITEM', 'Edit Item');
define('TXT_WPSC_PRODUCTDETAILS', 'Product Details');
define('TXT_WPSC_SELECT_PRODUCT', 'Select an Existing Product');
define('TXT_WPSC_ENTERPRODUCTDETAILSHERE', '(enter in your product details here)');
define('TXT_WPSC_ADDITIONALPRODUCTDESCRIPTION', 'Additional Product Description');
define('TXT_WPSC_ADDITEM', 'Add Item');
define('TXT_WPSC_CHOOSEACATEGORY', 'Choose a Product Group');
define('TXT_WPSC_CHOOSEABRAND', 'Choose a Brand');
define('TXT_WPSC_USETHEACTUALIMAGE', 'use the actual image. No resize.');
define('TXT_WPSC_DISPLAYPURCHASES', 'Purchase Log');
define('TXT_WPSC_ID', 'ID');
define('TXT_WPSC_TRANSACTIONSTATUS', 'Transaction Status');
define('TXT_WPSC_DATE', 'Date');
define('TXT_WPSC_VIEWDETAILS', 'Details'); //Text has changed - translation is needed 
define('TXT_WPSC_STATUS', 'Status');
define('TXT_WPSC_SUCCESSFUL', 'Successful');
define('TXT_WPSC_FAILED', 'Failed');
define('TXT_WPSC_GST', 'GST');
define('TXT_WPSC_PP', 'P&amp;P');
define('TXT_WPSC_TOTAL', 'Total');
define('TXT_WPSC_FINALTOTAL', 'Final Total');
define('TXT_WPSC_CUSTOMERDETAILS', 'Customer Details');
define('TXT_WPSC_USERSCARTWASEMPTY', 'This users cart was empty');
define('TXT_WPSC_GOBACK', 'Go Back');
define('TXT_WPSC_THANKSAPPLIED', 'Thanks, your changes have been applied.');
define('TXT_WPSC_FORM_FIELDS', 'Checkout Options');
define('TXT_WPSC_PAYMENTGATEWAY2', 'Payment Gateway');
define('TXT_WPSC_PLEASESELECTAPAYMENTGATEWAY', 'Please Select A Payment Gateway');
define('TXT_WPSC_PAYMENTGATEWAYNOTE', '<strong>Note:</strong> This lite version of the e-Commerce plugin only allows you to interface with PayPal.<br /><br />If you are looking for a more profesional "non paypal solution" then we have a <a href="http://www.instinct.co.nz/blogshop">gold version of e-Commerce</a> available that interfaces seamlessly with authorize.net,  DPS (www.dps.co.nz), and paystation (www.paystation.co.nz).');
define('TXT_WPSC_URLSETTINGS', 'URL Settings');
define('TXT_WPSC_PRODUCTLISTURL', 'Product List URL');
define('TXT_WPSC_SHOPPINGCARTURL', 'Shopping Cart URL');
define('TXT_WPSC_CHECKOUTURL', 'Checkout URL');
define('TXT_WPSC_TRANSACTIONDETAILSURL', 'Transaction Details URL');
define('TXT_WPSC_PRESENTATIONSETTINGS', 'Presentation Settings');
define('TXT_WPSC_CARTLOCATION', 'Cart Location');
define('TXT_WPSC_SIDEBAR', 'Sidebar');
define('TXT_WPSC_PAGE', 'Page');
define('TXT_WPSC_WIDGET', 'Widget');
define('TXT_WPSC_NEEDTOENABLEWIDGET', 'You need to enable the widgets plugin to use this');
define('TXT_WPSC_MANUAL', 'Manual');
define('TXT_WPSC_SHOWCATEGORIESBRANDS', 'Show Categories/Brands');
define('TXT_WPSC_BOTH', 'Both');
define('TXT_WPSC_PRODUCTTHUMBNAILSIZE', 'Default Product Thumbnail Size');
define('TXT_WPSC_CATEGORYTHUMBNAILSIZE', 'Default Product Group Thumbnail Size');
define('TXT_WPSC_SHOWPOSTAGEANDPACKAGING', 'Show Postage and Packaging');
define('TXT_WPSC_YES', 'Yes');
define('TXT_WPSC_NO', 'No');
define('TXT_WPSC_SHOWSPECIALS', 'Show Specials in the Sidebar');
define('TXT_WPSC_SHOWPRODUCTRATINGS', 'Show Product Ratings');
define('TXT_WPSC_CURRENCYSETTINGS', 'Currency Settings');
define('TXT_WPSC_GSTTAXRATE', 'GST/Tax Rate');
define('TXT_WPSC_CURRENCYTYPE', 'Currency Type');
define('TXT_WPSC_CURRENCYSIGNLOCATION', 'Currency Sign Location');
define('TXT_WPSC_DEFAULTPOSTAGEPACKAGING', 'Default Postage &amp; Packaging');
define('TXT_WPSC_ADMINISTRATIONSETTINGS', 'Administration Settings');
define('TXT_WPSC_MAXDOWNLOADSPERFILE', 'Max downloads per file');
define('TXT_WPSC_PURCHASELOGEMAIL', 'Purchase Log Email');
define('TXT_WPSC_REPLYEMAIL', 'Reply Email');
define('TXT_WPSC_BRANDNOCAP', 'brand');
define('TXT_WPSC_CATEGORYNOCAP', 'category');
define('TXT_WPSC_UDPATING', 'Updating');
define('TXT_WPSC_UPDATING', 'Updating');
define('TXT_WPSC_MOREDETAILS', 'More Details');
define('TXT_WPSC_ADDTOCART', 'Add To Cart');
define('TXT_WPSC_AVGCUSTREVIEW', 'Avg. Customer Rating');
define('TXT_WPSC_YOURRATING', 'Your Rating');
define('TXT_WPSC_RATING_SAVED', 'Saved');
define('TXT_WPSC_RATETHISITEM', 'Rate This item');
define('TXT_WPSC_PRODUCTSOLDOUT', 'This product has sold out.');
define('TXT_WPSC_NOITEMSINTHIS', 'There are no items in this');
define('TXT_WPSC_CATEGORYORBRAND', 'Category or Brand');
define('TXT_WPSC_PLEASECHOOSEA', 'Please choose a');
define('TXT_WPSC_PLEASECHOOSEAGROUP', 'Please choose a Product Group');
define('TXT_WPSC_PRODUCT', 'Product');
define('TXT_WPSC_QUANTITY', 'Quantity');
define('TXT_WPSC_QUANTITY_SHORT', 'Qty'); //translation needed
define('TXT_WPSC_APPLY', 'Apply');
define('TXT_WPSC_MAKEPAYMENT', 'Make Payment');
define('TXT_WPSC_EMPTYSHOPPINGCART', 'Empty shopping cart');
define('TXT_WPSC_TOTALPRICE', 'Total Price');
define('TXT_WPSC_NOITEMSINTHESHOPPINGCART', 'There are no items in the shopping cart');
define('TXT_WPSC_EMAILMSG1', "Thank you, the order has been accepted, any items to be shipped will be processed as soon as possible, any items that can be downloaded can be downloaded using the links on this page.\n\rAll prices include tax and postage and packaging where applicable.\n\rYou ordered these items:\n\r");
define('TXT_WPSC_EMAILMSG2', ".\n\rThese items were ordered:\n\r\n\r");
define('TXT_WPSC_CLICKTODOWNLOAD', 'Click here to download');
define('TXT_WPSC_DOWNLOAD', 'Download');
define('TXT_WPSC_YOURTRANSACTIONID', 'Your Transaction ID');
define('TXT_WPSC_TRANSACTIONID', 'Transaction ID');
define('TXT_WPSC_PURCHASERECEIPT', 'Purchase Receipt');
define('TXT_WPSC_PURCHASEREPORT', 'Purchase Report');
define('TXT_WPSC_THETRANSACTIONWASSUCCESSFUL', 'The Transaction was successful');
define('TXT_WPSC_THETRANSACTIONWASNOTSUCCESSFUL', 'The Transaction was not successful');
define('TXT_WPSC_GOBACKTOCHECKOUT', 'Go back to the checkout');
define('TXT_WPSC_SPECIALPRICE', 'Special Price');

define('TXT_WPSC_INSTRUCTIONS', '
  Install Steps
  <ul class=\'installation\'>
  <li>Place contents of zip file in the wp-content/plugins directory.</li>
  <li>Activate the plugin from the wordpress plugin page</li>
  <li>Go to the "Payment Gateway Options" page in the "e-Commerce" tab, and enter in the appropriate details. (the default paypal gateway accessed is the paypal sandbox)</li>
  <li>Create some Categories and Products using the Categories and Products pages</li>
  </ul>

  <p>If you are upgrading from Previous Version we suggest you deactivate and reactivate the plugin.</p>

  <h2>Upgrades and Modules</h2>
  
<p>You will find more information about the following WP e-Commerce modules by visiting the <a href="http://www.instinct.co.nz/blogshop">Instinct BlogShop</a> website.</p>

  <ul class=\'installation\'>
   <li>Gold</li>
   <li>DropShop</li>
   <li>Audio Player</li>
   <li>GridView</li>
  </ul>

   <h2>Wishlist</h2>
  <p>If you want a new feature that is not currently supported by WP e-Commerce here then you can either <a href="http://www.instinct.co.nz/contact-us/">commission the development</a> or add it to the wish list in the <a href="http://instinct.co.nz/blogshop/support-forums/">community forums</a>.

  <h2>Customisation and Tips</h2>
  <p><strong>Tax</strong><br />If required you can enter in Tax rates on the Options page in the "e-Commerce" tab.</p>
  <p><strong>Email purchase Logs</strong><br />If you add an email address for the purchase log on the Purchase Log page this will be the email address that is sent a purchase notification each time something is bought.</p>
  <p><strong>Hiding pages</strong><br />
  To prevent the Shopping Cart, Checkout and Transaction Results pages from being displayed in the page list you must first find out their ID numbers. <br />
  <br />Then you have to edit the file in the theme you use that displays pages. This is normally found in sidebar.php, find the line similar to:<br />
  <br />"&lt;?php wp_list_pages(\'title_li=&lt;h2&gt;Pages&lt;/h2&gt;\' ); ?&gt;"<br />
  and replace with<br />
  "&lt;?php wp_list_pages(\'title_li=&lt;h2&gt;Pages&lt;h2&gt;&amp;exclude=3,4,5\'); ?&gt;"<br />
  <br />The word exclude tells it to exclude the pages from the list, replace 3,4,5 with the ID numbers of the pages you wish to exclude.</p>
  <p><strong>Image Functions</strong><br />
  If your server does not have the PHP image functions installed, you will not be able to resize images once they are uploaded</p>
  <p><strong>Permalinks</strong><br />
  We\'re doing the best we can.
  I have not been able to find a really good way to pass the required variables from page to page using permalinks that dont use mod_rewrite, if you cant use mod_rewrite, the plugin may not work with permalinks turned on.</p>
  <p><strong>Javascript and Ajax </strong><br />
  e-commerce lite uses Ajax, the shopping cart that the user sees will work without Javascript, but the Administration pages (specifically the Products and Categories pages) will not. To use the Administration section you will need to use a reasonably modern browser and have Javascript turned on. (Internet Explorer 6 or better, or Firefox 1.0 or better, older versions have not been tested for and may or may not work)</p>
<p><strong>More on Flickr</strong><br />
We have created a number of <a href="http://www.google.com/search?q=flickr+wp+e-commerce&ie=utf-8&oe=utf-8&aq=t&rls=org.mozilla:en-US:official&client=firefox-a">visual guides</a> on Flickr. 

');


define('TXT_WPSC_SHIPPINGSETTINGS', 'Shipping Settings');
define('TXT_WPSC_BASE_LOCAL', 'Base Local Shipping');
define('TXT_WPSC_BASE_INTERNATIONAL', 'Base International Shipping');
define('TXT_WPSC_BASE_COUNTRY', 'Base Country/Region');

define('TXT_WPSC_SHIPPING_DETAILS', 'Additional Shipping Costs'); // this needs updating
define('TXT_WPSC_LOCAL_PNP', 'Local Shipping Fee'); // this needs updating
define('TXT_WPSC_INTERNATIONAL_PNP', 'International Shipping Fee'); // this needs updating
define('TXT_WPSC_SHIPPING_NOTE', 'Note: charged only once per product regardless of quantity ordered.');
define('TXT_WPSC_COUNTRY_FORM_FIELD', 'Country Form Field');
define('TXT_WPSC_COUNTRY_FORM_FIELD_EXPLANATION', '(select which form field on the checkout page you want to use for specifying the country)');

define('TXT_WPSC_SHIPPING', 'Shipping');
define('TXT_WPSC_EMAIL_FORM_FIELD', 'Email Form Field');
define('TXT_WPSC_EMAIL_FORM_FIELD_EXPLANATION', '(select which form field on the checkout page you want to use for specifying the email address)');


define('TXT_WPSC_PRODUCTIMAGES', 'Product Images');
define('TXT_WPSC_PRODUCTDOWNLOAD', 'Product Download');
define('TXT_WPSC_SHOWTHUMBNAILS', 'Show Thumbnails');
define('TXT_WPSC_ADD_ADDITIONAL_IMAGE', 'Add Additional Image');
define('TXT_WPSC_DELETE_IMAGE', 'Delete Image');
define('TXT_WPSC_GOLD_OPTIONS', 'Gold Options');
define('TXT_WPSC_ACTIVATE_SETTINGS', 'Activation Settings: Gold Cart');
define('TXT_WPSC_ACTIVATION_KEY', 'Activation Key');
define('TXT_WPSC_THANKSACTIVATED', 'Thanks, the gold shopping cart has been activated.');
define('TXT_WPSC_NOTACTIVATED', 'Sorry, the API key was incorrect.');

define('TXT_WPSC_DEFAULT', 'Default View');
define('TXT_WPSC_LIST', 'List View');

define('TXT_WPSC_VISIBLE', 'Visible');
define('TXT_WPSC_DELETE_PRODUCT', 'Delete Product');
define('TXT_WPSC_ADDITIONAL_IMAGE', 'Additional Image');
define('TXT_WPSC_GATEWAY_OPTIONS', 'Gateway Options');
define('TXT_WPSC_SHIPPING_COUNTRY', 'Choose your shipping country');
define('TXT_WPSC_CONFIRM_TOTALS', 'Confirm your totals before making the payment:');
define('TXT_WPSC_CHECKOUT_FORM_FIELDS_DESCRIPTION', 'Here you can customise the forms to be displayed in your checkout page. The checkout page is where you collect important user information that will show up in your purchase logs i.e. the buyers address, and name...');
define('TXT_WPSC_PAYMENT_OPTIONS', 'Payment Options');
define('TXT_WPSC_PAYMENT_DESCRIPTION', 'Choose what payment options you want to make available in the checkout page. By default purchasers can only pay for items using a credit card however you may wish to also let them pay manually in which case you should select the credit card + manual payment option.');

define('TXT_WPSC_DEFAULT_GATEWAY_ONLY', ' only');
define('TXT_WPSC_PLUS_MANUAL_PAYMENT', ' plus manual payment option');
define('TXT_WPSC_PAYMENT_METHOD', 'Payment Method');
define('TXT_WPSC_PAY_USING', 'Pay using');
define('TXT_WPSC_PAY_MANUALLY', 'Pay by Cheque/Bank Deposit');
define('TXT_WPSC_CREDIT_CARD', 'Credit Card');
define('TXT_WPSC_ADJUSTABLE_QUANTITY', 'Show quantity form in list view');
define('TXT_WPSC_RSS_FEED_HEADER', 'Subscribe to your orders');
define('TXT_WPSC_RSS_FEED_LINK', 'Subscribe to an RSS feed');
define('TXT_WPSC_RSS_FEED_TEXT', 'of your orders');
define('TXT_WPSC_INITIAL_SETUP', 'Created default options.');
define('TXT_WPSC_PLUGIN_NEWS_HEADER', 'Plugin News');


define('TXT_WPSC_PLUGIN_NEWS', '

The <a href="http://instinct.co.nz/blogshop/products-page/" target="_blank">WP DropShop Module</a> is the latest and most cutting edge shopping cart available online. Coupled with Grid View then your site will be the talk of street! <br/><br/>

The <a href="http://instinct.co.nz/blogshop/products-page/" target="_blank">GridView Module</a> is a visual module built to enhance the way your product page looks.<br/><br/>

<a href="http://www.instinct.co.nz/wp-campaign-monitor/100">WP Campaign Monitor</a> is an email newsletter tool built just for WP users who want to send campaigns, track the results and manage their subscribers. The latest version integrates with e-commerce lite meaning that you will be able to send buyers email newsletters and much more. 

');


define('TXT_WPSC_POWERED_BY', 'This shop is powered by ');

define('TXT_WPSC_NO_PURCHASES', 'There have not been any purchases yet.');


define('TXT_WPSC_DELIVERY_ADDRESS', 'Delivery Address');
define('TXT_WPSC_DELIVERY_CITY', 'Delivery City');
define('TXT_WPSC_DELIVERY_COUNTRY', 'Delivery Country');
define('TXT_WPSC_MP3_SETTINGS', 'MP3 Settings');
define('TXT_WPSC_MP3_SETTINGS_DESCRIPTION', 'To create the 30 second MP3 file clips, this plugin needs <a href=\'http://sox.sourceforge.net/\'>SoX</a> compiled with MP3 support, enter the path to SoX here.');
define('TXT_WPSC_SOX_PATH', 'SoX Path');
define('TXT_WPSC_PREVIEW', 'Preview');
define('TXT_WPSC_DOWNLOAD_CSV', 'Download CSV');
define('TXT_WPSC_PREVIEW_FILE', 'Preview File');
define('TXT_WPSC_NEW_PREVIEW_FILE', 'New Preview File');
define('TXT_WPSC_REPLACE_PRODUCT', 'Replace Product');
define('TXT_WPSC_TOTALSHIPPING', 'Total Shipping');
define('TXT_WPSC_DISPLAY_FRONT_PAGE', 'Display on Front page');
define('TXT_WPSC_SEARCH_FOR', 'Search For');
define('TXT_WPSC_YOUR_SEARCH_FOR', 'Your search for');
define('TXT_WPSC_RETURNED_NO_RESULTS', 'returned no results.');
define('TXT_WPSC_POSTAGE', 'Postage');
define('TXT_WPSC_SUBTOTAL', 'Subtotal');


//everything under here needs translations, added after 19/1/2007

define('TXT_WPSC_PLEASEENTERAVALID', 'Please enter a valid');
define('TXT_WPSC_PLEASEENTERAVALIDNAME', 'Please enter your first name.');
define('TXT_WPSC_PLEASEENTERAVALIDSURNAME', 'Please enter your last name.');
define('TXT_WPSC_PLEASEENTERAVALIDEMAILADDRESS', 'Please enter a valid email address.');
define('TXT_WPSC_PLEASEENTERAVALIDADDRESS', 'Please complete your address.');
define('TXT_WPSC_PLEASEENTERAVALIDCITY', 'Please enter your town or city.');
define('TXT_WPSC_PLEASEENTERAVALIDPHONENUMBER', 'Please enter a valid phone number.');
define('TXT_WPSC_PLEASESELECTCOUNTRY', 'Please select your country from the list.');
define('TXT_WPSC_PLEASEAGREETERMSANDCONDITIONS', 'Please select the terms and conditions option.  Unfortunately we cannot process your order otherwise.');
define('TXT_WPSC_POSITION', 'Position');

define('TXT_WPSC_DELIVERY_REGION', 'Delivery Region');


define('TXT_WPSC_GENERAL_SETTINGS', 'General Settings');
define('TXT_WPSC_TAX_SETTINGS', 'Tax Settings');
define('TXT_WPSC_POSTAGE_AND_TAX', 'Postage &amp; Tax ');
define('TXT_WPSC_GRID', 'Grid View');
define('TXT_WPSC_CANT_MOVE_CATEGORY', 'You cannot move a product group with subcategories.');
define('TXT_WPSC_SURETODELETECATEGORY', 'Are you sure you want to delete this category? If the category has any subcategories, they will be deleted too.');
define('TXT_WPSC_VARIATION', 'Variation');
define('TXT_WPSC_OVER_TWO_VARIATIONS', 'Variation stock and price control does not work if you have more than two variations.');
define('TXT_WPSC_PRICE_AND_STOCK_CONTROL', 'Price and Stock Control') ;

// pe.{
define('TXT_WPSC_CATSBRANDSLOCATION', 'Categories &amp; Brands Location');
define('TXT_WPSC_DESCITEMSEPARATOR', ' : ');
define('TXT_WPSC_SEARCHITEMSEPARATOR', ' : ');
define('TXT_WPSC_SINGLE_SHIPPING_COUNTRY', 'Shipping country:');
define('TXT_WPSC_ADD_ANOTHER_VARIATION', 'Add a variation');
// }.pe
define('TXT_WPSC_PRODUCT_CATEGORIES', 'Select Categories');
define('TXT_WPSC_THANKS_DELETED', 'Thanks, the purchase log record has been deleted');
define('TXT_WPSC_PRODUCT_IMAGE_PREVIEW', 'Product image');
define('TXT_WPSC_PRODUCT_THUMBNAIL_PREVIEW', 'Product thumbnail');
define('TXT_WPSC_REMOVE_LOG', 'Remove this record from the purchase log');
define('TXT_WPSC_CATSANDBRAND', 'Categories &amp; Brands');
define('TXT_WPSC_GOLD_DROPSHOP', 'DropShop');
define('TXT_WPSC_NEEDTOENABLEDROPSHOP', 'You need to install the Gold and DropShop extentions to use this');
define('TXT_WPSC_DRAG_ITEM_HERE', 'Drag any item here to add it to your cart.');
define('TXT_WPSC_SHOWCATEGORYTHUMBNAILS', 'Show Product Group Thumbnails');
define('TXT_WPSC_SHOPPING_CART', 'Shopping Cart');
define('TXT_WPSC_SHOW_SLIDING_CART', 'Use Sliding Cart');
define('TXT_WPSC_PREVIEW_FILE', 'Upload Preview');
define('TXT_WPSC_PREVIEW_FILE_NOTE', 'Note: If you do not upload a preview file and your server has sox compiled with MP3 support then a preview file will be created for you.');
define('TXT_WPSC_PURCHASE_NUMBER', 'Purchase No.');
define('TXT_WPSC_FILTER_ORDER', 'Filter Orders');
define('TXT_WPSC_SHOW_SEARCH', 'Show Search');
define('TXT_WPSC_LOG_CURRENT_MONTH', 'Display results from current month');
define('TXT_WPSC_LOG_PAST_THREE_MONTHS', 'Display results from past three months');
define('TXT_WPSC_LOG_ALL', 'Display all results');
define('TXT_SHOW_IMAGES_ONLY', 'Show images only');
define('TXT_WPSC_SHOW_GALLERY', 'Show Thumbnail Gallery');
define('TXT_WPSC_PLEASE_SELECT', 'Please select');
define('TXT_WPSC_TXN_ID', 'Transaction Id');
define('TXT_WPSC_SINGLE_PRODUCTTHUMBNAILSIZE', 'Single Product Image Size');
define('TXT_WPSC_RESET', 'Reset');
define('TXT_WPSC_ORDER_PENDING', 'Thank you, your purchase is pending, you will be sent an email once the order clears.');
define('TXT_WPSC_ORDER_FAILED', 'We\'re Sorry, your order has not been accepted, the most likely reason is that you have insufficient funds.');
// Adrian - added for the options I added
define('TXT_WPSC_SHOW_CATEGORY_COUNT', 'Show Product Count per Product Group');
define('TXT_WPSC_CATSPRODS_DISPLAY_TYPE', 'Product Groups/Products Display');
define('TXT_WPSC_CATSPRODS_TYPE_CATONLY', 'Product Groups Only (All products displayed)');
define('TXT_WPSC_CATSPRODS_TYPE_SLIDEPRODS', 'Sliding Product Groups (1 product per page)');
// Adrian - END new added options
define('TXT_WPSC_ORDER_DETAILS', 'Order Details');
define('TXT_WPSC_SAVE_PROFILE', 'Save Profile');
define('TXT_WPSC_USERACCOUNTURL', 'User Account URL');
define('TXT_WPSC_MUST_BE_LOGGED_IN', 'You must be logged in to use this page. Please use the form below to login to your account.');
define('TXT_WPSC_YOUR_ACCOUNT', 'Your Account');

define('TXT_WPSC_YOU_JUST_ADDED', 'You just added "[product_name]" to your cart.');
define('TXT_WPSC_SORRY_NONE_LEFT', 'Sorry, but the item "[product_name]" is out of stock.');

define('TXT_WPSC_CONTINUE_SHOPPING', 'Continue Shopping');
define('TXT_WPSC_ITEM_GONE_OUT_OF_STOCK', 'It appears that an item has gone out of stock, please go back and edit your order.');
define('TXT_WPSC_DISPLAY_FANCY_NOTIFICATIONS', 'Display Fancy Purchase Notifications');
define('TXT_WPSC_IF_USER_CHECKOUT', 'If you have a user account, Please ');
define('TXT_WPSC_LOG_IN', 'log in.');
define('TXT_WPSC_IS_DONATION', 'Is the product a donation?');
define('TXT_WPSC_DONATION', 'Donation');
define('TXT_WPSC_DONATION_LC', 'donation');
define('TXT_WPSC_DONATION_SHIPPING', 'No shipping for donations');
define('TXT_WPSC_DOWNLOADABLEPRODUCT_URL', 'Product Filename');
define('TXT_WPSC_FILETOBEPRODUCTURL', 'Note: Upload your file to the e-commerce files directory and enter the filename here.');
define('TXT_WPSC_DONATIONS', 'Donations');
define('TXT_WPSC_WRONG_FILE_PERMS', 'The following directories are not writable: :directory: You won\'t be able to upload any images or files here. You will need to change the permissions on these directories to make them writable.');
define('TXT_WPSC_RESET_API', 'Reset API key');
define('TXT_WPSC_PRODUCTS_PER_PAGE', 'Products per Page');
define('TXT_WPSC_OPTION_PRODUCTS_PER_PAGE', 'number of products to show per page');
define('TXT_WPSC_PAGE_NUMBER_POSITION', 'Page Number position');


define('TXT_WPSC_YOUR_BILLING_CONTACT_DETAILS', '1. Your billing/contact details');
define('TXT_WPSC_DELIVER_TO_A_FRIEND', '2. Shipping details');
define('TXT_WPSC_E_COMMERCE', 'e-Commerce');
define('TXT_WPSC_THANKS_SAVED', 'Thanks, your changes have been saved.');
define('TXT_WPSC_REGISTER', 'Register');
define('TXT_WPSC_TAX', 'Tax');

define('TXT_WPSC_USE_SHIPPING', 'Use Shipping');
define('TXT_WPSC_USE_SHIPPING_DESCRIPTION', 'If you are only selling digital downloads, you should select no to disable the shipping on your site.');

define('TXT_WPSC_PAGESETTINGS', 'Pagination settings');
define('TXT_WPSC_USE_PAGINATION', 'Use Pagination');
define('TXT_WPSC_ADD_PRODUCT', 'Add Product');
define('TXT_WPSC_EDIT_PRODUCT', 'Edit Product');
define('TXT_WPSC_UPDATE_PAGE_URLS', 'Update Page URLs');
define('TXT_WPSC_VARIATIONS_AND_SPECIALS_DONT_MIX', 'Note: You cannot currently set a special price on a product with variations');
define('TXT_WPSC_PRODUCT_SPECIALS', 'Product Specials');
define('TXT_WPSC_SHOW_SHARE_THIS', 'Show Share This (Social Bookmarks)');
define('TXT_WPSC_SHOW_NO_PRODUCT', 'No Product');
define('TXT_WPSC_CHOOSE_DOWNLOADABLE_PRODUCT', 'Choose a downloadable file for this product:');
define('TXT_WPSC_PLEASECHOOSE', 'Please Choose');

define('TXT_WPSC_VARIATION_CONTROL', 'Variation Control');
define('TXT_WPSC_VARIATION_GRID_CONTROL_SINGLE', ':variation1: Control');
define('TXT_WPSC_VARIATION_GRID_CONTROL_PAIR', ':variation1: and :variation2: Control');
define('TXT_WPSC_VIEW_PREVIEW_CLIP', 'View Preview Clip');
define('TXT_WPSC_REQUIRE_REGISTRATION', 'Users must register before checking out');
define('TXT_WPSC_PLEASE_LOGIN', 'Please login or signup above to make your purchase');
define('TXT_WPSC_IF_JUST_REGISTERED', 'If you have just registered, please check your email and login before you make your purchase');
define('TXT_WPSC_SELECT_THEME', 'Select Theme');
define('TXT_WPSC_NEW_ORDER_PENDING_SUBJECT', 'New pending order');
define('TXT_WPSC_NEW_ORDER_PENDING_BODY', "There is a new order awaiting processing: \n\r ");
define('TXT_WPSC_NO_SHIPPING', "Does not use Shipping");
define('TXT_WPSC_DEFAULT_MEMBERS_ONLY', "This page is for members only, you must log in to access this page");
define('TXT_WPSC_MEMBERSHIP_CONTROL', "Membership Control");
define('TXT_WPSC_PRODUCT_MEMBER_STATUS', "Is this product a membership?");
define('TXT_WPSC_PRODUCT_MEMBERSHIP_LENGTH', "Membership Length");
define('TXT_WPSC_DAYS', "Days");
define('TXT_WPSC_MONTHS', "Months");
define('TXT_WPSC_YEARS', "Years");
define('TXT_WPSC_MEMBERS_SUBSCRIPTION_ENDED', "Your subscription has ended.");
define('TXT_WPSC_ACTIVATE_SETTINGS_MEMBERS', 'Activation Settings: Members Module');
define('TXT_WPSC_GOLD_CART', 'Gold Cart');
define('TXT_WPSC_SHOW_CATEGORY_DESCRIPTION', 'Show Product Group Description');
define('TXT_WPSC_ALSO_BOUGHT', 'People who bought this item also bought');
define('TXT_WPSC_MARKETING_SETTINGS', 'Marketing Settings');
define('TXT_WPSC_OPTION_ALSO_BOUGHT', 'Display Cross Sales');
define('TXT_WPSC_MARKETING', 'Marketing');
define('TXT_WPSC_DISPLAYCOUPONS', 'Coupons');
define('TXT_WPSC_ADD_COUPON', 'Add Coupon');
define('TXT_WPSC_COUPON', 'Coupon');
define('TXT_WPSC_ADDCOUPONS', 'Add Coupon');
define('TXT_WPSC_COUPON_CODE', 'Coupon Code');
define('TXT_WPSC_DISCOUNT', 'Discount');
define('TXT_WPSC_START', 'Start');
define('TXT_WPSC_EXPIRY', 'Expiry');
define('TXT_WPSC_USE_ONCE', 'Use Once');
define('TXT_WPSC_ACTIVE', 'Active');
define('TXT_WPSC_COUPONHASBEENADDED', 'Thanks, the coupon has been added.');
define('TXT_WPSC_COUPON', 'Coupon');
define('TXT_WPSC_COUPON_DOESNT_EXIST', 'That coupon has expired or does not exist.');
define('TXT_WPSC_PRODUCT_TAGS', 'Product Tags');

define('TXT_WPSC_DROPSHOPDISPLAY', 'DropShop Display');
define('TXT_WPSC_HIDEADDTOCARTBUTTON', 'Hide "Add to cart" button');

define('TXT_WPSC_PRODUCT_TAGS', 'Product Tags');

define('TXT_WPSC_SHOW_DROPSHOP_ALL', 'Show Dropshop on every page');
define('TXT_WPSC_SHOW_DROPSHOP_PRODUCT', 'Show Dropshop only on product page');
define('TXT_WPSC_PRICEAFTERDISCOUNT', 'Price after discount');
define('TXT_WPSC_ECOM_NEWS', 'e-Commerce News');
define('TXT_WPSC_SAVE_PRODUCT_ORDER', 'Save Product Order');

define('TXT_WPSC_ECOMMERCE_SUBSCRIBERS', 'e-Commerce Subscribers');
define('TXT_WPSC_USERID', 'User ID');
define('TXT_WPSC_REGISTEREDDATE', 'Registered Date');
define('TXT_WPSC_SUSPEND', 'Suspend');
define('TXT_WPSC_ACTIVATE', 'Activate');


define('TXT_WPSC_DROPSHOP_LIGHT', 'Use light Dropshop style');
define('TXT_WPSC_DROPSHOP_DARK', 'Use dark Dropshop style');


define('TXT_WPSC_MANUAL_PAYMENT', 'Manual Payment');
define('TXT_WPSC_CREDIT_CARD', 'Credit Card');
define('TXT_WPSC_CREDIT_CARD_AND_MANUAL_PAYMENT', 'Manual Payment and Credit Card');
define('TXT_WPSC_PAYMENT_INSTRUCTIONS', 'Enter the manual payment instructions that you wish to display');

define('TXT_WPSC_SHOWALL', "Show All");
define('TXT_WPSC_PRICE_RANGE', "Price Range");
define('TXT_WPSC_GOOGLE_RISK_AVS', "Address verification failed");
define('TXT_WPSC_GOOGLE_RISK_CVN', "Credit card verification failed");
define('TXT_WPSC_GOOGLE_RISK_BOTH', "Credit card and Address verification failed");
define('TXT_WPSC_GOOGLE_RISK', 'Google Risk');
define('TXT_WPSC_CANCEL_ORDER', 'Cancel this order');
define('TXT_WPSC_FIRST_NAME', 'First Name');
define('TXT_WPSC_LAST_NAME', 'Last Name');
define('TXT_WPSC_DELIVERY_FIRST_NAME', 'Delivery First Name');
define('TXT_WPSC_DELIVERY_LAST_NAME', 'Delivery Last Name');
define('TXT_WPSC_DELIVERY_STATE', 'Delivery State');
define('TXT_WPSC_SHIPWIREEMAIL', 'ShipWire Email');
define('TXT_WPSC_SHIPWIREPASSWORD', 'ShipWire Password');
define('TXT_WPSC_SHIPWIRESETTINGS', 'ShipWire Settings');
define('TXT_WPSC_NO_DOWNLOADABLES', 'You have not purchased any downloadable products yet.');
define('TXT_DISPLAY_VARIATIONS', 'Display Variations');


define('TXT_WPSC_NONAME', 'No Name');
define('TXT_WPSC_WEIGHT', 'Weight');
define('TXT_WPSC_LOG_TRANSACTIONACCEPTEDLOGS', 'Display only accepted transactions');
define('TXT_WPSC_SEARCHEMAIL', 'Search By Email');

?>