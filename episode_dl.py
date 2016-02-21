#!/usr/bin/env python
# -*- coding: utf-8 -*-

from lxml.html import parse
import urllib2
import os
import logging
import sys
import bencode
import base64
import subprocess
import initial
from initial import mysql
from initial import tc

if len (sys.argv) > 1:
    ID_EPISODE = sys.argv[1]
else:
    logging.error('Не указан параметр')
    exit()

result_episode = mysql("SELECT * FROM Episodes WHERE ID = '%(id)s'"%{"id":ID_EPISODE}, 'one')
result_serial= mysql("SELECT * FROM Serials WHERE ID = '%(id)s'"%{"id":result_episode[1]}, 'one')
url_dl = 'http://www.lostfilm.tv/nrdr2.php?c={0}&s={1}&e={2}'.format(result_episode[1],result_episode[4],result_episode[5])
opener = urllib2.build_opener()
opener.addheaders.append(initial.COOKIE)
result = opener.open(url_dl)
page = parse(result).getroot()
hrefs = page.cssselect("a")[0].get('href')
page = parse(hrefs).getroot()
hrefs = page.cssselect("table div a")
TMP = 0
torrent_quality = ""
torrent_href = ""
quality_lf = {}
result_quality = mysql("SELECT * FROM Quality", 'all')
for i in result_quality:
    quality_lf[i[1]] = mysql("SELECT * FROM Quality_LF WHERE Quality = {0}".format(i[0]), 'all')
write = True
for i in hrefs:
    response = urllib2.urlopen(i.get('href'))
    torrent = response.read()
    for k in result_quality:
        for j in quality_lf[k[1]]:
            if bencode.bdecode(torrent)['info']['name'].lower().replace(' ','').find(j[1].lower()) != -1 :
                quality_lf_test = mysql("SELECT * FROM Quality_LF", 'all')
                for m in quality_lf_test:
                    if m[1] != j[1]:
                        if bencode.bdecode(torrent)['info']['name'].lower().replace(' ','').find(m[1].lower()) == -1:
                            write = True
                        else:
                            write = False
                    else:
                        write = True
                if TMP < k[0] and write:
                    TMP = k[0]
                    torrent_quality = torrent

if result_episode[5] != '99':
    download_path = format(initial.config.get('transmission', 'download_dir')).decode('utf-8') + '/' + format(result_serial[2]).replace('(','').replace(')','').strip() + '/' + format(result_serial[2]).replace('(','').replace(')','').strip() + ' ' + format(result_episode[4])
else:
    download_path = format(initial.config.get('transmission', 'download_dir')).decode('utf-8') + '/' + format(result_serial[2]).replace('(','').replace(')','').strip()
    episodes_data = mysql("SELECT * FROM Episodes WHERE Serial = '%(serial)s' AND Season = '%(season)s' AND Episode != 99 AND File != 0" % {"serial": format(result_episode[1]), "season": format(result_episode[4])}, 'all')
    for e in episodes_data:
        result_file = mysql("SELECT * FROM Files WHERE ID = '%(id)s'"%{"id":e[8]}, 'one')
        if result_file[3] != 0:
            tc.stop_torrent(result_file[3])
            if result_file[4] == 0:
                tc.remove_torrent(result_file[3])
                mysql("INSERT INTO DeleteFile SET Path = '%(path)s', ID_File = '%(id_file)s'"%{"path":result_file[2] + '/' + result_file[1], "id_file":result_file[0]}, None)
            else:
                tc.remove_torrent(result_file[3], delete_data=True)
        else:
            mysql("INSERT INTO DeleteFile SET Path = '%(path)s', ID_File = '%(id_file)s'"%{"path":result_file[2] + '/' + result_file[1], "id_file":result_file[0]}, None)
        mysql("UPDATE Episodes SET File = '%(file)s', Quality = '%(quality)s' WHERE ID = '%(id)s'"%{"file": '0', "quality": '0', "id":e[0]}, None)
        mysql("DELETE FROM Files WHERE ID = '%(id)s'"%{"id": result_file[0]}, None)

if result_episode[8] == 0:
    logging.info("Качаем " + download_path.encode('utf-8') + "/" + bencode.bdecode(torrent_quality)['info']['name'])
    id_torrent = tc.add_torrent(base64.b64encode(torrent_quality), download_dir=download_path.encode('utf-8')).id
    serial_path = format(initial.config.get('transmission', 'download_dir')).decode('utf-8') + '/' + format(result_serial[2]).replace('(','').replace(')','').strip()
    if not os.path.exists(serial_path.encode('utf-8')):
        os.makedirs(serial_path.encode('utf-8'))
        subprocess.Popen([initial.ROOT_PATH + '/thetvdb.py ' + result_serial[0]], shell=True)
    mysql("INSERT INTO Files SET Name = '%(name)s', Path_DL = '%(path_dl)s', ID_TORRENT = '%(id_torrent)s', DL = 1"%{"name":bencode.bdecode(torrent_quality)['info']['name'], "path_dl":download_path, "id_torrent":id_torrent}, None)
    result_file = mysql("SELECT * FROM Files WHERE Name = '%(name)s'"%{"name":bencode.bdecode(torrent_quality)['info']['name']}, 'one')
    mysql("UPDATE Episodes SET File = '%(file)s', Quality = '%(quality)s' WHERE ID = '%(id)s'"%{"file":result_file[0], "quality":TMP, "id":ID_EPISODE}, None)
else:
    result_file = mysql("SELECT * FROM Files WHERE ID = '%(id)s'"%{"id":result_episode[8]}, 'one')
    fails = 0 
    try:
	    tc.get_torrent(result_file[3]).percentDone
    except Exception, error_message:
	    fails = 1
    if download_path != result_file[2] or TMP > result_episode[6] or ( not os.path.exists(download_path.encode('utf-8') + "/" + bencode.bdecode(torrent_quality)['info']['name']) and ( result_file[4] != 1 or fails == 1 )):
        logging.info("Качаем " + download_path.encode('utf-8') + "/" + bencode.bdecode(torrent_quality)['info']['name'])
        if result_file[3] != 0:
            tc.stop_torrent(result_file[3])
            if result_file[4] == 0:
                tc.remove_torrent(result_file[3])
                mysql("INSERT INTO DeleteFile SET Path = '%(path)s', ID_File = '%(id_file)s'"%{"path":result_file[2] + '/' + result_file[1], "id_file":result_file[0]}, None)
            else:
                tc.remove_torrent(result_file[3], delete_data=True)
        else:
            mysql("INSERT INTO DeleteFile SET Path = '%(path)s', ID_File = '%(id_file)s'"%{"path":result_file[2] + '/' + result_file[1], "id_file":result_file[0]}, None)
        id_torrent = tc.add_torrent(base64.b64encode(torrent_quality), download_dir=download_path.encode('utf-8')).id
        mysql("UPDATE Files SET Name = '%(name)s', Path_DL = '%(path_dl)s', ID_TORRENT = '%(id_torrent)s', DL = 1 WHERE ID = '%(id)s'"%{"name":bencode.bdecode(torrent_quality)['info']['name'], "path_dl":download_path, "id_torrent":id_torrent, "id":result_file[0]}, None)
        mysql("UPDATE Episodes SET File = '%(file)s', Quality = '%(quality)s' WHERE ID = '%(id)s'"%{"file":result_file[0], "quality":TMP, "id":ID_EPISODE}, None)


initial.db.commit()
initial.db.close()
