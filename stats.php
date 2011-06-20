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
//
//    Improved Stats written by Tom Lightspeed (tomlightspeed@gmail.com + http://facebook.com/tomlightspeed)
//    Developed Socially for http://ozco.in
//    If you liked my work, want changes/etc please contact me or donate 16p56JHwLna29dFhTRcTAurj4Zc2eScxTD.
//    May the force be with you.

$pageTitle = "- Stats";
include ("includes/header.php");

$numberResults = 30;
$last_no_blocks_found = 5;

$BTC_per_block = 50; // don't keep this hardcoded

$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);

$difficulty = $bitcoinController->query("getdifficulty");
//time = difficulty * 2**32 / hashrate
// hashrate is in Mhash/s
function CalculateTimePerBlock( $btc_difficulty, $_hashrate ){
	$find_time_hours = ((($btc_difficulty * bcpow(2,32)) / ($_hashrate * bcpow(10,6))) / 3600);
	return $find_time_hours;
}

function CoinsPerDay( $time_per_block, $btc_block ){
	$coins_per_day = (24 / $time_per_block) * $btc_block;
	return $coins_per_day;
}
?>

<div id="stats_wrap">
<?php
if( !$cookieValid ){
	echo "<div id=\"new_user_message\"><p>Welcome to <a href=\"https://ozco.in/\">ozco.in</a> visitor! Please login or <a href=\"https://ozco.in/register.php\">join us</a> to get detailed stats and graphs relating to your hashing!</p></div>";
}
?>
<div id="stats_members">
<table class="stats_table member_width">
<tr><th colspan="4" scope="col">Top <?php echo $numberResults;?> Hashrates</th></tr>
<tr><th scope="col">Rank</th><th scope="col">User Name</th><th scope="col">MH/s</th><th scope="col">BTC/Day</th></tr>
<?php

// TOP 30 CURRENT HASHRATES  *************************************************************************************************************************

$result = mysql_query("SELECT id, hashrate FROM webUsers ORDER BY hashrate DESC LIMIT " . $numberResults);
$rank = 1;
$user_found = false;

while ($resultrow = mysql_fetch_object($result)) {
	$resdss = mysql_query("SELECT username FROM webUsers WHERE id=$resultrow->id");
	$resdss = mysql_fetch_object($resdss);
	$username = $resdss->username;
	if( $cookieValid && $username == $userInfo->username )
	{
		echo "<tr class=\"user_position\">";
		$user_found = true;
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
	
	$user_hash_rate = $resultrow->hashrate;

	echo "</td><td>" . $username . "</td><td>" . number_format( $user_hash_rate ) . "</td><td>&nbsp;";
	
	$time_per_block = CalculateTimePerBlock($difficulty, $user_hash_rate);
	
	$coins_day = CoinsPerDay($time_per_block, $BTC_per_block);
	
	echo number_format( $coins_day, 3 );
	
	echo "</td></tr>";

	$rank++;
}

if( $cookieValid && $user_found == false )
{
	$query_init       = "SET @rownum := 0";

	$query_getrank    =   "SELECT rank, hashrate FROM (
                        SELECT @rownum := @rownum + 1 AS rank, hashrate, id
                        FROM webUsers ORDER BY hashrate DESC
                        ) as result WHERE id=" . $userInfo->id;

	mysql_query( $query_init );
	$result = mysql_query( $query_getrank );
	$row = mysql_fetch_array( $result );
	
	$user_hashrate = $row['hashrate'];

	echo "<tr class=\"user_position\"><td>" . $row['rank'] . "</td><td>" . $userInfo->username . "</td><td>" . number_format( $user_hashrate ) . "</td><td>";
	
	$time_per_block = CalculateTimePerBlock($difficulty, $user_hashrate);
	
	$coins_day = CoinsPerDay($time_per_block, $BTC_per_block);
	
	echo number_format( $coins_day, 3 ) . "</td></tr>";
}
?>
</table>
</div>
<div id="stats_lifetime">
<table class="stats_table member_width">
<tr><th colspan="3" scope="col">Top <?php echo $numberResults;?> Lifetime Shares</th></tr>
<tr><th scope="col">Rank</th><th scope="col">User Name</th><th scope="col">Shares</th></tr>
<?php

