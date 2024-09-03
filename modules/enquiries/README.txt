Here's a very simple contact form feature that should get you out of the starting blocks quickly.

SETUP INSTRUCTIONS

* Everything should install automatically if you're using the Trongate desktop app

* The contact form can be viewed by going to your BASE_URL followed by enquries

* It's highly recommended to add a custom route so that your contact form can be found at /contact (or somewhere similar).  To achieve this, open up custom_routing.php and add a line new.  Below is an example of some code from custom_routing.php (where /contact will open the contact us form)...

<?php
$routes = [
    'contact' => 'enquiries/index',
    'tg-admin' => 'trongate_administrators/login',
    'tg-admin/submit_login' => 'trongate_administrators/submit_login'
];
define('CUSTOM_ROUTES', $routes);

* Please DO feel free to change the 'prove you are human' questions and answers.  You will find them on the controller file.

* FINALLY, please remember this is a VERY basic feature.  The chances of this thing successfully blocking spam are zero.  You are encouraged to build spam protection into this and any other features that take your fancy.  I hope you find this useful.  Cheers!  - DC