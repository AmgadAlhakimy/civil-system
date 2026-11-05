from flask import Flask, request, jsonify
import io
import os

os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'

from deepface import DeepFace
import numpy as np
from PIL import Image

app = Flask(__name__)
API_KEY = os.environ.get('FACE_AI_KEY', '')

@app.before_request
def check_api_key():
    if API_KEY and request.headers.get('X-Api-Key') != API_KEY:
        return jsonify({'error': 'unauthorized'}), 401

@app.route('/extract', methods=['POST'])
def extract():
    if 'image' not in request.files:
        return jsonify({'error': 'no image'}), 400

    file = request.files['image']
    try:
        img_bytes = file.read()
        img = Image.open(io.BytesIO(img_bytes)).convert('RGB')
        img_array = np.array(img)

        embeddings = DeepFace.represent(
            img_path=img_array,
            model_name='Facenet512',
            enforce_detection=True,
        )

        if not embeddings or len(embeddings) == 0:
            return jsonify({'error': 'no_face_detected'}), 422

        if len(embeddings) > 1:
            return jsonify({'error': 'multiple_faces_detected'}), 422

        return jsonify({'embedding': embeddings[0]['embedding']})

    except Exception as e:
        err = str(e).lower()
        if 'face could not be detected' in err or 'no face' in err:
            return jsonify({'error': 'no_face_detected'}), 422
        return jsonify({'error': str(e)}), 500

@app.route('/health', methods=['GET'])
def health():
    return jsonify({'status': 'ok'})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8001)