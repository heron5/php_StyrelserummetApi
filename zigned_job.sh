#!/usr/bin/bash
echo "Checking/updating Zigned for Styrelserummet..."
cd /var/www/styrelserummet_api
echo "Trigger meetings fetcher..."
./zigned_trigger_fetch_meetings.py
sleep 10
echo "Trigger protocol upload..."
./zigned_upload_protocols.py
sleep 10
echo "Trigger user fetcher..."
./zigned_trigger_fetch_users.py
sleep 10
echo "Trigger status checker..."
./zigned_check_pending_meetings.py
echo "Checking/updating Zigned for Styrelserummet - COMPLETED"
