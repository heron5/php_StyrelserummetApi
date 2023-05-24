#!/usr/bin/python3


from datetime import datetime, timezone
import json
import requests
from requests.auth import HTTPBasicAuth
from email.utils import parsedate_to_datetime
from logtail import logMessage

# URL for TEST
#url1 = "https://prod-55.northeurope.logic.azure.com:443/workflows/de3ec3dd6613474e85584b63e6f53367/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=vzoKN5SI5DKgAwuB6TLMJFetO7aCXzS-bLKg3xwxypE"

# URL for PROD
url1 = "https://prod-33.northeurope.logic.azure.com:443/workflows/d6e35772702542289e29ccd24098f214/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=B7SNS-lVt-BgbWfMeKYupFO188tfx24cSslBS_36pQE"

user='so2api'
password='Vk4gHePa7SwnBtCq'

def getMeetings():

   url = 'http://192.168.2.205:82/datamodel.php?model=MeetingsPendingForEsign'

   response = requests.get(url, auth=(user, password))

   data = json.loads(response.text)
   print ( len(data))

   if len(data) > 0:
      logMessage("INFO","zigned_check_pending_meetings.py","Trigger logic app to check pending meetings.")
      # Using url1
      response = requests.request("POST", url1)
      if response.status_code == 200:
         logMessage("INFO","zigned_check_pending_meetings.py",response.text)
      else:
         logMessage("ERROR","zigned_check_pending_meetings.py",response.text)

if __name__ == '__main__':

    getMeetings()
