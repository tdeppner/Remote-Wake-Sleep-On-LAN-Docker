<?php
	/*
	Remote Wake/Sleep-On-LAN Server [CONFIGURATION FILE]
	https://github.com/sciguy14/Remote-Wake-Sleep-On-LAN-Server
	Original Author: Jeremy E. Blum (http://www.jeremyblum.com)
	Security Edits By: Felix Ryan (https://www.felixrr.pro)
	License: GPL v3 (http://www.gnu.org/licenses/gpl.html)

	UPDATE THE VALUES IN THIS FILE AND CHANGE THE NAME TO: "config.php"
	*/

	// Set the below to "true" (no quotes) to enforce HTTPS connections (don't forget to create a self-signed cert and enable it in Apache2)
	$USE_HTTPS = false;

	// Now a php password() value, which contains dollar signs, so must use single quotes.
	// Will be generated and substituted based on PASSPHRASE env variable via entrypoint.sh.
	$APPROVED_HASH = 'RWSOLS_HASH';

	// This is the number of times that the WOL server will try to ping the target computer to check if it has woken up. Default = 15.
	$MAX_PINGS = REPLACE_RWSOLS_MAX_PINGS;
	// This is the number of seconds to wait between pings commands when waking up or sleeping. Waking from shutdown or sleep will impact this.
	$SLEEP_TIME = REPLACE_RWSOLS_SLEEP_TIME;

	// This is the Name of the computers to appear in the drop down
	$COMPUTER_NAME = array(REPLACE_RWSOLS_COMPUTER_NAME);

	// This is the MAC address of the Network Interface on the computer you are trying to wake.
	$COMPUTER_MAC = array(REPLACE_RWSOLS_COMPUTER_MAC);

	// This is the LOCAL IP address of the computer you are trying to wake.  Use a reserved DHCP through your router's administration interface to ensure it doesn't change.
	$COMPUTER_LOCAL_IP = array(REPLACE_RWSOLS_COMPUTER_IP);

	// This is the Port being used by the Windows SleepOnLan Utility to initiate a Sleep State
	// http://www.ireksoftware.com/SleepOnLan/
	// Alternate Download Link: http://www.jeremyblum.com/wp-content/uploads/2013/07/SleepOnLan.zip
	$COMPUTER_SLEEP_CMD_PORT = REPLACE_RWSOLS_SLEEP_PORT;

	// Command to be issued by the windows sleeponlan utility
	// options are suspend, hibernate, logoff, poweroff, forcepoweroff, lock, reboot
	// You can create a windows scheduled task that starts sleeponlan.exe on boot with following startup parameters /auto /port=7760
	$COMPUTER_SLEEP_CMD = "REPLACE_RWSOLS_SLEEP_CMD";

	// This is the location of the bootstrap style folder relative to your index and config file. Default = "" (Same folder as this file)
	// Directory must be called "bootstrap". You may wish to move if this WOL script is the "child" of a larger web project on your Pi, that will also use bootstrap styling.
	// If if it on directory up, for example, you would set this to "../"
	// Two directories up? Set too "../../"
	// etc...
	$BOOTSTRAP_LOCATION_PREFIX = "";

    $DEBUG = false;
