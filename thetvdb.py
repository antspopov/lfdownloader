#!/usr/bin/env python
# -*- coding: utf-8 -*-

from lxml.html import parse
import urllib2
import os
import logging
import sys
import bencode
import base64
import initial
from initial import mysql
from initial import tc

if len (sys.argv) > 1:
    ID_SERIAL = sys.argv[1]
else:
    logging.error('Не указаны параметры')
    exit()

result_serial= mysql("SELECT * FROM Serials WHERE ID = '%(id)s'"%{"id":ID_SERIAL}, 'one')
name = ""
url = ""
serial_name = result_serial[1].encode('utf-8')
#print serial_name
#print urllib2.quote(serial_name);
url_dl = 'https://thetvdb.com/index.php?seriesname=' + urllib2.quote(serial_name) + '&fieldlocation=4&language=22&genre=&year=&network=&zap2it_id=&tvcom_id=&imdb_id=&order=translation&addedBy=&searching=Search&tab=advancedsearch'
opener = urllib2.build_opener()
result = opener.open(url_dl)
page = parse(result).getroot()
hrefs = page.cssselect("td.odd")
for i in hrefs:
    test = i.cssselect("a")
    if len(test) != 0:
        for j in test:
            if j.text.encode('utf-8') != "":
                name = j.text.encode('utf-8')
                url = 'https://thetvdb.com' + j.get('href')

if name == "":
    serial_name = result_serial[2].replace('(','').replace(')','').encode('utf-8')
    url_dl = 'https://thetvdb.com/index.php?seriesname=' + urllib2.quote(serial_name) + '&fieldlocation=4&language=22&genre=&year=&network=&zap2it_id=&tvcom_id=&imdb_id=&order=translation&addedBy=&searching=Search&tab=advancedsearch'
    opener = urllib2.build_opener()
    result = opener.open(url_dl)
    page = parse(result).getroot()
    hrefs = page.cssselect("td.odd")
    for i in hrefs:
        test = i.cssselect("a")
        if len(test) != 0:
            for j in test:
                if j.text.encode('utf-8') != "":
                    name = j.text.encode('utf-8')
                    url = 'https://thetvdb.com' + j.get('href')

if name == "":
    serial_name = result_serial[2].replace('(','').replace(')','').encode('utf-8')
    url_dl = 'https://thetvdb.com/index.php?seriesname=' + urllib2.quote(serial_name) + '&fieldlocation=4&language=7&genre=&year=&network=&zap2it_id=&tvcom_id=&imdb_id=&order=translation&addedBy=&searching=Search&tab=advancedsearch'
    opener = urllib2.build_opener()
    result = opener.open(url_dl)
    page = parse(result).getroot()
    hrefs = page.cssselect("td.odd")
    for i in hrefs:
        test = i.cssselect("a")
        if len(test) != 0:
            for j in test:
                if j.text.encode('utf-8') != "":
                    name = j.text.encode('utf-8')
                    url = 'https://thetvdb.com' + j.get('href')

print result_serial[1].encode('utf-8') + ' ----> ' + name + ' == ' + url
serial_path = format(initial.config.get('transmission', 'download_dir')).decode('utf-8') + '/' + format(result_serial[2]).replace('(','').replace(')','').strip()
tvshow = open(serial_path.encode('utf-8') + '/tvshow.nfo', 'w')
tvshow.write(url)
tvshow.close()
