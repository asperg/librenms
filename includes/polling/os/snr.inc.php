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

$hardware = trim(snmp_get($device, '.1.3.6.1.4.1.40418.7.100.25.1.1.1.0', '-OQv'), '"');
$version = trim(snmp_get($device, '.1.3.6.1.4.1.40418.7.100.25.1.1.2.0', '-OQv'), '"');
$serial = trim(snmp_get($device, '.1.3.6.1.2.1.47.1.1.1.1.11.1', '-OQv'), '"');

if (empty($hardware) && empty($version)) {
    $temp_data = snmp_get_multi_oid($device, ['sysHardwareVersion.1', 'sysSoftwareVersion.1'], '-OUQs', 'NAG-MIB');
    $hardware =  $temp_data['sysHardwareVersion.1'];
    $version = $temp_data['sysSoftwareVersion.1'];
    unset($temp_data);
}
