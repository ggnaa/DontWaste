import requests
import pytest

BASE_URL = "http://localhost:8000"

# Fixture: Esto se ejecuta antes de cada test. 
# Crea un "navegador invisible" que guarda las cookies de sesión.
@pytest.fixture
def cliente():
    session = requests.Session()
    # Hacemos un GET inicial para que PHP nos asigne un PHPSESSID
    session.get(f"{BASE_URL}/index.php")
    return session

def test_estado_pagina_principal(cliente):
    """Verifica que el servidor esté vivo y cargue el dashboard."""
    respuesta = cliente.get(f"{BASE_URL}/index.php")
    
    # QA Check 1: El servidor respondió correctamente (Código 200)
    assert respuesta.status_code == 200
    # QA Check 2: El HTML contiene el título del proyecto
    assert "DontWaste" in respuesta.text

def test_borrar_suscripcion_inexistente(cliente):
    """Verifica cómo reacciona el backend al intentar borrar algo que no existe."""
    payload = {"plataforma": "PlataformaFalsa"}
    respuesta = cliente.post(f"{BASE_URL}/processors/borrar_suscripcion.php", data=payload)
    
    # QA Check: Aunque la plataforma no exista, el servidor no debe romperse (Evitar Error 500)
    assert respuesta.status_code == 200

def test_seguridad_inyeccion_xss(cliente):
    """Verifica la sanitización de inputs usando un payload malicioso."""
    # Simulamos el envío del formulario de una nueva suscripción
    payload = {
        "plataforma": "<script>alert('HACKED')</script>",
        "dia": "10",
        "usd": "15.00"
    }
    
    # Nota: Ajustá la URL si tu archivo se llama guardar_suscripcion.php u otro nombre
    respuesta = cliente.post(f"{BASE_URL}/processors/guardar_suscripcion.php", data=payload)
    
    # QA Check 1: El servidor no debe fallar
    assert respuesta.status_code == 200
    
    # QA Check 2: Verificamos que la función htmlspecialchars() de PHP haya hecho su trabajo.
    # El HTML resultante NUNCA debe contener la etiqueta <script> cruda.
    assert "<script>" not in respuesta.text
    # Debería haberse convertido en texto seguro (&lt;script&gt;) o haber sido rechazado
