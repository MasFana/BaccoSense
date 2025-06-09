import paho.mqtt.client as mqtt
import json
import requests
from datetime import datetime
import time

# MQTT config (non-SSL)
MQTT_BROKER = "7d677df85f0a4b2ca584c9e9c7f3a1de.s1.eu.hivemq.cloud"
MQTT_PORT = 1883  # Unencrypted MQTT
MQTT_USERNAME = "fanaqt"
MQTT_PASSWORD = "Fana12345"
MQTT_TOPIC = "sk"

# POST target
POST_URL = "http://localhost:8000/add"

# Track last sent minute
last_sent_minute = -1

def on_connect(client, userdata, flags, rc):
    print("Connected with result code", rc)
    client.subscribe(MQTT_TOPIC)

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
            print("Posted:", response.status_code)
            last_sent_minute = current_minute
        except Exception as e:
            print("Failed to POST:", e)

# MQTT client (non-TLS)
client = mqtt.Client()
client.username_pw_set(MQTT_USERNAME, MQTT_PASSWORD)

client.on_connect = on_connect
client.on_message = on_message

client.connect(MQTT_BROKER, MQTT_PORT, 60)

client.loop_start()

try:
    while True:
        time.sleep(1)
except KeyboardInterrupt:
    client.loop_stop()
    client.disconnect()
    print("Disconnected.")
