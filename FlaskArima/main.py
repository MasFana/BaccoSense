import json
import random
import websockets
import asyncio
from datetime import datetime

clients = set()

async def send_periodic_updates():
    while True:
        if clients:  # Only send if there are connected clients
            message = json.dumps({
                "timestamp": datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
                "type": "update",
                "data": {
                    "suhu": random.randint(15, 30),
                    "kelembapan": random.randint(40, 80),
                }
            })
            # Broadcast to all connected clients
            websockets.broadcast(clients, message)
        await asyncio.sleep(5)  # Wait for 1 second

async def handle_message(websocket):
    clients.add(websocket)
    try:
        async for message in websocket:
            print(f"Received message: {message}")
            # Echo the message back to all connected clients
            for conn in clients:
                if conn != websocket:
                    await conn.send(message)
    except websockets.exceptions.ConnectionClosed:
        pass
    finally:
        clients.remove(websocket)

async def main():
    # Start the periodic update task
    periodic_task = asyncio.create_task(send_periodic_updates())
    
    # Start the WebSocket server
    server = await websockets.serve(
        handle_message,
        'localhost',
        6969,
        process_request=lambda path, request_headers: None
    )
    
    # Keep both tasks running
    await asyncio.gather(
        server.wait_closed(),
        periodic_task
    )

if __name__ == "__main__":
    asyncio.run(main())