import pytest
from playwright.sync_api import Page, expect

BASE_URL = "http://localhost:8000/index.php"

# ==========================================
# FIXTURES (Setup)
# ==========================================

@pytest.fixture(autouse=True)
def preparar_dashboard(page: Page):
    """Inicia sesión automáticamente antes de cada test para aislar los estados."""
    page.goto(BASE_URL)
    page.locator("input[name='nombre_inicial']").fill("Usuario QA")
    page.get_by_role("button", name="Empezar").click()
    
    # Esperamos a que el layout principal cargue para evitar flakiness
    expect(page.locator(".top-bar")).to_be_visible()

# ==========================================
# UI / ESTADO INICIAL
# ==========================================

def test_estado_vacio_tarjetas_y_grafico(page: Page):
    expect(page.locator("#total-gastado")).to_have_text("$ 0")
    expect(page.locator("#total-ingresos")).to_have_text("$ 0")
    expect(page.locator("#total-ahorro")).to_have_text("+ $ 0")
    expect(page.locator("#expenseChart")).to_be_visible()
    expect(page.locator(".legend-item")).to_have_count(8)
    expect(page.locator('#empty-history-msg')).to_contain_text("No hay movimientos recientes.")

def test_toggle_dark_mode(page: Page):
    btn_dark_mode = page.locator("button[aria-label='Dark Mode']")
    body = page.locator("body")
    
    btn_dark_mode.click()
    expect(body).to_have_class("dark-theme")
    
    btn_dark_mode.click()
    expect(body).not_to_have_class("dark-theme")

def test_notificaciones_vacias(page: Page):
    page.locator("button[aria-label='Notificaciones']").click()
    expect(page.get_by_text("No hay notificaciones nuevas.", exact=True)).to_be_visible()

# ==========================================
# HAPPY PATHS (Flujos Ideales)
# ==========================================

def test_registrar_ingreso_actualiza_tarjetas(page: Page):
    page.get_by_text("+ Nuevo").click()
    page.locator("select[name='tipo']:visible").select_option("ingreso")
    page.locator("input[name='monto']:visible").fill("2000")
    page.locator("input[name='descripcion']:visible").fill("Sueldo mensual")
    page.get_by_role("button", name="Guardar").click()

    # Validamos que el DOM se actualizó vía HTMX
    expect(page.locator("#total-ingresos")).to_contain_text("2.000")
    expect(page.locator("#total-ahorro")).to_contain_text("2.000")
    expect(page.locator(".history-list .history-info strong")).to_have_text("Sueldo mensual")

def test_registrar_gasto_descuenta_ahorro(page: Page):
    page.get_by_text("+ Nuevo").click()
    page.locator("select[name='tipo']:visible").select_option("ingreso")
    page.locator("input[name='monto']:visible").fill("3000")
    page.get_by_role("button", name="Guardar").click()

    page.get_by_text("+ Nuevo").click()
    page.locator("input[name='monto']:visible").fill("1000")
    page.locator("input[name='descripcion']:visible").fill("Supermercado")
    page.get_by_role("button", name="Guardar").click()

    expect(page.locator("#total-gastado")).to_contain_text("1.000")
    expect(page.locator("#total-ahorro")).to_contain_text("2.000")

def test_persistencia_datos_al_recargar(page: Page):
    page.get_by_text("+ Nuevo").click()
    page.locator("select[name='tipo']:visible").select_option("ingreso")
    page.locator("input[name='monto']:visible").fill("5000")
    page.get_by_role("button", name="Guardar").click()

    expect(page.locator("#total-ahorro")).to_contain_text("5.000")

# ==========================================
# NEGATIVE PATHS (Manejo de Errores)
# ==========================================

def test_error_gasto_sin_fondos(page: Page):
    page.get_by_text("+ Nuevo").click()
    page.locator("input[name='monto']:visible").fill("1500")
    
    def verificar_y_cerrar_alerta(dialog):
        assert "saldo" in dialog.message.lower()
        dialog.accept()

    page.once("dialog", verificar_y_cerrar_alerta)
    
    page.get_by_role("button", name="Guardar").click()

