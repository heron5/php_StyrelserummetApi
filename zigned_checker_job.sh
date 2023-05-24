#!/usr/bin/bash
echo "Checking/updating Zigned for Styrelserummet..."
cd /var/www/styrelserummet_api
echo "Trigger status checker..."
./zigned_check_pending_meetings.py
echo "Checking/updating Zigned for Styrelserummet - COMPLETED"
