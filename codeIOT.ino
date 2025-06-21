  #include <DHT.h>
#include <ESP8266WiFi.h>
#include <WiFiClientSecure.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>

// ========== PINS ==========
#define TRIG_PIN D6
#define ECHO_PIN D5
#define SOUND_VELOCITY 0.034

#define DHT_PIN  D7
#define DHT_TYPE DHT22
DHT dht(DHT_PIN, DHT_TYPE);

const int relayPins[4] = {D1, D2, D3, D4};
bool relayStates[4] = {false, false, false, false};
// D1: Heater | D2: Cooler | D3: Humidifier | D4: Dehumidifier
bool autoMode = true;

// ========== WIFI & MQTT ==========
#define WIFI_SSID "Bintang"
#define WIFI_PASSWORD "belitungrayaa"
#define MQTT_USERNAME "fanaqt"
#define MQTT_PASSWORD "Fana12345"
#define MQTT_BROKER "7d677df85f0a4b2ca584c9e9c7f3a1de.s1.eu.hivemq.cloud"
#define MQTT_PORT 8883
#define PUB_TOPIK "sk"
#define SUB_TOPIK "r"

WiFiClientSecure espClient;
PubSubClient mqttClient(espClient);

// ========== SETUP ==========
void setup() {
  Serial.begin(115200);
  dht.begin();
  pinMode(TRIG_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);
  for (int i = 0; i < 4; i++) {
    pinMode(relayPins[i], OUTPUT);
    digitalWrite(relayPins[i], HIGH); // OFF
  }

  connectToWiFi();
  mqttClient.setCallback(mqttCallback);
  connectToMQTT();
}

// ========== LOOP ==========
void loop() {
  if (WiFi.status() != WL_CONNECTED) connectToWiFi();
  if (!mqttClient.connected()) connectToMQTT();
  mqttClient.loop();

  static unsigned long lastRead = 0;
  if (millis() - lastRead >= 2500) {
    lastRead = millis();
    fuzzyControlAndPublish();
  }
}

// ========== FUZZY MEMBERSHIP FUNCTIONS ==========
float fuzzyReverseGrade(float x, float a, float b) {
  if (x <= a) return 1;
  else if (x >= b) return 0;
  else return (b - x) / (b - a);
}
float fuzzyGrade(float x, float a, float b) {
  if (x <= a) return 0;
  else if (x >= b) return 1;
  else return (x - a) / (b - a);
}
float fuzzyTriangle(float x, float a, float b, float c) {
  if (x <= a || x >= c) return 0;
  else if (x == b) return 1;
  else if (x < b) return (x - a) / (b - a);
  else return (c - x) / (c - b);
}

// ========== SENSOR FUNCTIONS ==========
float getHumidity() { return dht.readHumidity(); }
float getTemperature() { return dht.readTemperature(); }
float getDistance() {
  digitalWrite(TRIG_PIN, LOW);
  delayMicroseconds(2);
  digitalWrite(TRIG_PIN, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIG_PIN, LOW);
  long duration = pulseIn(ECHO_PIN, HIGH, 30000);
  return duration * SOUND_VELOCITY / 2;
}

