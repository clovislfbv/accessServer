import mysql.connector
from mysql.connector import Error
from dotenv import load_dotenv
from datetime import datetime
import os
import sys
import pytz

# Access command-line arguments
if len(sys.argv) != 3:
    print("Usage: python index.py <username> <server_ip>")
    sys.exit(1)

user = sys.argv[1]  # First parameter
server_ip = sys.argv[2]  # Second parameter

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

        def delete_files_on_time(username, server_ip):
            cursor = conn.cursor()
            result = None
            current = None

            france_tz = pytz.timezone('Europe/Paris')

            while result != []:
                if current is None:
                    query = "SELECT end_time FROM files WHERE user='" + username + "' AND server_ip='" + server_ip + "' ORDER BY end_time LIMIT 1"
                    cursor.execute(query)
                    result = cursor.fetchall()

                    if result != []:
                        current = result[0][0]
                    print(current)
                else:
                    if current.tzinfo is None:
                        current = france_tz.localize(current)  # Assuming current is in UTC+2

                    # Get the current time in France
                    now_in_france = datetime.now(france_tz)

                    # Compare the two timestamps
                    if current <= now_in_france:
                        query = "DELETE FROM files WHERE end_time <= NOW() AND user='" + username + "' AND server_ip='" + server_ip + "' ORDER BY end_time LIMIT 1"
                        cursor.execute(query)
                        current = None
                    else:
                        print(f"Current timestamp: {current}, Now in France: {now_in_france}")
        
        delete_files_on_time(user, server_ip)


except Error as e:
    print(f"Error: {e}")
    exit(1)