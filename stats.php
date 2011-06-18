<?php
//    Copyright (C) 2011  Mike Allison <dj.mikeallison@gmail.com>
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>.

// 	  BTC Donations: 163Pv9cUDJTNUbadV4HMRQSSj3ipwLURRc

include ("includes/header.php");

$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);

?>
<table><tr><td>
<h1>Member Stats</h1><br/>
<table border="1" cellspacing=0 cellpadding=5>
<tr><td><b>Top 20 Hashrates</b></td></td><td><b>Top 20 Lifetime Shares</b></td></tr>
<tr><td><table width=100% border="1">
<tr><td>Rank</td><td>User Name</td><td>Hashrate</td></tr>
<?php

$result = mysql_query("SELECT id, hashrate FROM webUsers ORDER BY hashrate DESC LIMIT 30");
$rank = 1;

while ($resultrow = mysql_fetch_object($result)) {
	$resdss = mysql_query("SELECT username FROM webUsers WHERE id=$resultrow->id");
	$resdss = mysql_fetch_object($resdss);
	$username = "$resdss->username";
	echo "<tr><td>".$rank."</td><td>".$username."</td><td>".$resultrow->hashrate."</td></tr>";
	$rank++;
}

?>
</table></td>
<td><table border="1" width=100%>
<tr><td>User Name</td><td>Shares</td></tr>
<?php

$result = mysql_query("SELECT id, share_count, stale_share_count FROM webUsers ORDER BY share_count DESC LIMIT 30");

while ($resultrow = mysql_fetch_object($result)) {
	$resdss = mysql_query("SELECT username FROM webUsers WHERE id=$resultrow->id");
	$resdss = mysql_fetch_object($resdss);
	$username = "$resdss->username";
	echo "<tr><td>".$username."</td><td>".($resultrow->share_count - $resultrow->stale_share_count)."</td></tr>";
}

?>
</table></td></tr>
</table>
<?php

echo "</td><td>"; // start server stats
echo "<h2>Server Stats</h2><br/>";
echo "Current Block: ".$bitcoinController->query("getblocknumber")."\n<br/>";
echo "Current Difficulty: ".round($bitcoinController->query("getdifficulty"), 2)."<br/>";

$result = mysql_query("SELECT blockNumber, confirms, timestamp FROM networkBlocks WHERE confirms > 1 ORDER BY blockNumber DESC LIMIT 1");
if ($resultrow = mysql_fetch_object($result)) {
	echo "<br>Last Block Found: ".$resultrow->blockNumber."<br/>";
	echo "Confirmations: ".$resultrow->confirms."<br/>";
	echo "Time: ".strftime("%B %d %Y %r",$resultrow->timestamp)."<br/>";
	echo "<br><a href=blocks.php style=\"color: blue\">More Block Info</a><br>";
}

$res = mysql_query("SELECT count(webUsers.id) FROM webUsers WHERE hashrate > 0") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
$users = $row[0];

echo "<br>Current Users Mining: ".$users."<br/>";
echo "Current Total Miners: ".$settings->getsetting('currentworkers')."<br/>";

echo "</td></tr></table>";

include("includes/footer.php");

?>