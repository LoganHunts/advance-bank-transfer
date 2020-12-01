WordPress/ WooCommerce Assignment 3 (Level 1)
 
Requirements:
Install the WooCommerce plugin.
 
Step #1
Create a WooCommerce payment method named 'Advanced Bank Transfer'.
Same basic settings like native BACS payment method
http://imgwxl.com/a/chrome_2019-06-04_17-37-45.png
Here our purpose is to let buyer upload the bank payment receipt during checkout.
http://imgwxl.com/a/chrome_2019-06-04_18-22-09.png
    1. Upload field should be jQuery AJAX upload and not the page reload. 
    2. After uploading it should show file path with (X) button to unlink file. Unlinking file will also fire AJAX request to delete file from the server. 
    
When that payment method is selected, the buyer can't complete the order until image or pdf file uploaded.
In the order admin area, display the uploaded receipt image under Advanced fields section. Here: https://imgur.com/QppkeD6



Step #2
Two new setting needs to add. 
1. Restrict file upload to selected countries.
Desc: Leave blank to run for all countries.
Like this http://imgwxl.com/a/chrome_2019-06-04_17-57-29.png
So, if a person selected India.
Then validate the billing country with the gateway selected country. If matched then show, otherwise don't.

2. Allowed mime types for upload file.
WooCommerce native select2 js to use
Options: .jpg, .jpeg, .png, .pdf only

After a successful order, the order status should be ‘On hold’. Admin would manually approve it like current BACS method orders.

Imp. points to remember while making this addon
Crosscheck if WooCommerce plugin active.
The plugin should follow a high level of OOPS
Proper hierarchy of files i.e. folder structure needs to follow.                                  Contd.     


New gateway method shouldn’t work with the WooCommerce subscription plugin while default settings are applied .
Code should be clean, readable, well modularized & documented. 
