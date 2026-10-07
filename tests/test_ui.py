import pytest
from playwright.sync_api import Page, expect

BASE_URL = "http://localhost:8000/index.php"

def test_flujo_completo_e2e(page: Page):
    page.goto(BASE_URL)
    page.locator("input[name='nombre_inicial']").fill("Usuario QA")
    page.get_by_role("button", name="Empezar").click()

    # --- PASO A: REGISTRAR EL INGRESO ---
    page.get_by_text("+ Nuevo").click()
    
    # IMPORTANTE: Acá tenés que agregar el clic o selección para cambiar 
    # el formulario a modo "Ingreso" (si tenés un botón o un select)
    # Ejemplo: page.get_by_text("Ingreso").click()
    page.locator("select[name='tipo']:visible").select_option("ingreso")

    page.locator("input[name='monto']:visible").fill("10000")
    page.locator("input[name='descripcion']:visible").fill("Sueldo")
    page.get_by_role("button", name="Guardar").click()
    page.wait_for_timeout(1000)

    # --- PASO B: REGISTRAR EL GASTO ---
    page.get_by_text("+ Nuevo").click()
    # (Asegurate de volver a seleccionar "Gasto" si es necesario)
    page.locator("input[name='monto']:visible").fill("4500")
    page.locator("input[name='descripcion']:visible").fill("Cena de prueba QA")
    page.get_by_role("button", name="Guardar").click()
    page.wait_for_timeout(1000)

    # --- PASO C: VERIFICAR ---
    page.get_by_text("Actividad", exact=True).click()
    page.wait_for_timeout(1000)
    tabla_actividad = page.locator(".actividad-items")
    expect(tabla_actividad).to_contain_text("Cena de prueba QA")

def test_alerta_saldo_insuficiente(page: Page):
    """Verifica que el sistema impida registrar un gasto sin saldo suficiente."""
    page.goto(BASE_URL)
    page.locator("input[name='nombre_inicial']").fill("Usuario QA")
    page.get_by_role("button", name="Empezar").click()

    page.get_by_text("+ Nuevo").click()
    page.locator("input[name='monto']:visible").fill("4500")
    page.locator("input[name='descripcion']:visible").fill("Intento de Gasto")

    # 2. Creamos un "atajador" para la alerta
    def verificar_y_cerrar_alerta(dialog):
        # Comprobamos que el texto de la alerta sea el correcto
        assert "saldo" in dialog.message.lower()
        dialog.accept() # Hacemos clic en "Aceptar"

    # 3. Le decimos a Playwright que escuche la próxima alerta y use nuestro atajador
    page.once("dialog", verificar_y_cerrar_alerta)
    
    page.get_by_role("button", name="Guardar").click()
