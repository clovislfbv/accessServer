import mysql.connector
from mysql.connector import Error
from dotenv import load_dotenv
from datetime import datetime
import os
import sys
import pytz
import requests
from time import sleep

# Access command-line arguments
if len(sys.argv) != 4:
    print("Usage: python index.py <username> <server_ip> <session_id>")
    sys.exit(1)

user = sys.argv[1]  # First parameter
server_ip = sys.argv[2]  # Second parameter
session_id = sys.argv[3]  # Third parameter

load_dotenv()

servername = "db"
username = os.getenv('username')
password = os.getenv('pswd')
dbname = os.getenv('db_name')

try:
    # Establish the connection
    conn = mysql.connector.connect(
        host=servername,
        user=username,
        password=password,
        database=dbname
    )

    if conn.is_connected():
        print("Connection successful")

        # Set up session for maintaining PHP session state
        session = requests.Session()
        
        # Headers to mimic the browser request (optional but recommended)
        headers = {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest',
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        }

        session.cookies.set('PHPSESSID', session_id)

        response = session.post(
            'http://localhost/php/helper.php', 
            data={"action": "get_python_status"},
            headers=headers
        )

        print("test : ", str(response.text))

        def delete_files_on_time(username, server_ip):
            cursor = conn.cursor()
            result = None
            current = None

            france_tz = pytz.timezone('Europe/Paris')

            while result != []:
                query = "SELECT end_time FROM files WHERE user='" + username + "' AND server_ip='" + server_ip + "' ORDER BY end_time LIMIT 1"
                cursor.execute(query)
                result = cursor.fetchall()

                if result != []:
                    current = result[0][0]
                print(current)
                
                if current is not None and current.tzinfo is None:
                    current = france_tz.localize(current)  # Assuming current is in UTC+2

                # Get the current time in France
                now_in_france = datetime.now(france_tz)

                # Compare the two timestamps
                if current is not None:
                    if current <= now_in_france:
                        response = session.post(
                            "http://localhost/php/helper.php",
                            data={"action": "remove_local_file"},
                            headers=headers
                        )

                        query = "DELETE FROM files WHERE end_time <= NOW() AND user='" + username + "' AND server_ip='" + server_ip + "' ORDER BY end_time LIMIT 1"
                        cursor.execute(query)
                        print(f"Rows deleted: {cursor.rowcount}")
                        current = None
                    else:
                        print(f"Current timestamp: {current}, Now in France: {now_in_france}")
                
                sleep(0.5)
        
        delete_files_on_time(user, server_ip)
        
        # If you have a specific PHP session ID, you can set it like this:
        # session.cookies.set('PHPSESSID', 'your_session_id_here')

        # Make the request to set python status
        response = session.post(
            'http://localhost/php/helper.php', 
            data={"action": "set_python_status", "status": "false"},
            headers=headers
        )

        if response.status_code == 200:
            print("Request was successful")
            print(f"Response: {response.text}")
        else:
            print(f"Request failed with status code: {response.status_code}")
            print(f"Response: {response.text}")


except Error as e:
    print(f"Error: {e}")
    exit(1)