// TOP 30 LIFETIME SHARES  *************************************************************************************************************************

$result = mysql_query("SELECT id, share_count, stale_share_count FROM webUsers ORDER BY share_count DESC LIMIT " . $numberResults);
$rank = 1;
$user_found = false;

while ($resultrow = mysql_fetch_object($result)) {
	$resdss = mysql_query("SELECT username FROM webUsers WHERE id=$resultrow->id");
	$resdss = mysql_fetch_object($resdss);
	$username = $resdss->username;
	if( $cookieValid && $username == $userInfo->username )
	{
		echo "<tr class=\"user_position\">";
		$user_found = true;
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

	echo "</td><td>" . $username . "</td><td>" . number_format($resultrow->share_count - $resultrow->stale_share_count) . "</td></tr>";
	$rank++;
}

if( $cookieValid && $user_found == false )
{
	$query_init       = "SET @rownum := 0";

	$query_getrank    =   "SELECT rank, shares FROM (
                        SELECT @rownum := @rownum + 1 AS rank, share_count-stale_share_count AS shares, id
                        FROM webUsers ORDER BY shares DESC
                        ) as result WHERE id=" . $userInfo->id;

	mysql_query( $query_init );
	$result = mysql_query( $query_getrank );
	$row = mysql_fetch_array( $result );

	echo "<tr class=\"user_position\"><td>" . $row['rank'] . "</td><td>" . $userInfo->username . "</td><td>" . number_format( $row['shares'] ) . "</td></tr>";
}
?>
</table>
</div>
<div id="stats_server">
<table class="stats_table server_width">
<?php

// START SERVER STATS *************************************************************************************************************************
echo "<tr><th colspan=\"2\" scope=\"col\">Server Stats</td></tr>";

$current_block_no = $bitcoinController->query("getblocknumber");

echo "<tr><td class=\"leftheader\">Current Block</td><td><a href=\"http://blockexplorer.com/b/" . $current_block_no . "\">";
echo number_format($current_block_no) . "</a></td></tr>";

$show_difficulty = round($difficulty, 2);

echo "<tr><td class=\"leftheader\">Current Difficulty</th><td><a href=\"http://dot-bit.org/tools/nextDifficulty.php\">" . number_format($show_difficulty) . "</a></td></tr>";

$result = mysql_query("SELECT blockNumber, confirms, timestamp FROM networkBlocks WHERE confirms > 1 ORDER BY blockNumber DESC LIMIT 1");

$show_time_since_found = false;
$time_last_found;

if ($resultrow = mysql_fetch_object($result)) {

	$found_block_no = $resultrow->blockNumber;
	$confirm_no = $resultrow->confirms;

	echo "<tr><td class=\"leftheader\">Last Block Found</td><td><a href=\"http://blockexplorer.com/b/" . $found_block_no . "\">" . number_format($found_block_no) . "</a></td></tr>";
	echo "<tr><td class=\"leftheader\">Confirmations</td><td>" . number_format($confirm_no);

	if( $confirm_no > 99 )
	{
		echo "&nbsp;<img src=\"/images/excited.gif\" />";
	}

	echo "</td></tr>";
	
	$time_last_found = $resultrow->timestamp;
	
	echo "<tr><td class=\"leftheader\">Time Found</td><td>".strftime("%B %d %Y %r", $time_last_found)."</td></tr>";
	
	$show_time_since_found = true;
}

$res = mysql_query("SELECT count(webUsers.id) FROM webUsers WHERE hashrate > 0") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
$users = $row[0];

echo "<tr><td class=\"leftheader\">Current Users Mining</td><td>" . number_format($users) . "</td></tr>";
echo "<tr><td class=\"leftheader\">Current Total Miners</td><td>" . number_format($settings->getsetting('currentworkers')) . "</td></tr>";

