from flask import Flask, request, jsonify
import joblib
import numpy as np
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

# Charger le modèle
try:
    model = joblib.load('model_calories.pkl')
    print("✅ Modèle chargé avec succès.")
except Exception as e:
    print("❌ Erreur de chargement du modèle :", e)
    model = None

@app.route('/')
def home():
    return jsonify({'message': 'API Flask active ✅'})

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json()
        duration = float(data.get('duration', 0))
        intensity_str = str(data.get('intensity', 'medium')).lower()

        # conversion intensité
        if intensity_str == 'low':
            intensity = 1
        elif intensity_str == 'medium':
            intensity = 2
        else:
            intensity = 3

        if model is None:
            return jsonify({'error': 'Modèle non disponible'}), 500

        prediction = model.predict(np.array([[duration, intensity]]))[0]
        return jsonify({'prediction': round(float(prediction), 2)})

    except Exception as e:
        print("❌ Erreur dans /predict :", e)
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(port=5000, debug=True)
