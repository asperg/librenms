<?php
/**
 * snr.inc.php
 *
 * LibreNMS os poller module for SNR
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    LibreNMS
 * @link       http://librenms.org
 * @copyright  2016 Neil Lathwood
 * @author     Neil Lathwood <neil@lathwood.co.uk>
 */

if (strpos($device['sysObjectID'], '.1.3.6.1.4.1.17409') !== false) { // FD12xx series
    $hardware = trim(snmp_get($device, '.1.3.6.1.4.1.17409.2.3.1.2.1.1.3.1', '-OQv'), '"');
    $version = trim(snmp_get($device, '.1.3.6.1.4.1.17409.2.3.1.3.1.1.8.1.0', '-OQv'), '"');
    $serial = trim(snmp_get($device, '.1.3.6.1.4.1.17409.2.3.1.3.1.1.12.1.0', '-OQv'), '"');

    $hardware = "FD12xx " . $hardware;
}


if (strpos($device['sysObjectID'], '.1.3.6.1.4.1.34592') !== false) { // FD11xx series
    #$hardware = trim(snmp_get($device, '.1.3.6.1.4.1.17409.2.3.1.2.1.1.3.1', '-OQv'), '"');
    $version = trim(snmp_get($device, '.1.3.6.1.4.1.34592.1.3.1.5.1.3.0', '-OQv'), '"') . " " . trim(snmp_get($device, '.1.3.6.1.4.1.34592.1.3.1.5.1.4.0', '-OQv'), '"') ;
    #$serial = trim(snmp_get($device, '.1.3.6.1.4.1.17409.2.3.1.3.1.1.12.1.0', '-OQv'), '"');
    $hardware = "FD11xx ";
}
