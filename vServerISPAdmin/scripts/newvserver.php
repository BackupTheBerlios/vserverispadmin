#!/usr/bin/php -nq
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


$NOHTML = true;

$SERVERNAME = $argv["1"];
$SERVERIP = $argv["2"];
$SERVERDNSNAME = $argv["3"];
$ROOTPASSWORD = $argv["4"];

$VSERVERDIR = "/vservers";
$VSERVERCONFDIR = "/usr/etc/vservers";

$TARGETDIR = $VSERVERDIR."/".$SERVERNAME;
$TARGETCONFDIR = $VSERVERCONFDIR."/".$SERVERNAME;

$TEMPLATE = "/vservers/default-template.tar.bz2";
$TEMPLATECONF = "/vservers/default-template-config.tar.bz2";

include "../lib/global.inc";
set_time_limit(400);
    print "Creating directories\n";

    if ( file_exists( $TARGETDIR ) )
    {
	print "VServer already existing (in ".$TARGETDIR.")\n";
	exit(1);
    }
    else
    {
        mkdir( $TARGETDIR );
    }

    if ( file_exists( $TARGETCONFDIR ) )
    {
	print "VServer already existing (in ".$TARGETCONFDIR.")\n";
	exit(1);
    }
    else
    {
        mkdir( $TARGETCONFDIR );
    }
    
    print "Copying server from template\n";
    chdir( $TARGETDIR );
    exec("tar xjpf ".$TEMPLATE);

    chdir( $TARGETCONFDIR );
    exec("tar xjpf ".$TEMPLATECONF);
    

    print "Copying server-configuration from template\n";

    chdir( $TARGETCONFDIR );
    exec("tar xjpf ".$TEMPLATECONF);

    print "Apply configuration to server\n";
    
    exec( "echo ".$SERVERNAME." > ".$TARGETCONFDIR."/name");
    exec( "echo ".$SERVERIP." > ".$TARGETCONFDIR."/interfaces/0/ip");
    exec( "echo ".$SERVERDNSNAME." > ".$TARGETCONFDIR."/uts/nodename");

    exec( "echo \"#!/bin/bash\" > ".$TARGETCONFDIR."/scripts/post-stop");
    exec( "echo \"umount ".$TARGETDIR."/usr/portage\" >> ".$TARGETCONFDIR."/scripts/post-stop");

    exec( "echo \"#!/bin/bash\" > ".$TARGETCONFDIR."/scripts/pre-start");
    exec( "echo \"mount -o bind /usr/portage ".$TARGETDIR."/usr/portage\" >> ".$TARGETCONFDIR."/scripts/pre-start");

    unlink( $TARGETCONFDIR."/run" );
    symlink( "/usr/var/run/vservers/".$SERVERNAME, $TARGETCONFDIR."/run" );

    unlink( $TARGETCONFDIR."/vdir" );
    symlink( "/usr/etc/vservers/.defaults/vdirbase/".$SERVERNAME, $TARGETCONFDIR."/vdir" );

    exec( "echo \"127.0.0.1	localhost\" > ".$TARGETDIR."/etc/hosts");
    exec( "echo \"".$SERVERIP." ".$SERVERDNSNAME." ".$SERVERNAME."\" >> ".$TARGETDIR."/etc/hosts");

    exec( "echo ".$SERVERNAME." > ".$TARGETDIR."/etc/hostname");    

    print "Setting root password\n";
    
    
    $lines = array();
    
    $fd = fopen( $TARGETDIR."/etc/shadow", "r" );
    
    while (!feof( $fd ) )
    {
	$lines[] = trim( fgets( $fd, 1024 ) );
    }
    fclose( $fd );
    

    $fd = fopen( $TARGETDIR."/etc/shadow", "w" );
    
    foreach( $lines as $line )
    {
	if ( ereg( "^root", $line ) )
	{
	    fputs( $fd, "root:".crypt($ROOTPASSWORD).":12600:0:::::\n" );
	}
	else
	{
	    fputs( $fd, $line."\n" );
	}
    
    }
    
    fclose( $fd );
    
    print "Booting server\n";
    system("/usr/sbin/vserver ".$SERVERNAME." start &");
    print "done\n";


?>