$hashrate = $settings->getsetting('currenthashrate');
$show_hashrate = round($hashrate / 1000,3);

$time_to_find = CalculateTimePerBlock($difficulty, $hashrate);
// change 25.75 hours to 25:45 hours
$intpart = floor( $time_to_find );
$fraction = $time_to_find - $intpart; // results in 0.75
$minutes = number_format(($fraction * 60 ),0);

echo "<tr><td class=\"leftheader\">Pool Hash Rate</td><td>". number_format($show_hashrate, 3) . " Ghashes/s</td></tr>";
echo "<tr><td class=\"leftheader\">Est. Time To Find Block</td><td>" . number_format($time_to_find,0) . " Hours " . $minutes . " Minutes</td></tr>";

$now = new DateTime( "now" );
$hours_diff = ($now->getTimestamp() - $time_last_found) / 3600;
$time_last_found_out = $hours_diff%24 . " Hours " . $hours_diff*60%60 . " Minutes";

echo "<tr><td class=\"leftheader\">Time Since Last Block</td><td>" . $time_last_found_out . "</td></tr>";

echo "</table>";

// SHOW LAST (=$last_no_blocks_found) BLOCKS  *************************************************************************************************************************

echo "<table class=\"stats_table server_width top_spacing\">";
echo "<tr><th scope=\"col\" colspan=\"4\">Last $last_no_blocks_found Blocks Found - <a href=\"blocks.php\">All Blocks Found</a></th></tr>";
echo "<tr><th scope=\"col\">Block</th><th scope=\"col\">Confirms</th><th scope=\"col\">Finder</th><th scope=\"col\">Time</th></tr>";

$result = mysql_query("SELECT blockNumber, confirms, timestamp FROM networkBlocks WHERE confirms > 1 ORDER BY blockNumber DESC LIMIT " . $last_no_blocks_found);

while($resultrow = mysql_fetch_object($result)) {
	echo "<tr>";
	$resdss = mysql_query("SELECT username FROM shares_history WHERE upstream_result = 'Y' AND blockNumber = $resultrow->blockNumber");
	$resdss = mysql_fetch_object($resdss);

	$splitUsername = explode(".", $resdss->username);
	$realUsername = $splitUsername[0];

	$confirms = $resultrow->confirms;

	if ($confirms > 120) {
		$confirms = "Done";
	}

	$block_no = $resultrow->blockNumber;

	echo "<td><a href=\"http://blockexplorer.com/b/$block_no\">" . number_format($block_no) . "</a></td>";
	echo "<td>" . $confirms . "</td>";
	echo "<td>$realUsername</td>";
	echo "<td>".strftime("%F %r",$resultrow->timestamp)."</td>";
	echo "</tr>";
}

echo "</table>";

// SERVER HASHRATE/TIME GRAPH *************************************************************************************************************************
// http://www.filamentgroup.com/lab/update_to_jquery_visualize_accessible_charts_with_html5_from_designing_with/
// table is hidden, graph follows
/*
   uncomment once db changes have been made
echo "<table class=\"hide\">";
echo "<caption>Pool Hashrate over 1 Month</caption>";
echo "<thead><tr><td></td>";

echo "</thead><tbody>";

echo "</tbody></table>";
   
*/
/*
	<table>
	<caption>2009 Employee Sales by Department</caption>
	<thead>
	<tr>
	<td></td>
	<th scope="col">food</th>
	<th scope="col">auto</th>
	<th scope="col">household</th>
	<th scope="col">furniture</th>
	<th scope="col">kitchen</th>
	<th scope="col">bath</th>
	</tr>
	</thead>
	<tbody>
	<tr>
	<th scope="row">Mary</th>
	<td>190</td>
	<td>160</td>
	<td>40</td>
	<td>120</td>
	<td>30</td>
	<td>70</td>
	</tr>
		</tbody>
	</table>
*/
	
echo "</div><div class=\"clear\"></div>";

include("includes/footer.php");

?>