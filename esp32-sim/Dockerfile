FROM python:3.11-slim

WORKDIR /app

# Dipendenze minime: solo requests
RUN pip install --no-cache-dir requests==2.31.0

COPY simulator.py .
COPY secret.py .

CMD ["python", "-u", "simulator.py"]
