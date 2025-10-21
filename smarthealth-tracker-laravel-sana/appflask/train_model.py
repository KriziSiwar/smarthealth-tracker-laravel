import numpy as np
from sklearn.linear_model import LinearRegression
import joblib

# Données fictives d’entraînement : [durée, intensité]
X = np.array([
    [20, 1],
    [30, 1],
    [30, 2],
    [45, 2],
    [60, 3],
    [90, 3]
])

# Calories brûlées correspondantes
y = np.array([120, 180, 250, 320, 500, 700])

model = LinearRegression()
model.fit(X, y)

joblib.dump(model, 'model_calories.pkl')
print("✅ Modèle sauvegardé sous model_calories.pkl")
