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


print "<table cellspacing=\"0\" cellpadding=\"0\">";
    
    print "<tr>";
	print "<th colspan=\"9\">Running VServers</th>";
    print "</tr>";
    
    print "<tr>";
	print "<th>&nbsp;CTX&nbsp;</th><th>&nbsp;PROC&nbsp;</th><th>&nbsp;VSZ&nbsp;</th><th>&nbsp;RSS&nbsp;</th><th>&nbsp;userTIME&nbsp;</th><th>&nbsp;sysTIME&nbsp;</th><th>&nbsp;UPTIME&nbsp;</th><th>&nbsp;NAME&nbsp;</th><th&nbsp;</th>";
    print "</tr>";

$pd = popen("/usr/bin/sudo /usr/sbin/vserver-stat", "r" );

$firstline = true;
while( !feof($pd) )
{
    if ( $firstline )
    {
	trim( fgets($pd, 1024) );
	$firstline=false;
	
	continue;
    }    

    
    print "<tr>";

    $line = trim(fgets( $pd, 1024 ));

    while(substr_count($line , "  " )  > 0)
    {
		$line = str_replace( "  ", " ", $line );
    }

    if ( trim($line) == "" ) continue;

    $info = split( " ", $line, 8 );


    $i = 1;    
    foreach( $info as $field )
    {
	if ( $field == "" ) continue;

	if ( $i == 8 ) $serverName = $field;

	print "<td>";
	    print "&nbsp;".trim( $field )."&nbsp;";
	print "</td>";

	

	$i++;

    }
    
    print "<td><a href=\"editvserver.php\">edit</a></td>";

    print "</tr>";

}
print "</table>";


htmlbottom();
?>
