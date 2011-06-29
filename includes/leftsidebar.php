<?php
/*
   Copyright (C) 41a240b48fb7c10c68ae4820ac54c0f32a214056bfcfe1c2e7ab4d3fb53187a0 Name Year (sha256)

   Permission is hereby granted, free of charge, to any person obtaining a copy
   of this software and associated documentation files (the "Software"), to deal
   in the Software without restriction, including without limitation the rights
   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
   copies of the Software, and to permit persons to whom the Software is
   furnished to do so, subject to the following conditions:

   The above copyright notice and this permission notice shall be included in
   all copies or substantial portions of the Software.

   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
   THE SOFTWARE.

   Note From Author: Please donate at the following address: 1Fc2ScswXAHPUgj3qzmbRmwWJSLL2yv8Q

   Modified for http://ozco.in/
   By Tom Lightspeed = tomlightspeed@gmail.com
   and Ryan Shaw (ryannathans) = ryannathans@hotmail.com = 1NasuTCjGjLDrTafSDR416FtLQidvjk99N
*/

if(!$cookieValid){
//No valid cookie show login//
?>
<!--Login Input Field-->
<div id="leftsidebar">
<form action="/login.php" method="post" id="loginForm">
Login:<br>
<input type="text" name="username" onclick="this.value='';" onfocus="this.select()" onblur="this.value=!this.value?'Username':this.value;" value="username" />
<input type="password" name="password" onclick="this.value='';" onfocus="this.select()" onblur="this.value=!this.value?'password':this.value;" value="password" />
<input type="submit" value="LOGIN">
</form><br/>
<form action="/lostpassword.php">
<input type="submit" value="Lost Password">
</form>
</div>
<?php
}else if($cookieValid){
//Valid cookie YES! Show this user stats//
?>
<div id="leftsidebar">
<span>
<?php
echo "Welcome Back, <i><b>".$userInfo->username."</b></i><br/><hr size='1' width='100%' /><br/>";
echo "Current Hashrate: <i><b>".$currentUserHashrate." MH/s</b></i><br/>";
echo "Lifetime Shares: <i><b>".$lifetimeUserShares."</b></i><br/>";
echo "Lifetime Invalid: <i><b>".$lifetimeUserInvalidShares."</b></i><br/>";
echo "Valid This Round: <b><i>".$totalUserShares."</i> shares</b><br/>";
echo "Round Shares: <b><i>".$totalOverallShares."</i> shares</b><br/>";
echo "Est. Earnings: <b><i>".sprintf("%.8f", $userRoundEstimate)."</i> BTC</b><br/><br/>";
echo "<hr size='1' width='225'>";
echo "Current Balance: <b><i>".$currentBalance." </i>BTC</b>";
echo "<hr size='1' width='225'><br/>";

//edit by Ryan Shaw (ryannathans)

if(is_numeric($settings->getsetting('statstime'))) {
  $lastupdatedtime = time() - $settings->getsetting('statstime');
  $lastupdatedtimei = date("i", $lastupdatedtime);
  $lastupdatedtimei = $lastupdatedtimei - 0;

  if($lastupdatedtimei == '01') {
    echo 'Last Updated: 1 minute ago<br />';
  }
  elseif($lastupdatedtimei == '00') {
    echo 'Last Updated: ' . date("s", $lastupdatedtime) . ' seconds ago<br />';
  }
  else {
    echo 'Last Updated: ' . $lastupdatedtimei . ' minutes ago<br />';
  }

  $nextupdatetime = 10 - $lastupdatedtimei;


  if($nextupdatetime == '1') {
    echo 'Next Update In: ' . $nextupdatetime . ' minute<br />';
  }

  if($nextupdatetime < 0) {

    $nextupdatetime = abs($nextupdatetime);

    if($nextupdatetime < 1) {
      echo 'Server is ' . date("s", mktime(0, $nextupdatetime)) . ' seconds overdue refreshing';
    }
    if($nextupdatetime == 1) {
      echo 'Server is ' . $nextupdatetime . ' minute overdue refreshing';
    }
    if($nextupdatetime > 1) {
      echo 'Server is ' . $nextupdatetime . ' minutes overdue refreshing';
    }
  }
  else {
    echo 'Next Update In: ' . $nextupdatetime . ' minutes<br />';
  }
}
else {
  echo 'EPIC FAIL<br />statstime entry in database was not numeric<br />update times can not function without it<br />';
}

//end edit by Ryan Shaw (ryannathans)

?>
<br />
<a class="fancy_button top_spacing" href="my_stats.php">
<span style="background-color: #070;">Stats</span>
</a>
<a class="fancy_button top_spacing left_spacing" href="logout.php">
<span style="background-color: #070;">Logout</span>
</a>
</span>
</div>
<?php
}
?>
