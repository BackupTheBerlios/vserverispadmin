<?php
/*
 * vServerISPAdmin - ISP style administration for vServers
 * Copyright (C) 2004 Christoph Mertins <c.mertins@gmx.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

include "../lib/global.inc";

htmltop();

$MODE = $_POST["MODE"];

if ( $MODE == "" )
{
?>


<form name="editvserver" action="newvserver.php" method="post">
    <input type="hidden" name="MODE" value="save" />
    Servername: <input type="text" name="serverName" /><br />
    Serverip: <input type="text" name="serverIp" /><br />
    Server-DNS-name: <input type="text" name="serverDnsName" /><br />
    RootPassword: <input type="password" name="rootPW" /><br />
    
    <input type="submit" value="submit" />
    
</form>
<?php
}
elseif ( $MODE == "save" )
{
    set_time_limit(400);
    $CMD =  "/usr/bin/sudo ".$_CONF["SCRIPT_CREATE_VSERVER"]." ".$_POST["serverName"]." ".$_POST["serverIp"]." ".$_POST["serverDnsName"];
    print $CMD;
    $pd = popen( $CMD, "r" );
    
    while( !feof($pd) )
    {
	print fgets( $pd, 1024)."<br />";
	flush();
    }

}


htmlbottom();
?>
