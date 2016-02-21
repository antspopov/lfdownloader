#!/usr/bin/env python
# -*- coding: utf-8 -*-

import initial
from initial import mysql
from initial import tc
import logging

torrent_data = mysql("SELECT * FROM Files", 'all')
for i in torrent_data:
    if i[3] != 0:
        try:
            tc.get_torrent(i[3]).percentDone
        except Exception, error_message:
            logging.error('Transmission error: ' + format(error_message))
            mysql("UPDATE Files SET DL = '%(dl)s' WHERE ID = '%(id)s'" % {"dl": 0, "id": i[0]}, None)
            continue

        if i[4] == 1:
            if tc.get_torrent(i[3]).percentDone == 1:
                mysql("UPDATE Files SET DL = 0 WHERE ID = '%(id)s'" % {"id": i[0]}, None)
        else:
            if tc.get_torrent(i[3]).percentDone != 1:
                mysql("UPDATE Files SET DL = 1 WHERE ID = '%(id)s'" % {"id": i[0]}, None)
        if tc.get_torrent(i[3]).uploadRatio == 3:
            mysql("UPDATE Files SET ID_TORRENT = '%(id_torrent)s' WHERE ID = '%(id)s'" % {"id_torrent": 0, "id": i[0]}, None)
            tc.remove_torrent(i[3])
    else:
        if i[4] == 1:
            mysql("UPDATE Files SET DL = '%(dl)s' WHERE ID = '%(id)s'" % {"dl": 0, "id": i[0]}, None)

initial.db.commit()
initial.db.close()
