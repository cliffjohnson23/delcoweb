<?php
//$usernme = $_POST['Username'];
//echo 'Username: '.$usernme.'<br />';
$conn = new mysqli('localhost', 'database_user', 'xxxxxxxxxx', 'database_name');
if(!$conn){
	die('Database Connection Error' . $conn->connect_error);
}

$usernme = $_POST['Username'];

$key = pack('H*', "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

// Get cardnumber from database
$sql = "SELECT id,cardnumber FROM xxxxxx_hosting_info WHERE hiUsername = '$usernme' LIMIT 1";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        //echo "id-2: " . $row["id"]. " - cardnumber-2: " . $row["cardnumber"] . "<br>";
        $numb_encoded = $row["cardnumber"];
    }
} else {
    echo "NO results found" . $conn->error;
}

// decode, decrypt and show number
	$ciphertext = base64_decode($numb_encoded);
	# retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
  $iv = substr($ciphertext, 0, $iv_size);
    
  # retrieves the cipher text (everything except the $iv_size in the front)
  $numb_encrypt = substr($ciphertext, $iv_size);
  
	$numb = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $numb_encrypt, MCRYPT_MODE_CBC, $iv);
  //echo "cardnumber: $numb <br />";

$conn->close();

header('Content-type: application/json');
echo json_encode(array("mycardnumb"=>$numb));

?> 