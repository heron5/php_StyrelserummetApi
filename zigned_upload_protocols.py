#!/usr/bin/python3


from datetime import datetime, timezone
import json
import requests
from requests.auth import HTTPBasicAuth
from email.utils import parsedate_to_datetime
from logtail import logMessage

# x-zigned-api-key TEST
#zkey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6ImNsY2oxNG5hczExMzIxdTByaXdjM3dobGMiLCJpYXQiOjE2NzI5MTk1ODd9.-AOWvPT3YfKF1G7eabORZbY8Fyvp1h-26p-VQrA7O5I"

# x-zigned-api-key PROD
zkey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6ImNsZDh3b2tlZzAxMTkxc3V4bG80Ym14aDUiLCJpYXQiOjE2NzQ0ODQyMzl9.bf4drgxqr386fJo7HfFE1E-k7Nz7eE8M8Gnbj6EB51U"

user='so2api'
password='Vk4gHePa7SwnBtCq'

def getMeetings():

    url = 'http://192.168.2.205:82/datamodel.php?model=MeetingsForEsignDocUpload'

    response = requests.get(url, auth=(user, password))

    data = json.loads(response.text)

    print (len(data))

    for meeting in data:
       meeting_id = meeting["meeting_id"]
       zigned_id = meeting["zigned_id"]
       print (meeting_id)
       getFiles(meeting_id, zigned_id)

def getFiles(meeting_id, zigned_id):

    url = 'http://192.168.2.205:82/datamodel.php?model=FilesProtocols&meeting_id='+meeting_id

    response = requests.get(url, auth=(user, password))

    data = json.loads(response.text)

    for file in data:
       file_name = file["file_name"]
       file_part = file["file_part"]
       print (file_name)
       print (file_part)
       print (zigned_id)
       getPDF(file_name, file_part, zigned_id, meeting_id)

def getPDF(file_name, file_part, zigned_id, meeting_id):

   logMessage("INFO","zigned_upload_protocols.py","Uploading file to Zigned. ("+file_part+", "+meeting_id+")")
   url = "https://api.zigned.io/rest/v2/agreements/"+zigned_id+"/file"

   payload={}
   files=[
#     ('file',("'" + file_part + "'",open("../../broomdocs/" + file_name,'rb'),'application/pdf'))
      ('file',(file_part ,open("../broomdocs/" + file_name,'rb'),'application/pdf'))
    ]
   headers = {
     'x-zigned-api-key': zkey
   }

   response = requests.request("POST", url, headers=headers, data=payload, files=files)
   print (response.status_code)
   if response.status_code == 201:
         logMessage("INFO","1_zigned_upload_protocols.py",response.text)
   else:
         logMessage("ERROR","2_zigned_upload_protocols.py",response.text)

   data = json.loads(response.text)
   doc_id = data["original_document"]["id"]
   doc_check = data["original_document"]["compatibility_check"]
   doc_updated = data["original_document"]["updated_at"]
   status = data["status"]
   updated = data["updated_at"]


   url = "http://192.168.2.205:82/zigned_controller.php"


   payload = {}
   payload['UpdateDoc'] = {
             'MeetingId': meeting_id,
             'ZignedStatus': status,
             'ZignedUpdated': updated,
             'ZignedDocId': doc_id,
             'ZignedDocCheck': doc_check,
             'ZignedDocUpdated': doc_updated
        }

   headers = {
     'api-key': '2fa5c83f005056010a35'
   }
   logMessage("INFO","zigned_upload_protocols.py","Updating document info in Styrelserummet ("+file_part+", "+meeting_id+")")
   response = requests.request("POST", url, headers=headers, data=json.dumps(payload), auth=(user, password))
   print (response.status_code)
   if response:
         logMessage("INFO","3_zigned_upload_protocols.py",response.text)
   else:
         logMessage("ERROR","4_zigned_upload_protocols.py",response.text)





   f = open("upload_logg.txt", "a", encoding='utf-8')
   f.write(response.text)
   f.write("\n")
   f.close()
if __name__ == '__main__':

    getMeetings()
