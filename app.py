from flask import Flask, render_template, jsonify, request
from flask_socketio import SocketIO, emit
import serial
import json
from flask_cors import CORS


app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "http://localhost:3000"}})
app.secret_key = 'your_secret_key' 
socketio = SocketIO(app, cors_allowed_origins="http://localhost:3000")

# Set up Serial communication with Arduino
ser = None  # Start with no active serial connection

@app.after_request
def add_cors_headers(response):
    response.headers.add('Access-Control-Allow-Origin', 'http://localhost:3000')
    response.headers.add('Access-Control-Allow-Headers', 'Content-Type, Authorization')
    return response

@socketio.on('connect')
def handle_connect():
    print('Client connected')

@socketio.on('disconnect')
def handle_disconnect():
    print('Client disconnected')

@socketio.on('rfid_scan')
def handle_rfid_scan():
    global ser
    try:
        if ser and ser.is_open:
            while True:
                line = ser.readline().decode('utf-8').strip()
                if line.startswith("Card Detected:"):
                    json_data = json.loads(line.replace("Card Detected:", "").strip())
                    print(f"Scanned RFID Data: {json_data}")  # Print data to Python console
                    emit('rfid_received', json_data)
                    print(json_data)
    except Exception as e:
        print(f"Error: {e}")
        if ser:
            ser.close()

@socketio.on('serial_port_opened')
def handle_serial_port_opened(data):
    global ser
    try:
        if ser:
            ser.close()  # Close any previously opened connection
        ser = serial.Serial(data['portName'], data['baudRate'])
        print('Serial port opened:', data)
    except Exception as e:
        print(f"Failed to open serial port: {e}")


if __name__ == '__main__':
    socketio.run(app, host='0.0.0.0', port=5000)
