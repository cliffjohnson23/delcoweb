	//alert("create variables");
	var plans = {
		"Basic": 49,
		"Standard": 79,
		"Freedom": 99,
		"Heritage": 279,
		"Enterprise": 699,
		"EnterprisePlus": 999
	}
	var orderPage = 0;
	var items = ['hostingplan', 'domainrent'];
	var selectd = ['none', 'none'];
	var rmvOvr = 'background-color: #cccccc;';
	var rmvOut = 'background-color: #aaaaaa;';
	var numInCart = 0;

  imgChk = new Image;
	imgChk.src = '/images/checkout.gif';
	imgChkOut = imgChk.src;
	imgChk.src = '/images/checkoutOver.gif';
	imgChkOvr = imgChk.src;
	crtOvr = new Image;
	crtOvr.src = '/images/cartxOver.gif';
	crtOut = new Image;
	crtOut.src = '/images/cartx.gif';

	window.addEventListener('load', initiate);
	
function initiate() {
	//alert("initiate");
	//jQuery.noConflict();
} 
function show(){
  jQuery('#databox').html('<table id="cartable" style="width: 100%; margin: 10px; border-spacing: 0;"></table>');
  jQuery('#cartable').append('<tr style="height: 60px;"><td style="width: 75%; border-top: solid 1px white; text-align: left;">&nbsp;&nbsp;PRODUCT DESCRIPTION</td><td style="width: 25%; border-top: solid 1px white; text-align: right;">AMOUNT&nbsp;&nbsp;</td></tr>');
  var cartTot = 0;
	for(var f=0;f<localStorage.length;f++){
    var keyword=localStorage.key(f);
    var avalue=localStorage.getItem(keyword);
    if (_.contains(items, keyword)) { 
    		var removeButton = !orderPage ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button style="background-color: #aaaaaa; border-radius: 5px;" onmouseover="this.style=rmvOvr" onmouseout="this.style=rmvOut" onclick="removei(\''+keyword+'\')">remove</button>' : "";
    	 	jQuery('#cartable').append('<tr style="height: 100px; font-weight: bold;"><td style="border-top: solid 1px white; text-align: left; font-size: 1.1em;">&nbsp;&nbsp;Management Plan&nbsp; - &nbsp;'+avalue+' &nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal">(Annual Subscription)</span>'+removeButton+'</td><td style="border-top: solid 1px white; text-align: right;">$'+plans[avalue]+'&nbsp;&nbsp;</td></tr>');
    	cartTot += plans[avalue];
    }
  }
  // Display item in cart with description
	if (!cartTot) {
		jQuery('#cartable').append('<tr style="height: 80px;"><td style="border-top: solid 1px white; text-align: left;">&nbsp;</td><td style="border-top: solid 1px white; text-align: right;">&nbsp;</td></tr>');
	}
	// Display total line with amount total
	if (cartTot) {
		jQuery('#cartable').append('<tr style="height: 60px;"><td style="text-align: left; border-top: double 2px white;">&nbsp;&nbsp;TOTAL</td><td style="text-align: right; font-weight: bold; border-top: double 2px white;">$'+cartTot+'&nbsp;&nbsp;</td></tr>');
	} else {
		jQuery('#cartable').append('<tr style="height: 60px;"><td style="text-align: left; border-top: solid 1px white;">&nbsp;&nbsp;TOTAL</td><td style="text-align: right; border-top: solid 1px white;">$'+cartTot+'&nbsp;&nbsp;</td></tr>');
	}
	jQuery('#cartable').append('<tr style="height: 1px;"><td style="border-top: solid 1px white; text-align: left;">&nbsp;</td><td style="border-top: solid 1px white; text-align: right;">&nbsp;</td></tr>');
	//alert("orderPage: "+orderPage);
	if (cartTot && !orderPage==1) {
		jQuery('#cartable').append('<tr style="height: 60px;"><td style="text-align: right;">&nbsp;&nbsp;</td><td style="text-align: right; font-weight: bold;"><a href="process-order"><div style="width: 170px"><img src="/images/lock.png" width="27px" style="float: left;" ><img name="checkImg" src="/images/checkout.gif" width="125px" onmouseover="this.src=imgChkOvr" onmouseout="this.src=imgChkOut" style="float: right" /></div></a>&nbsp;&nbsp;</td></tr>');
	}

	// Get correct image icon, number of items in cart
	showIcon(countItems());
}
function checkForm() {
	var field=document.getElementById('expiremonth').value;
	var regex = /^([0-9]{2})/g;     
  var result = field.match(regex1);
	if (!result) {
		alert("Enter a valid expire month");
		return false;
  }
	var field=document.getElementById('expireyear').value;
	var regex = /^([0-9]{2})/g;     
  var result = field.match(regex1);
	if (!result) {
		alert("Enter a valid expire year");
		return false;
  }
}
function countItems() {
	var cntr = 0;
	for(var f=0;f<localStorage.length;f++){
    var keyword=localStorage.key(f);
    var avalue=localStorage.getItem(keyword);
    if (_.contains(items, keyword)) { 
    	var i = items.indexOf(keyword); 		
    	selectd[i] = avalue;					// items correspond to selectd
    	cntr += 1;
    }
  }
	return cntr;
}
function cartOver() {
	document.getElementById('cartImage').src=crtOvr.src;
	document.getElementById('checkout').style = "font-weight: bold; width: 100px; position: absolute; bottom: 0; left: 0; padding-left: 20px; display: inline; color: #f5f5f5;";
}
function cartOut() {
	document.getElementById('cartImage').src=crtOut.src;
	document.getElementById('checkout').style = "font-weight: bold; width: 100px; position: absolute; bottom: 0; left: 0; padding-left: 20px; display: inline; color: #ffffff;";	
}
function showIcon(cntr) {
	//alert("items in cart: "+cntr);
	numInCart = cntr;
	document.getElementById('cartnumber').innerHTML = numInCart;
	if (numInCart > 0) {
		document.getElementById('checkout').style = "font-weight: bold; width: 100px; position: absolute; bottom: 0; left: 0; padding-left: 20px; display: inline; color: white;";
	} else {
		document.getElementById('checkout').style = "display: none"; 
	}
}
function removei(keyword){
    localStorage.removeItem(keyword);
    show();
}
function removeAll(){
	//alert("remove all");
    localStorage.clear();
    show();
}
function addCart(item) {
  var keyword;	
  //var plans = ['Basic','Standard','Freedom','Heritage','Enterprize'];
  //(plans.indexOf(item)+1)
	//if (_.contains(plans, item)) {
	if (plans[item]) {
		keyword=items[0];
	} else {
		alert('plan not found error');
		return;
	} 

  localStorage.setItem(keyword,item);
  window.location.assign("/shopping-cart")  //test as: /location_go.html / live: /index.php/shopping-cart
}
