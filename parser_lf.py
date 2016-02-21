#!/usr/bin/env python
# -*- coding: utf-8 -*-

from lxml.html import parse
from lxml.html import tostring
import urllib2
import initial
from initial import mysql

LF='http://lostfilm.tv/serials.php'
opener = urllib2.build_opener()
opener.addheaders.append(initial.COOKIE)
result = opener.open(LF)
page = parse(result).getroot()
hrefs = page.cssselect("a.bb_a")

for i in hrefs:
        S_ID = i.get('href').split('=')[1].strip().replace("_", "")
        S_Name = i.text.strip().replace("'","")
        S_Name_ENG = i.cssselect('span')[0].text.strip().replace("'","")
        mysql("INSERT INTO Serials SET ID = '%(id)s' , Name = '%(name)s', Name_ENG = '%(name_eng)s' ON DUPLICATE KEY UPDATE Name = '%(name)s', Name_ENG  = '%(name_eng)s'"%{"id":S_ID, "name":S_Name, "name_eng":S_Name_ENG }, None)
        print S_Name
        serial='http://lostfilm.tv/browse.php?cat=' + S_ID
        result = opener.open(serial)
        page = parse(result).getroot()
        hrefs = page.cssselect('td.t_episode_title')
        for j in hrefs:
                E_Name = j.cssselect('span')[0].text.strip().replace("'","")
                E_Name_ENG = tostring(j.cssselect('div div nobr br')[0]).replace("<br>", "").replace("'","").strip() #[0].text
                E_Season = j.get("onclick").split(",")[1].replace("'","").strip()
                E_Episode = j.get("onclick").split(",")[2].replace("'","").replace(")","").strip()
                result = mysql("SELECT * FROM Episodes WHERE Serial = '%(id_serial)s' AND Season = '%(season)s' AND Episode = '%(episode)s'"%{"id_serial":S_ID, "season":E_Season, "episode":E_Episode}, 'one')
                if result == None:
                        mysql("INSERT INTO Episodes SET Serial = '%(id_serial)s', Name = '%(name)s', Name_ENG = '%(name_eng)s', Season = '%(season)s', Episode = '%(episode)s'"%{"id_serial":S_ID, "name":E_Name, "name_eng":E_Name_ENG, "season":E_Season, "episode":E_Episode}, None)
                        initial.db.commit()
                else:
                        mysql("UPDATE Episodes SET Name = '%(name)s', Name_ENG = '%(name_eng)s' WHERE ID = '%(id)s' AND Serial = '%(id_serial)s' AND Season = '%(season)s' AND Episode = '%(episode)s'"%{"id":result[0], "id_serial":S_ID, "name":E_Name, "name_eng":E_Name_ENG, "season":E_Season, "episode":E_Episode}, None)
initial.db.commit()
initial.db.close()
