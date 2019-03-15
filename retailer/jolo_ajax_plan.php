<?php
$mobile = isset($_GET['mobile']) && $_GET['mobile'] != '' ? $_GET['mobile'] : '0';
$ch = curl_init();
$timeout = 30; // set to zero for no timeout
$myurl = "https://joloapi.com/api/findoperator.php?userid=ankit560&key=129838766538838&mob=".$mobile."&type=json";
curl_setopt ($ch, CURLOPT_URL, $myurl);
curl_setopt ($ch, CURLOPT_HEADER, 0);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
$jsonxx = curl_exec($ch);
print_r($jsonxx);
// $curl_error = curl_errno($ch);
// curl_close($ch);
// $someArray = json_decode($jsonxx, true);
// if (count($someArray) > 0) {
// echo "<table><thead><tr>
// <th>Detail</th>
// <th>Amount (Rs.)</th>
// <th>Validity (days)</th>
// </tr></thead><tbody>";
// foreach ($someArray as $key => $value) {
// echo " <tr><td>" .$value["Detail"] . "</td> <td>" .$value["Amount"] . "</td> <td>" .$value["Validity"] . "</td> </tr>";
// }
// echo "</tbody></table><br/>";
// }else{
// echo"No offer details available for this category";
// }
?>