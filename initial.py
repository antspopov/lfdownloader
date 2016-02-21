#!/usr/bin/env python
# -*- coding: utf-8 -*-

from lxml.html import parse
from lxml.html import tostring
import urllib2
import MySQLdb
import ConfigParser
import os, stat
import logging
import sys
import transmissionrpc
import bencode
import base64

if not os.path.exists('/var/log/lf_d'):
    os.makedirs('/var/log/lf_d')
    os.chmod("/var/log/lf_d", stat.S_IRWXU)
    os.chmod("/var/log/lf_d", stat.S_IRWXG)
    os.chmod("/var/log/lf_d", stat.S_IRWXO)
    file = open("/var/log/lf_d/lf_d.log", "w")
    file.write("Created")
    file.close()
    os.chmod("/var/log/lf_d/lf_d.log", stat.S_IRWXU)
    os.chmod("/var/log/lf_d/lf_d.log", stat.S_IRWXG)
    os.chmod("/var/log/lf_d/lf_d.log", stat.S_IRWXO)
    logging.warning('Не найден файл лога. Создание...')

logging.basicConfig(format='%(filename)s[LINE:%(lineno)d]# %(levelname)-8s [%(asctime)s]  %(message)s', level=logging.DEBUG, filename='/var/log/lf_d/lf_d.log')

config = ConfigParser.ConfigParser()
ROOT_PATH = os.path.dirname(__file__)
if not os.path.exists(ROOT_PATH + '/config.ini'):
    logging.error('Не найден конфигурационный файл. Для создания файла перейдите на веб страницу службы.')
    exit()
config.read(ROOT_PATH + '/config.ini')

COOKIE = ("Cookie", "uid=" + config.get('lostfilm', 'uid') + "; pass=" + config.get('lostfilm', 'pass') + "; usess=" + config.get('lostfilm', 'usess') + "; phpbb2mysql_data=" + config.get('lostfilm', 'phpbb2mysql_data'))

try:
    db = MySQLdb.connect(host=config.get('mysql', 'host'), user=config.get('mysql', 'db_username'), passwd=config.get('mysql', 'db_password'), db=config.get('mysql', 'db_name'), charset='utf8' , init_command='SET NAMES UTF8', use_unicode = True)
except Exception, error_message:
    logging.error('MySQL error ' + format(error_message[0]) + ': ' + format(error_message[1]))

db.set_character_set('utf8')
request = db.cursor()
request.execute('SET NAMES utf8;')
request.execute('SET CHARACTER SET utf8;')
request.execute('SET character_set_connection=utf8;')

try:
    tc = transmissionrpc.Client(config.get('transmission', 'host'), port=config.get('transmission', 'port'), user=config.get('transmission', 'user'), password=config.get('transmission', 'password'))
except Exception, error_message:
    logging.error('Transmission error: ' + format(error_message))
    exit()

def mysql(query, type=None):
    try:
        request.execute(query)
    except Exception, error_message:
        logging.error('MySQL error ' + format(error_message[0]) + ': ' + format(error_message[1]))
        exit()
    if type == 'all':
        return request.fetchall()
    if type == 'one':
        return request.fetchone()
    if type == None:
        return None

