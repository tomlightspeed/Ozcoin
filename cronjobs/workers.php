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

lock("workers.php");
	
/////////Update workers
$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);
//Get difficulty
$difficulty = $bitcoinController->query("getdifficulty");

//Get site percentage
$sitePercent = 0;
$sitePercentQ = mysql_query("SELECT value FROM settings WHERE setting='sitepercent'");
if ($sitePercentR = mysql_fetch_object($sitePercentQ)) $sitePercent = $sitePercentR->value;				

//Setup score variables
$c = .001;
$f=1;
if ($sitePercent > 0)
	$f = $sitePercent / 100;
else
	$f = (-$c)/(1-$c);
$p = 1.0/$difficulty;
$r = log(1.0-$p+$p/$c);
$B = 50;
$los = log(1/(exp($r)-1));

//Active in past 10 minutes
try {
	$sql ="SELECT sum(a.id) IS NOT NULL AS active, p.username FROM pool_worker p LEFT JOIN ".
		  "(SELECT count(id) AS id, username FROM shares WHERE time > DATE_SUB(now(), INTERVAL 10 MINUTE) group by username ". 
		  "UNION ".
		  "SELECT count(id) AS id, username FROM shares_history WHERE time > DATE_SUB(now(), INTERVAL 10 MINUTE) group by username) a ON p.username=a.username group by username";
	$result = mysql_query($sql);
	while ($resultObj = mysql_fetch_object($result)) {
		mysql_query("UPDATE pool_worker p SET active=".$resultObj->active." WHERE username='".$resultObj->username."'");
	}
} catch (Exception $e) {}

$totalRoundShares = $settings->getsetting("currentroundshares");
//if ($totalRoundShares < $difficulty) $totalRoundShares = $difficulty;
$userListQ = mysql_query("UPDATE webUsers SET round_estimate = (1-".$f.")*49.5*(shares_this_round/".$totalRoundShares.")*(1-(donate_percent/100))");
