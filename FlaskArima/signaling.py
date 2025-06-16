import paho.mqtt.client as mqtt
import json
import requests
from datetime import datetime
import time
import ssl

# MQTT config (with SSL)
MQTT_BROKER = "7d677df85f0a4b2ca584c9e9c7f3a1de.s1.eu.hivemq.cloud"
MQTT_PORT = 8883  # MQTT over TLS
MQTT_USERNAME = "fanaqt"
MQTT_PASSWORD = "Fana12345"
MQTT_TOPIC = "sk"
CLIENT_ID = "MasFanaPythonClientSignaling"
# POST target
POST_URL = "http://localhost:8000/api/add"

# Track last sent minute
last_sent_minute = -1

def on_connect(client, userdata, flags, rc):
    print("Connected with result code", rc)
    if rc == 0:
        client.subscribe(MQTT_TOPIC)
    else:
        print("Connection failed")

def on_message(client, userdata, msg):
    global last_sent_minute

    try:
        payload = msg.payload.decode()
        print("Received:", payload)
        data = json.loads(payload)

        suhu = float(data["s"])
        kelembaban = float(data["k"])

    except (ValueError, KeyError, json.JSONDecodeError) as e:
        print("Invalid payload:", e)
        return

    now = datetime.now()
    current_minute = now.minute

    if current_minute % 10 == 0 and current_minute != last_sent_minute:
        try:
            response = requests.post(POST_URL, data={
                "suhu": suhu,
                "kelembaban": kelembaban
            })
            print("Posted:", response.status_code, response.text)
            last_sent_minute = current_minute
        except Exception as e:
            print("Failed to POST:", e)

# MQTT client with TLS
client = mqtt.Client(CLIENT_ID)
client.username_pw_set(MQTT_USERNAME, MQTT_PASSWORD)

# Configure TLS
client.tls_set(tls_version=ssl.PROTOCOL_TLS)  # Enable TLS
client.tls_insecure_set(True)  # For testing only (disable hostname verification)

client.on_connect = on_connect
client.on_message = on_message

try:
    client.connect(MQTT_BROKER, MQTT_PORT, 60)
except Exception as e:
    print("Connection error:", e)
    exit(1)

client.loop_start()

try:
    while True:
        time.sleep(1)
except KeyboardInterrupt:
    client.loop_stop()
    client.disconnect()
    print("Disconnected.")