#!/usr/bin/env python
# -*- coding: utf-8 -*-

import initial
import os
import logging
import subprocess
from initial import mysql
from initial import tc
# Проверяем обновление серий и скачиваем их

serials_data = mysql("SELECT * FROM Serials WHERE DL = 1", 'all')
for i in serials_data:
    season_data = mysql("SELECT DISTINCT Season from Episodes where Serial = '%(serial)s' ORDER BY Season ASC" % {"serial": i[0]}, 'all')
    for s in season_data:
        episode99_data = mysql("SELECT * from Episodes where Serial = '%(serial)s' AND Season = '%(season)s' AND Episode = '99'" % {"serial": i[0], "season": s[0]}, 'one')
        if episode99_data:
            subprocess.Popen([initial.ROOT_PATH + '/episode_dl.py ' + format(episode99_data[0])], shell=True)
        else:
            episode_data = mysql("SELECT * from Episodes where Serial = '%(serial)s' AND Season = '%(season)s'" % {"serial": i[0], "season": s[0]}, 'all')
            for e in episode_data:
                subprocess.Popen([initial.ROOT_PATH + '/episode_dl.py ' + format(e[0])], shell=True)
serials_data = mysql("SELECT * FROM Serials WHERE DL = 0", 'all')
for i in serials_data:
    episode_data = mysql("SELECT * from Episodes where Serial = '%(serial)s' AND DL = 1" % {"serial": i[0]}, 'all')
    for e in episode_data:
        subprocess.Popen([initial.ROOT_PATH + '/episode_dl.py ' + format(e[0])], shell=True)

# Удаляем помеченные на удаление файлы
delete_data = mysql("SELECT * FROM DeleteFile", 'all')
for d in delete_data:
    file_data = mysql("SELECT * FROM Files WHERE ID = '%(id)s'" %{"id": d[2]}, 'one')
    if not file_data or file_data[4] == 0:
        try:
            os.remove(d[1])
        except:
            logging.error('Не найден файл ' + format(d[1].encode('UTF-8')))
        mysql("DELETE FROM DeleteFile WHERE ID = '%(id)s'" %{"id": d[0]}, None)

#Чистим БД
files_data = mysql("SELECT * FROM Files", 'all')
for f in files_data:
    episode_data = mysql("SELECT * FROM Episodes WHERE File = '%(file)s'" % {"file": f[0]}, 'one')
    serial_data = mysql("SELECT * FROM Serials WHERE  ID = '%(id)s'" % {"id": episode_data[1]}, 'one')
    if not episode_data or (serial_data[3] == 0 and episode_data[7] == 0 and f[4] == 0 and not os.path.exists(format(f[2]) + "/" + format(f[1]))):
        if f[3] != 0:
            tc.stop_torrent(f[3])
            tc.remove_torrent(f[3])
        mysql("DELETE FROM Files WHERE ID = '%(id)s'" % {"id": f[0]}, None)
episodes_data = mysql("SELECT * FROM Episodes WHERE File != 0", 'all')
for e in episodes_data:
    if not mysql("SELECT * FROM Files WHERE ID = '%(id)s'" % {"id": e[8]}, 'one'):
        mysql("UPDATE Episodes SET File = '%(file)s', Quality = '%(quality)s' WHERE ID = '%(id)s'"%{"file": '0', "quality": '0', "id": e[0]})
initial.db.commit()
initial.db.close()

