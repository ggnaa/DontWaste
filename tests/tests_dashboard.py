import pytest
from playwright.sync_api import Page, expect

BASE_URL = "http://localhost:8000/index.php"

def test_estado_vacio(page: Page):
    # 0. Precondición: Ingreso como usuario nuevo
    page.goto(BASE_URL)
    page.locator("input[name='nombre_inicial']").fill("UsuarioVacio")
    page.get_by_text("Empezar").click()

    # 1, 2 y 3: Validar tarjetas de resumen en cero
    expect(page.locator("#total-gastado")).to_have_text("$ 0")
    expect(page.locator("#total-ingresos")).to_have_text("$ 0")
    expect(page.locator("#total-ahorro")).to_have_text("+ $ 0")

    # 4: Verificar que el gráfico no esté
    # to_be_hidden() pasa si tiene display:none, visibility:hidden o no existe en el HTML
    expect(page.locator("#expenseChart")).to_be_visible()

    # 5: Validar que no haya items de leyenda apagados/seleccionados
    # Asumimos que la clase que los pone grises es "item-apagado" (ajustalo a tu CSS)
    expect(page.locator(".legend-item")).to_have_count(8)

    # 6: Validar el texto del historial vacío
    # Asumimos que la lista tiene un ID o clase específica
    expect(page.locator('#empty-history-msg')).to_contain_text("No hay movimientos recientes.")

    # 7 y 8: Validar Layout (que las barras estén renderizadas)
    expect(page.locator(".top-bar")).to_be_visible()
    expect(page.locator(".sidebar")).to_be_visible()

    # 9 y 10: Validar Dark Mode en el Dashboard
    btn_dark_mode = page.locator("button[aria-label='Dark Mode']")
    body = page.locator("body")
        
    btn_dark_mode.click()
    expect(body).to_have_class("dark-theme")
        
    btn_dark_mode.click()
    expect(body).not_to_have_class("dark-theme")
    
    # 11: Validar estado vacío de Notificacione
    page.locator("button[aria-label='Notificaciones']").click()
    expect(page.get_by_text("No hay notificaciones nuevas.", exact=True)).to_be_visible()

def test_gasto_sin_ingreso(page: Page):
    # 0: Ingresar 
    page.goto(BASE_URL)
    page.locator("input[name='nombre_inicial']").fill("Usuario QA")
    page.get_by_role("button", name="Empezar").click()

    # 1: Intentar ingresar un gasto
    page.get_by_text("+ Nuevo").click()
    page.locator("input[name='monto']:visible").fill("1500")
    def verificar_y_cerrar_alerta(dialog):
        assert "saldo" in dialog.message.lower()
        dialog.accept()
        
    page.once("dialog",verificar_y_cerrar_alerta)

def test_ingresoMenor_gastoMayor(page: Page):
    page.goto(BASE_URL)
    page.locator("input[name='nombre_inicial']").fill("Usuario QA")
    page.get_by_role("button", name="Empezar").click()

    page.get_by_text("+ Nuevo").click()
    page.locator("select[name='tipo']:visible").select_option("ingreso")
    page.locator("input[name='monto']:visible").fill("1500")
    page.locator("input[name='descripcion']:visible").fill("Ingreso de prueba QA")
    page.get_by_role("button", name="Guardar").click()

    page.get_by_text("+ Nuevo").click()
    page.locator("input[name='monto']:visible").fill("4500")
    page.locator("input[name='descripcion']:visible").fill("Cena de prueba QA")
    page.get_by_role("button", name="Guardar").click()

    def verificar_cerrar_alerta(dialog):
        assert "Fondos insuficientes:" in dialog.message.lower()
        dialog.accept()

    page.once("dialog", verificar_cerrar_alerta)
    
