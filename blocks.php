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

echo "<h2>Blocks Found</h2><br/>";
echo "<table width=600 border=1 cellspacing=1 cellpadding=5>";
echo "<tr><td align=left><B>Block</B></td><td align=left><B>Confirms</B></td><td align=left><b>Finder</b></td><td align=left><b>Time</b></td></tr>";

$result = mysql_query("SELECT blockNumber, confirms, timestamp FROM networkBlocks WHERE confirms > 1 ORDER BY blockNumber DESC");

while($resultrow = mysql_fetch_object($result)) {
	print("<tr>");
	$resdss = mysql_query("SELECT username FROM shares_history WHERE upstream_result = 'Y' AND blockNumber = $resultrow->blockNumber");
	$resdss = mysql_fetch_object($resdss);

	$splitUsername = explode(".", $resdss->username);
	$realUsername = $splitUsername[0];

	$confirms = $resultrow->confirms;

	if ($confirms > 120) {
		$confirms = Completed;
	}

	echo "<td align=left>$resultrow->blockNumber</td>";
	echo "<td align=left>$confirms</td>";
	echo "<td align=left>$realUsername</td>";
	echo "<td align=left>".strftime("%B %d %Y %r",$resultrow->timestamp)."</td>";
}

echo "</table>";
echo "You will not get paid till Confirms have hit 120";
echo "<br><a href=stats.php style=\"color: blue\">Back to stats</a><br>";

include("includes/footer.php");