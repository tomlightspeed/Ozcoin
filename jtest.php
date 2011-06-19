<?PHP
include ("includes/header.php");
  require_once 'jsonRPCClient.php';
 /*
  $bitcoin = new jsonRPCClient('http://coinz:coinz11@127.0.0.1:8335/');
  echo "<pre>\n";
//print_r($bitcoin->getinfo()); echo "\n";
  echo "Received: ".$bitcoin->gettransaction("680bb1e805fed80f46fd5177cfad6e931d84a39fa193c6d0ff278b8d2a94d5d1")."\n";
$test = $bitcoin->gettransaction("680bb1e805fed80f46fd5177cfad6e931d84a39fa193c6d0ff278b8d2a94d5d1");
print_r($test);

$numAccounts = count($test);
for($i = 0; $i < $numAccounts; $i++){
echo "confirmations: ".$test[$i]["confirmations"]."<br>";
echo "TXID: ".$transactions[$i]["txid"]."<br>";
echo "Category: ".$test[$i]["category"]."<br><br>";

}
*/

  echo "</pre>";

$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);
$transactions = $bitcoinController->query(getinfo);

$numAccounts = count($transactions);
/*
echo "Txid: ".$transactions['txid']."<br>";
echo "confirmations : ".$transactions['confirmations']."<br>";

foreach( $transactions as $transaction ) { 
echo "confirmations: ".$transaction[0]."<br>"; 
echo "TXID: ".$transaction[1]."<br>"; 
echo "Category: ".$transaction[2]."<br><br>"; 
}

for($i = 0; $i < $numAccounts; $i++){
echo "confirmations: ".$transactions[$i][0]."<br>";
echo "TXID: ".$transactions[$i][1]."<br>";
echo "Category: ".$transactions[$i]["category"]."<br><br>";

}*/
print_r($transactions);


?>
