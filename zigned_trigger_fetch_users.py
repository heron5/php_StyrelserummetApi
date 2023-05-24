#!/usr/bin/python3


from datetime import datetime, timezone
import json
import requests
from requests.auth import HTTPBasicAuth
from email.utils import parsedate_to_datetime
from logtail import logMessage

# URL for TEST
#url1 = "https://prod-04.northeurope.logic.azure.com:443/workflows/8a3cb82bb51543afb6158febf4157808/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=Uyxyx2crNVfpL9cL-wpWxnMQbEh6O9ourvI-80RAb58"

# URL for PROD
url1 = "https://prod-25.northeurope.logic.azure.com:443/workflows/6176eae5243141538eb1fa77bb0bf502/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=tmq5A5ncaXarZ5lvIMBukBcdLVzBolc6hKH2FolA4Qo"

user='so2api'
password='Vk4gHePa7SwnBtCq'

def getMeetings():

   url = 'http://192.168.2.205:82/datamodel.php?model=MeetingsForEsigners'

   response = requests.get(url, auth=(user, password))

   data = json.loads(response.text)
   counter = 0

   for meeting in data:
       if  int( meeting["counter"]) == 0:
           counter = counter + 1
   print ( counter)

   if counter > 0:
      logMessage("INFO","zigned_trigger_fetch_users.py","Trigger logic app to fetch users for meetings.")
      # Using url1
      response = requests.request("POST", url1)
      if response.status_code == 200:
         logMessage("INFO","zigned_trigger_fetch_users.py",response.text)
      else:
         logMessage("ERROR","zigned_trigger_fetch_users.py",response.text)

if __name__ == '__main__':

    getMeetings()