// ========== FUZZY LOGIC & PUBLISH ==========
void fuzzyControlAndPublish() {
  float h = getHumidity();
  float t = getTemperature();
  float j = getDistance();

  if (isnan(h) || isnan(t)) {
    Serial.println("Failed to read DHT");
    return;
  }

  // Fuzzy temperature
  float cold = fuzzyReverseGrade(t, 16, 18);
  float normalTemp = fuzzyTriangle(t, 17, 20, 23);
  float hot = fuzzyGrade(t, 21, 23);
  String tempState = "unknown";
  float maxTemp = max(cold, max(normalTemp, hot));
  if (maxTemp == cold) tempState = "cold";
  else if (maxTemp == normalTemp) tempState = "normal";
  else if (maxTemp == hot) tempState = "hot";

  // Fuzzy humidity
  float dry = fuzzyReverseGrade(h, 55, 60);
  float normalHum = fuzzyTriangle(h, 58, 65, 72);
  float wet = fuzzyGrade(h, 70, 75);
  String humState = "unknown";
  float maxHum = max(dry, max(normalHum, wet));
  if (maxHum == dry) humState = "dry";
  else if (maxHum == normalHum) humState = "normal";
  else if (maxHum == wet) humState = "wet";

  // Auto control
  if (autoMode) {
    // Heater
    digitalWrite(relayPins[0], (tempState == "cold") ? LOW : HIGH);
    relayStates[0] = (tempState == "cold");

    // Cooler
    digitalWrite(relayPins[1], (tempState == "hot") ? LOW : HIGH);
    relayStates[1] = (tempState == "hot");

    // Humidifier
    digitalWrite(relayPins[2], (humState == "dry") ? LOW : HIGH);
    relayStates[2] = (humState == "dry");

    // Dehumidifier
    digitalWrite(relayPins[3], (humState == "wet") ? LOW : HIGH);
    relayStates[3] = (humState == "wet");
  }

  // Publish
  StaticJsonDocument<256> doc;
  doc["s"] = t;
  doc["k"] = h;
  doc["j"] = j;
  doc["r1"] = relayStates[0];
  doc["r2"] = relayStates[1];
  doc["r3"] = relayStates[2];
  doc["r4"] = relayStates[3];
  doc["auto"] = autoMode ? 1 : 0;
  char buffer[256];
  serializeJson(doc, buffer);
  mqttClient.publish(PUB_TOPIK, buffer);
  Serial.println("Data sent: " + String(buffer));
}

// ========== WIFI ==========
void connectToWiFi() {
  Serial.println("Connecting to WiFi...");
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  WiFi.setAutoReconnect(true);
  WiFi.persistent(true);
  int retry = 0;
  while (WiFi.status() != WL_CONNECTED && retry++ < 20) {
    delay(500);
    Serial.print(".");
  }
  Serial.println(WiFi.status() == WL_CONNECTED ? "\nWiFi connected!" : "\nWiFi failed.");
}

// ========== MQTT ==========
void connectToMQTT() {
  espClient.setInsecure();
  mqttClient.setServer(MQTT_BROKER, MQTT_PORT);
  while (!mqttClient.connected()) {
    Serial.print("Connecting to MQTT...");
    if (mqttClient.connect("MasFanaIOT", MQTT_USERNAME, MQTT_PASSWORD)) {
      Serial.println("connected.");
      mqttClient.subscribe(SUB_TOPIK);
    } else {
      Serial.print("failed, rc=");
      Serial.print(mqttClient.state());
      Serial.println(" retrying in 3s...");
      delay(3000);
    }
  }
}

// ========== HANDLE MQTT MESSAGE ==========
void mqttCallback(char* topic, byte* payload, unsigned int length) {
  Serial.print("Received [");
  Serial.print(topic);
  Serial.print("]: ");
  payload[length] = '\0';
  String message = String((char*)payload);
  Serial.println(message);

  if (message[0] == 'r') {
    fuzzyControlAndPublish();
    return;
  }

  StaticJsonDocument<100> doc;
  DeserializationError error = deserializeJson(doc, payload);
  if (error) {
    Serial.print("JSON Error: ");
    Serial.println(error.c_str());
    return;
  }

  if (doc.containsKey("auto")) {
    autoMode = doc["auto"] == 1;
    Serial.println("Auto mode: " + String(autoMode ? "ON" : "OFF"));
    fuzzyControlAndPublish();
    return;
  }

  int relayNum = doc["r"];
  int state = doc["s"];
  if (!autoMode && relayNum >= 1 && relayNum <= 4 && (state == 0 || state == 1)) {
    int index = relayNum - 1;
    digitalWrite(relayPins[index], (state == 1) ? LOW : HIGH);
    relayStates[index] = (state == 1);
    Serial.println("Relay " + String(relayNum) + " set to " + state);
    fuzzyControlAndPublish();
  } else {
    Serial.println("Invalid message or auto mode ON");
  }
}
