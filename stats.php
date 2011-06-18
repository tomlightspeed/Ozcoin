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

$numberResults = 30;

$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);

?>

<div id="stats_wrap">
<div id="stats_members">
<table class="stats_table member_width">
<tr><th colspan="3" scope="col">Top <?php echo $numberResults;?> Hashrates</th></tr>
<tr><th scope="col">Rank</th><th scope="col">User Name</th><th scope="col">Hashrate</th></tr>
<?php

$result = mysql_query("SELECT id, hashrate FROM webUsers ORDER BY hashrate DESC LIMIT " . $numberResults);
$rank = 1;

while ($resultrow = mysql_fetch_object($result)) {
	$resdss = mysql_query("SELECT username FROM webUsers WHERE id=$resultrow->id");
	$resdss = mysql_fetch_object($resdss);
	$username = $resdss->username;
	if( $username == $userInfo->username )
	{
		echo "<tr class=\"user_position\">";
	}
	else
	{
		echo "<tr>";
	}
	echo "<td>" . $rank;
	
	if( $rank == 1 )
	{
		echo "&nbsp;<img src=\"/images/crown.png\" />";
	}
	
	echo "</td><td>" . $username . "</td><td>" . $resultrow->hashrate . "</td></tr>";
		
	$rank++;
}

?>
</table>
</div>
<div id="stats_lifetime">
<table class="stats_table member_width">
<tr><th colspan="3" scope="col">Top <?php echo $numberResults;?> Lifetime Shares</th></tr>
<tr><th scope="col">Rank</th><th scope="col">User Name</th><th scope="col">Shares</th></tr>
<?php

$result = mysql_query("SELECT id, share_count, stale_share_count FROM webUsers ORDER BY share_count DESC LIMIT " . $numberResults);
$rank = 1;

while ($resultrow = mysql_fetch_object($result)) {
	$resdss = mysql_query("SELECT username FROM webUsers WHERE id=$resultrow->id");
	$resdss = mysql_fetch_object($resdss);
	$username = "$resdss->username";
	if( $username == $userInfo->username )
	{
		echo "<tr class=\"user_position\">";
	}
	else
	{
		echo "<tr>";
	}
	
	echo "<td>" . $rank;
	
	if( $rank == 1 )
	{
		echo "&nbsp;<img src=\"/images/crown.png\" />";
	}
	
	echo "</td><td>" . $username . "</td><td>" . ($resultrow->share_count - $resultrow->stale_share_count) . "</td></tr>";
	$rank++;
}

?>
</table>
</div>
<div id="stats_server">
<table class="stats_table server_width">
<?php

// START SERVER STATS
echo "<tr><th colspan=\"2\" scope=\"col\">Server Stats</th></tr>";
echo "<tr><th class=\"leftheader\" scope=\"col\">Current Block</th><td>".$bitcoinController->query("getblocknumber")."</td></tr>";
echo "<tr><th class=\"leftheader\" scope=\"col\">Current Difficulty</th><td>".round($bitcoinController->query("getdifficulty"), 2)."</td></tr>";

$result = mysql_query("SELECT blockNumber, confirms, timestamp FROM networkBlocks WHERE confirms > 1 ORDER BY blockNumber DESC LIMIT 1");
if ($resultrow = mysql_fetch_object($result)) {
	echo "<tr><th class=\"leftheader\" scope=\"col\">Last Block Found</th><td>".$resultrow->blockNumber."</td></tr>";
	$confirm_no = $resultrow->confirms;
	echo "<tr><th class=\"leftheader\" scope=\"col\">Confirmations</th><td>".$confirm_no;
	if( $confirm_no > 99 )
	{
		echo "&nbsp;<img src=\"/images/excited.gif\" />";
	}
	echo "</td></tr>";
	echo "<tr><th class=\"leftheader\" scope=\"col\">Time</th><td>".strftime("%B %d %Y %r",$resultrow->timestamp)."</td></tr>";
}

$res = mysql_query("SELECT count(webUsers.id) FROM webUsers WHERE hashrate > 0") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
$users = $row[0];

echo "<tr><th class=\"leftheader\" scope=\"col\">Current Users Mining</th><td>".$users."</td></tr>";
echo "<tr><th class=\"leftheader\" scope=\"col\">Current Total Miners</th><td>".$settings->getsetting('currentworkers')."</td></tr>";
echo "</table>";

echo "<br><a href=blocks.php style=\"color: blue\">More Block Info</a><br>";
echo "</div><div class=\"clear\"></div>";

include("includes/footer.php");

?>