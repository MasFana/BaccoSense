import asyncio
import websockets
import serial
import serial.tools.list_ports
import json
from typing import Set, Optional

# Constants
BAUD_RATE = 115200
WEBSOCKET_HOST = "0.0.0.0"
WEBSOCKET_PORT = 8765

class ESPWebSocketBridge:
    
    def __init__(self):
        self.connected_clients: Set[websockets.WebSocketServerProtocol] = set()
        self.serial_conn: Optional[serial.Serial] = None

    def find_esp_port(self) -> Optional[str]:
        """Auto-detect ESP8266 serial port"""
        ports = serial.tools.list_ports.comports()
        for port in ports:
            if port.description and any(
                keyword in port.description.lower() 
                for keyword in ["ch340", "usb", "esp", "cp210"]
            ):
                print(f"🔍 Found potential ESP8266 on {port.device}")
                return port.device
        return None

    async def initialize_serial(self) -> bool:
        """Initialize serial connection"""
        port = self.find_esp_port()
        if not port:
            print("❌ ESP8266 not found. Please check connection and try again.")
            return False
        
        try:
            self.serial_conn = serial.Serial(port, BAUD_RATE, timeout=1)
            print(f"✅ Connected to ESP8266 on {port}")
            return True
        except serial.SerialException as e:
            print(f"❌ Failed to connect to {port}: {e}")
            return False

    async def serial_reader(self):
        """Read data from serial and broadcast to WebSocket clients"""
        while True:
            try:
                if self.serial_conn and self.serial_conn.in_waiting:
                    line = self.serial_conn.readline().decode('utf-8').strip()
                    if line:  # Only process non-empty lines
                        print(f"📡 From ESP8266: {line}")
                        await self.broadcast(line)
            except UnicodeDecodeError:
                print("⚠️ Failed to decode serial data (invalid UTF-8)")
            except serial.SerialException as e:
                print(f"⚠️ Serial communication error: {e}")
                await asyncio.sleep(1)  # Wait before retrying
            except Exception as e:
                print(f"⚠️ Unexpected error in serial reader: {e}")
            
            await asyncio.sleep(0.01)

    async def broadcast(self, message: str):
        """Broadcast message to all connected WebSocket clients"""
        if not self.connected_clients:
            return
            
        try:
            message_data = json.dumps({"message": message})
            await asyncio.gather(*[client.send(message_data) for client in self.connected_clients])
            print(f"📤 Broadcasted to {len(self.connected_clients)} clients: {message}")
        except Exception as e:
            print(f"⚠️ Failed to broadcast message: {e}")

    async def websocket_handler(self, websocket):
        """Handle WebSocket connections"""
        self.connected_clients.add(websocket)
        print(f"🌐 New client connected (Total: {len(self.connected_clients)})")
        
        try:
            async for message in websocket:
                print(f"💬 Client message: {message}")
                # Optional: Forward client messages to ESP8266
                if self.serial_conn and self.serial_conn.writable():
                    try:
                        self.serial_conn.write(f"{message}\n".encode())
                    except serial.SerialException as e:
                        print(f"⚠️ Failed to send to ESP8266: {e}")
                        
        except websockets.exceptions.ConnectionClosed:
            pass
        finally:
            self.connected_clients.discard(websocket)
            print(f"🌐 Client disconnected (Remaining: {len(self.connected_clients)})")

    async def run(self):
        """Main application loop"""
        if not await self.initialize_serial():
            return

        print(f"🌐 Starting WebSocket server on ws://{WEBSOCKET_HOST}:{WEBSOCKET_PORT}")
        async with websockets.serve(
            self.websocket_handler, 
            WEBSOCKET_HOST, 
            WEBSOCKET_PORT
        ):
            await self.serial_reader()

async def main():
    bridge = ESPWebSocketBridge()
    await bridge.run()

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\n🛑 Server stopped by user")
    except Exception as e:
        print(f"🔥 Critical error: {e}")
