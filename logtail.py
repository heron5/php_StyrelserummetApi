#!/usr/bin/python3


from datetime import datetime, timezone
import json
import requests
from requests.auth import HTTPBasicAuth
from email.utils import parsedate_to_datetime


def logMessage(level, app, message):
   now = datetime.now() # current date and time

   date_time = now.strftime("%Y-%m-%d %H:%M:%S +0100")

#   print (date_time)
   url = "https://in.logtail.com/"

   payload={"dt":date_time,"level":level,"app":app,"message":message}
#   print (payload)
   headers = {
     'Content-Type': 'application/json',
     'Authorization': 'Bearer kiXyuTMDKGVA5axjd5qnc4R7'
   }

   response = requests.request("POST", url, headers=headers, data=json.dumps(payload))

#   print (response)
#   data = json.loads(response.text)


if __name__ == '__main__':

    logMessage("INFO", "logtail", "Testmessage")
