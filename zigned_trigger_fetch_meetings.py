#!/usr/bin/python3


from datetime import datetime, timezone
import json
import requests
from requests.auth import HTTPBasicAuth
from email.utils import parsedate_to_datetime
from logtail import logMessage

# URL for TEST
# url1 = "https://prod-11.northeurope.logic.azure.com:443/workflows/0416553ba22c4e7c927281f50c37e097/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=Cnuiclwr2oavas1OXJ5aoyG4IFwWV-KbT8XnjfOws3E"

# URL for PROD
url1 = "https://prod-31.northeurope.logic.azure.com:443/workflows/9753f4248a344c78a856f946444bff32/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=6RzU9tGfwdep1c8edFSSZpVA6JjhjMJFDak-5g23nkg"

user='so2api'
password='Vk4gHePa7SwnBtCq'

def getMeetings():

   url = 'http://192.168.2.205:82/datamodel.php?model=MeetingsForEsign'

   response = requests.get(url, auth=(user, password))

   data = json.loads(response.text)

   print (len(data))

   if len(data) > 0:
      logMessage("INFO","zigned_trigger_fetch_meetings.py","Trigger logic app to fetch new meetings.")
      # Using url1
      response = requests.request("POST", url1)
      if response.status_code == 200:
         logMessage("INFO","zigned_trigger_fetch_meetings.py",response.text)
      else:
         logMessage("ERROR","zigned_trigger_fetch_meetings.py",response.text)

if __name__ == '__main__':

    getMeetings()
