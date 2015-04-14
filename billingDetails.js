<script type="text/javascript">

function getNumber() {
  var myUser = document.getElementById("Username").value;	
  jQuery(document).ready(function(){

	jQuery.ajax({
		"url":"/getNumber.php",
		"type":"POST",
		"data":{
			"Username":myUser 
		},
		"dataType":"JSON"
		}).done(function(data){
			var vret = data;
      document.getElementById("cardnumber").value = vret.mycardnumb;	
		});
	});	
}
	
window.onload=getNumber;

function checkForm() {
	var field=document.getElementById('expiremonth').value;
	var regex = /^([0-9]{2})$/;     
	var result = field.match(regex);
	if (!result) {
		alert("Enter a valid expire month");
		return false;
	}
	var field=document.getElementById('expireyear').value;
	var regex = /^([0-9]{2})$/;     
	var result = field.match(regex);
	if (!result) {
		alert("Enter a valid expire year");
		return false;
	}
}
</script>