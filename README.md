Cliff Johnson - 4 code samples:

	1 addCart.js - this dynamically builds a shopping cart with jQuery with the show(), other functions
		add to the cart and change the number of items in cart display.
	2 checkDates.php - this runs nightly from a cron job.  It checks the database for signup dates is in date range
		sends anyone of 3 different notices to the user.  It also processes the scheduled payment with the financial
		gateway.
	3 billingDetails.js -  This process sends a user id using AJAX and jQuery and looks for a card number to be
		returned. 
	4 getNumber.php - to populate a credit card number to the users screen the encrypted number is
		retrieved from MySQL, decrypted, and then packed up as JSON and sent to the users device.
