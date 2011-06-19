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
$pageTitle = "- Stats";
include ("includes/header.php");

$numberResults = 30;
$last_no_blocks_found = 5;

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

	echo "</td><td>" . $username . "</td><td>" . number_format( $resultrow->hashrate ) . "</td></tr>";

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

	echo "<tr class=\"user_position\"><td>" . $row['rank'] . "</td><td>" . $userInfo->username . "</td><td>" . number_format( $row['hashrate'] ) . "</td></tr>";
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
$user_found = false;

while ($resultrow = mysql_fetch_object($result)) {
	$resdss = mysql_query("SELECT username FROM webUsers WHERE id=$resultrow->id");
	$resdss = mysql_fetch_object($resdss);
	$username = $resdss->username;
	if( $username == $userInfo->username )
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

// START SERVER STATS
echo "<tr><th colspan=\"2\" scope=\"col\">Server Stats</td></tr>";

$current_block_no = $bitcoinController->query("getblocknumber");

echo "<tr><td class=\"leftheader\">Current Block</td><td><a href=\"http://blockexplorer.com/b/" . $current_block_no . "\">";
echo number_format($current_block_no) . "</a></td></tr>";

$difficulty = $bitcoinController->query("getdifficulty");
$show_difficulty = round($difficulty, 2);

echo "<tr><td class=\"leftheader\">Current Difficulty</th><td><a href=\"http://dot-bit.org/tools/nextDifficulty.php\">" . number_format($show_difficulty) . "</a></td></tr>";

$result = mysql_query("SELECT blockNumber, confirms, timestamp FROM networkBlocks WHERE confirms > 1 ORDER BY blockNumber DESC LIMIT 1");

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
	echo "<tr><td class=\"leftheader\">Time Found</td><td>".strftime("%B %d %Y %r",$resultrow->timestamp)."</td></tr>";

}

$res = mysql_query("SELECT count(webUsers.id) FROM webUsers WHERE hashrate > 0") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
$users = $row[0];

echo "<tr><td class=\"leftheader\">Current Users Mining</td><td>" . number_format($users) . "</td></tr>";
echo "<tr><td class=\"leftheader\">Current Total Miners</td><td>" . number_format($settings->getsetting('currentworkers')) . "</td></tr>";

$hashrate = $settings->getsetting('currenthashrate') / 1000;
$show_hashrate = round($hashrate,3);
//time = difficulty * 2**32 / hashrate
$time_to_find = ((($difficulty * 2^32 / ($hashrate * 1000000000)) / 60) / 60);
$time_to_find = round( $time_to_find, 2 );

echo "<tr><td class=\"leftheader\">Pool Hash Rate</td><td>". number_format($show_hashrate, 3) ." Ghashes/s</td></tr>";
echo "<tr><td class=\"leftheader\">Time To Find Block</td><td>" . $time_to_find . " Hours</td></tr>";
echo "</table>";

// SHOW LAST (=$last_no_blocks_found) BLOCKS FOUND

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
		$confirms = Completed;
	}

	$block_no = $resultrow->blockNumber;

	echo "<td><a href=\"http://blockexplorer.com/b/$block_no\">" . number_format($block_no) . "</a></td>";
	echo "<td>" . $confirms . "</td>";
	echo "<td>$realUsername</td>";
	echo "<td>".strftime("%F %r",$resultrow->timestamp)."</td>";
	echo "</tr>";
}

echo "</table>";
echo "</div><div class=\"clear\"></div>";

include("includes/footer.php");

?>