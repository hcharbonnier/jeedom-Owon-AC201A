# jeedom-Owon-AC201A
Support for Owon AC201A IR Blaster in Jeedom
##

## Warning

alpha/unstable version

## Requirement

* Official Jeedom's Zigbee Plugin

## Setup

* copy the 3 files from device/owon folder to /var/www/html/plugins/zigbee/core/config/devices/owon/ jeedom's folder
* copy the file ac201a.py from quirks folder to /var/www/html/plugins/zigbee/resources/zigbeed/quirks/ jeedom's folder 
* create a scenario named AC201A-scenario in Jeedom
* paste the code from the file scenario/AC201-scenario.php to a code block in the scenario

## Configuration

* Reset your AC201A device by pressing the reset button fro more than 10 seconds.
* Pair your device in Jeedom's Ziggbe Plugin, and set its name (ie: AC201A)
* In the device settings page, set auto-actualisation feature to every minute 
* Edit the previously created scenario (AC201A-scenario)
* Configure this 4 commands as scenario's trigger:
* #[Aucun][AC201A][Mode Thermostat]#
* #[Aucun][AC201A][Consigne Froid Présence]#
* #[Aucun][AC201A][Consigne Chaud Présence]#
* #[Aucun][AC201A][Mode Ventilation]#

## Finding the IR Code

* Go to the Zigbee plugin's settings page
* Set the log level to debug
* Go to the Log page (Analyse/Logs), and select the logfile named zigbeed or zigbeed_1
* At the top of the windows, write pairing_code in the search field
* Open a new tab in your browser
* Go to the AC201 settings page
* Click on the "Module configuration" button
* Click on the Configuration tab in the new windows
* Click on the "Start Learning" button
* Your AC201A device should now display "L--"
* Point you AC Split remote to your AC201A device and press power button on the rmeote
* If the IR code is successfully read, the AC201 device will display "L-S"
* If the AC201 device display "L-F", restart the learnig procedure
* Now go back to your log tab and if you search pairing_code1 you will find your pairing code.
* Go back to the other tab, go to the "Set IR code" part of the page
* write your IRcode in the code field and click on the "Send IR Code" button

## Files description

* device/owon/* :device definitino in jeedom
* quirks/*.py: zigpy quirks (necessary to define IRCODE learning commands)
* scenario/*.php: AC201A is not folloing the Zigbee standard. When user modify a thermostat attribute in Jeedom, the AC201A device get the value, store it, but do nothing of it... This scenario is used to trigger the right zigbee manufacturer command to manage the thermostat when user modify the Zigbee thermostat attributes in Jeedom. 