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

$includeDirectory = "/var/www/includes/";

include($includeDirectory."requiredFunctions.php");

//Verify source of cron job request
if (isset($cronRemoteIP) && $_SERVER['REMOTE_ADDR'] !== $cronRemoteIP) {
 die(header("Location: /"));
}

lock("shares.php");

try {
	$sql = "" .
		"SELECT   SUM(id) AS id, " .
		"         a.associatedUserId " .
		"FROM     (SELECT  COUNT(s.id) AS id, " .
		"                  p.associatedUserId " .
		"         FROM     shares s, " .
		"                  pool_worker p " .
		"         WHERE    p.username  =s.username " .
		"         AND      s.our_result='Y' " .
		"         GROUP BY p.associatedUserId " .
		"          " .
		"         UNION " .
		"          " .
		"         SELECT   COUNT(s.id) AS id, " .
		"                  p.associatedUserId " .
		"         FROM     shares_history s " .
		"                  JOIN pool_worker p " .
		"                  ON       p.username = s.username " .
		"         WHERE    s.our_result        ='Y' " .
		"         AND      s.counted           =0 " .
		"         AND      s.blockNumber       > " .
		"                  (SELECT IFNULL(MAX(blockNumber), 0) " .
		"                  FROM    networkBlocks " .
		"                  WHERE   confirms > 0 " .
		"                  ) " .
		"         GROUP BY p.associatedUserId " .
		"         ) " .
		"         a " .
		"GROUP BY associatedUserId";
	$result = mysql_query($sql);
	$totalsharesthisround = 0;
	$associated_users = array();
	while ($row = mysql_fetch_array($result)) {
		$associated_users[] = $row['associatedUserId'];
		mysql_query("UPDATE webUsers SET shares_this_round=".$row["id"]." WHERE id=".$row["associatedUserId"]);
		$totalsharesthisround += $row["id"];
	}
	
	$sql = "UPDATE webUsers SET shares_this_round=0 WHERE id NOT IN (".implode(',', $id).")";
	mysql_query($sql);
} catch (Exception $ex)  { }
mysql_query("UPDATE settings SET value='".$totalsharesthisround."' WHERE setting='currentroundshares'");
?>