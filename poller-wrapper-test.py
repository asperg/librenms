#! /usr/bin/env python2
try:

    import json
    import os
    import Queue
    import subprocess
    import sys
    import threading
    import time
    from optparse import OptionParser

except:
    print "ERROR: missing one or more of the following python modules:"
    print "threading, Queue, sys, subprocess, time, os, json"
    sys.exit(2)

try:
    import MySQLdb
except:
    print "ERROR: missing the mysql python module:"
    print "On ubuntu: apt-get install python-mysqldb"
    print "On FreeBSD: cd /usr/ports/*/py-MySQLdb && make install clean"
    sys.exit(2)

"""
    Fetch configuration details from the config_to_json.php script
"""

ob_install_dir = os.path.dirname(os.path.realpath(__file__))
config_file = ob_install_dir + '/config.php'


def get_config_data():
    config_cmd = ['/usr/bin/env', 'php', '%s/config_to_json.php' % ob_install_dir]
    try:
        proc = subprocess.Popen(config_cmd, stdout=subprocess.PIPE, stdin=subprocess.PIPE)
    except:
        print "ERROR: Could not execute: %s" % config_cmd
        sys.exit(2)
    return proc.communicate()[0]

try:
    with open(config_file) as f:
        pass
except IOError as e:
    print "ERROR: Oh dear... %s does not seem readable" % config_file
    sys.exit(2)

try:
    config = json.loads(get_config_data())
except:
    print "ERROR: Could not load or parse configuration, are PATHs correct?"
    sys.exit(2)

poller_path = config['install_dir'] + '/poller.php'
log_dir = config['log_dir']
db_username = config['db_user']
db_password = config['db_pass']
db_port = int(config['db_port'])

if config['db_socket']:
    db_server = config['db_host']
    db_socket = config['db_socket']
else:
    db_server = config['db_host']
    db_socket = None

db_dbname = config['db_name']


def memc_alive():
    try:
        global memc
        key = str(uuid.uuid4())
        memc.set('poller.ping.' + key, key, 60)
        if memc.get('poller.ping.' + key) == key:
            memc.delete('poller.ping.' + key)
            return True
        else:
            return False
    except:
        return False


def memc_touch(key, time):
    try:
        global memc
        val = memc.get(key)
        memc.set(key, val, time)
    except:
        pass


def get_time_tag(step):
    ts = int(time.time())
    return ts - ts % step


if 'rrd' in config and 'step' in config['rrd']:
    step = config['rrd']['step']
else:
    step = 300

if ('distributed_poller' in config and 'distributed_poller_memcached_host' in config and 'distributed_poller_memcached_port' in config and config['distributed_poller']):

    time_tag = str(get_time_tag(step))
    master_tag = "poller.master." + time_tag
    nodes_tag = "poller.nodes." + time_tag

    try:
        import memcache
        import uuid
        memc = memcache.Client([config['distributed_poller_memcached_host'] + ':' +  str(config['distributed_poller_memcached_port'])])
        #master_tag = 'poller.master.1586172900'
        mcc = str(memc.get(master_tag))
        print "mtag is     : %s" % (master_tag)
        print "memcache str: %s" % (mcc)
    except SystemExit:
        raise
    except ImportError:
        print "ERROR: missing memcache python module:"
        distpoll = False
else:
    distpoll = False
