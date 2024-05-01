import paramiko
import sys

ssh = None
command = sys.argv[5]

def connect():
    username = sys.argv[1]
    hostname = sys.argv[2]
    password = sys.argv[3]
    port = sys.argv[4]

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(hostname=hostname, port=port, username=username, password=password)

def execute_command(command):
    stdin, stdout, stderr = ssh.exec_command()
    
    for line in stdout.read().splitlines():
        print(line)

if not ssh:
    connect()



ssh.close()