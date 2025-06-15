import ssl
import time
import json
import random
import threading
import paho.mqtt.client as mqtt

# ====== CONFIGURATION ======
MQTT_BROKER = "7d677df85f0a4b2ca584c9e9c7f3a1de.s1.eu.hivemq.cloud"
MQTT_PORT = 8883
MQTT_USERNAME = "fanaqt"
MQTT_PASSWORD = "Fana12345"
PUB_TOPIC = "sk"
SUB_TOPIC = "r"
CLIENT_ID = "MasFanaPythonClient"

# ====== GLOBAL STATES ======
auto_mode = True
relay_states = [False, False, False, False]  # Heater, Cooler, Humidifier, Dehumidifier

# ====== FUZZY FUNCTIONS ======
def fuzzy_reverse_grade(x, a, b):
    if x <= a: return 1
    elif x >= b: return 0
    return (b - x) / (b - a)

def fuzzy_grade(x, a, b):
    if x <= a: return 0
    elif x >= b: return 1
    return (x - a) / (b - a)

def fuzzy_triangle(x, a, b, c):
    if x <= a or x >= c: return 0
    elif x == b: return 1
    elif x < b: return (x - a) / (b - a)
    return (c - x) / (c - b)

# ====== SENSOR SIMULATION ======
def get_temperature(): return round(random.uniform(16, 28), 2)
def get_humidity(): return round(random.uniform(50, 80), 2)
def get_distance(): return round(random.uniform(10, 100), 2)

# ====== MQTT CALLBACKS ======
def on_connect(client, userdata, flags, rc):
    print("Connected with result code:", rc)
    client.subscribe(SUB_TOPIC)

def on_message(client, userdata, msg):
    global auto_mode
    payload = msg.payload.decode()
    print(f"[MQTT] Message received: {payload}")

    if payload.startswith('r'):
        fuzzy_control_and_publish()
        return

    try:
        data = json.loads(payload)
        if "auto" in data:
            auto_mode = bool(data["auto"])
            print("Auto mode set to:", auto_mode)
            fuzzy_control_and_publish()
        elif not auto_mode and "r" in data and "s" in data:
            r = int(data["r"])
            s = int(data["s"])
            if 1 <= r <= 4 and s in [0, 1]:
                relay_states[r - 1] = bool(s)
                print(f"Relay {r} set to: {'ON' if s else 'OFF'} (manual)")
    except Exception as e:
        print("Invalid payload:", e)

# ====== FUZZY LOGIC AND PUBLISHING ======
def fuzzy_control_and_publish():
    global relay_states
    t = get_temperature()
    h = get_humidity()
    d = get_distance()

    # Fuzzy temperature
    cold = fuzzy_reverse_grade(t, 16, 18)
    normal_temp = fuzzy_triangle(t, 17, 20, 23)
    hot = fuzzy_grade(t, 21, 23)
    temp_state = max([(cold, 'cold'), (normal_temp, 'normal'), (hot, 'hot')], key=lambda x: x[0])[1]

    # Fuzzy humidity
    dry = fuzzy_reverse_grade(h, 55, 60)
    normal_hum = fuzzy_triangle(h, 58, 65, 72)
    wet = fuzzy_grade(h, 70, 75)
    hum_state = max([(dry, 'dry'), (normal_hum, 'normal'), (wet, 'wet')], key=lambda x: x[0])[1]

    if auto_mode:
        relay_states[0] = (temp_state == "cold")     # Heater
        relay_states[1] = (temp_state == "hot")      # Cooler
        relay_states[2] = (hum_state == "dry")       # Humidifier
        relay_states[3] = (hum_state == "wet")       # Dehumidifier

    payload = {
        "s": t,
        "k": h,
        "j": d,
        "r1": relay_states[0],
        "r2": relay_states[1],
        "r3": relay_states[2],
        "r4": relay_states[3],
        "auto": 1 if auto_mode else 0
    }

    mqtt_client.publish(PUB_TOPIC, json.dumps(payload))
    print("[MQTT] Published:", payload)

# ====== MQTT CLIENT SETUP ======
mqtt_client = mqtt.Client(client_id=CLIENT_ID)
mqtt_client.username_pw_set(MQTT_USERNAME, MQTT_PASSWORD)
mqtt_client.tls_set(cert_reqs=ssl.CERT_NONE)
mqtt_client.tls_insecure_set(True)
mqtt_client.on_connect = on_connect
mqtt_client.on_message = on_message
mqtt_client.connect(MQTT_BROKER, MQTT_PORT)

# ====== MAIN LOOP ======
def start_loop():
    mqtt_client.loop_start()
    while True:
        fuzzy_control_and_publish()
        time.sleep(2.5)

if __name__ == "__main__":
    try:
        start_loop()
    except KeyboardInterrupt:
        print("Exiting...")
        mqtt_client.loop_stop()
        mqtt_client.disconnect()
