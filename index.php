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
$pageTitle = "- Home";
include ("includes/header.php");

$blogPostsQ = mysql_query("SELECT `timestamp`, `title`, `message` FROM `news` ORDER BY `timestamp` DESC");
		while($blog = mysql_fetch_array($blogPostsQ)){

				?><h2 style="text-decoration:underline;"><?php echo $blog["title"]." | ".date("M,d Y g:ja", $blog["timestamp"]);?></h2><br/><?
				$text = nl2br($blog["message"]);
				echo "$text";
}

?>

<?php include ("includes/footer.php"); ?>