def test_error_gasto_mayor_a_ingresos(page: Page):
    page.get_by_text("+ Nuevo").click()
    page.locator("select[name='tipo']:visible").select_option("ingreso")
    page.locator("input[name='monto']:visible").fill("1500")
    page.get_by_role("button", name="Guardar").click()

    page.get_by_text("+ Nuevo").click()
    page.locator("input[name='monto']:visible").fill("4500")
    
    def verificar_cerrar_alerta(dialog):
        assert "fondos insuficientes" in dialog.message.lower()
        dialog.accept()

    page.once("dialog", verificar_cerrar_alerta)
    
    page.get_by_role("button", name="Guardar").click()

def test_formulario_bloquea_monto_vacio(page: Page):
    page.get_by_text("+ Nuevo").click()
    page.get_by_role("button", name="Guardar").click()
    
    is_invalid = page.locator("input[name='monto']:visible").evaluate("el => el.validity.valueMissing")
    assert is_invalid is True, "El formulario permitió enviar un monto vacío"

# ==========================================
# EDGE CASES (Casos Límite)
# ==========================================

def test_matematica_acumulativa_multiples_gastos(page: Page):
    page.get_by_text("+ Nuevo").click()
    page.locator("select[name='tipo']:visible").select_option("ingreso")
    page.locator("input[name='monto']:visible").fill("10000")
    page.get_by_role("button", name="Guardar").click()

    montos = ["100", "250", "150"]
    for monto in montos:
        page.get_by_text("+ Nuevo").click()
        page.locator("input[name='monto']:visible").fill(monto)
        page.get_by_role("button", name="Guardar").click()

    expect(page.locator("#total-gastado")).to_contain_text("500")
    expect(page.locator("#total-ahorro")).to_contain_text("9.500")

def test_descripcion_extremadamente_larga(page: Page):
    texto_largo = "A" * 150 
    
    page.get_by_text("+ Nuevo").click()
    page.locator("select[name='tipo']:visible").select_option("ingreso")
    page.locator("input[name='monto']:visible").fill("100")
    page.locator("input[name='descripcion']:visible").fill(texto_largo)
    page.get_by_role("button", name="Guardar").click()
    
    # Valida que el texto no rompa el contenedor principal.
    historial_width = page.locator(".sidebar").evaluate("el => el.scrollWidth <= el.clientWidth")
    assert historial_width is True, "El texto largo desbordó el contenedor CSS"

def test_rechaza_valores_cero_y_negativos(page: Page):
    page.get_by_text("+ Nuevo").click()
    input_monto = page.locator("input[name='monto']:visible")
    btn_guardar = page.get_by_role("button", name="Guardar")

    input_monto.fill("0")
    btn_guardar.click()
    
    is_valid_zero = input_monto.evaluate("el => el.validity.valid")
    assert is_valid_zero is False, "Regresión: El sistema permitió ingresar el valor 0"
    
    input_monto.fill("-50")
    btn_guardar.click()
    
    is_valid_negative = input_monto.evaluate("el => el.validity.valid")
    assert is_valid_negative is False, "Regresión: El sistema permitió ingresar un valor negativo"


def test_gasto_minimo_y_redondeo_visual(page: Page):
    page.get_by_text("+ Nuevo").click()
    page.locator("select[name='tipo']:visible").select_option("ingreso")
    page.locator("input[name='monto']:visible").fill("100")
    page.get_by_role("button", name="Guardar").click()

    page.get_by_text("+ Nuevo").click()
    page.locator("input[name='monto']:visible").fill("0.01")
    page.get_by_role("button", name="Guardar").click()

    # Historial debe mostrar los decimales exactos.
    expect(page.locator(".history-list .history-amount").first).to_contain_text("0,01")

    # Las cards de arriba redondean.
    expect(page.locator("#total-gastado")).to_have_text("$ 0")
