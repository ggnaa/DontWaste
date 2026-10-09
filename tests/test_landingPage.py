import pytest
from playwright.sync_api import Page, expect

BASE_URL = "http://localhost:8000/index.php"

def test_a_login_exitoso(page: Page):
    page.goto(BASE_URL)
    page.locator("input[name='nombre_inicial']").fill("QA tester")
    page.get_by_text("Empezar").click()
    
    titulo_dashboard = page.get_by_role("heading", name="dashboard", exact=True)
    expect(titulo_dashboard).to_be_visible()

@pytest.mark.xfail(reason="Bug: Permite emojis y no muestra .error-msg")
def test_b_caracteres_invalidos(page: Page):
    page.goto(BASE_URL)
    input_nombre = page.locator("input[name='nombre_inicial']")
    input_nombre.fill("QA 🚀 tester")
    page.get_by_text("Empezar").click()
    
    # Asume que tu código limpia el input o frena el envío. 
    # Ajustá la aserción según cómo manejes este error en DontWaste.
    expect(page.locator(".error-msg")).to_contain_text("Solo se permiten caracteres ASCII")

def test_c_limite_caracteres(page: Page):
    page.goto(BASE_URL)
    input_nombre = page.locator("input[name='nombre_inicial']")
    
    expect(input_nombre).to_have_attribute("maxlength", "12")
    input_nombre.fill("a" * 60)
    expect(input_nombre).to_have_value("a" * 12)

def test_d_campo_vacio(page: Page):
    page.goto(BASE_URL)
    input_nombre = page.locator("input[name='nombre_inicial']")
    page.get_by_text("Empezar").click()
    
    # Evalúa la validación nativa "required" de HTML5
    es_valido = input_nombre.evaluate("el => el.checkValidity()")
    assert es_valido is False

def test_e_y_f_alternar_dark_mode(page: Page):
    # Agrupamos E y F en un solo test lógico de toggle
    page.goto(BASE_URL)
    btn_theme = page.locator('button[\\@click*="darkMode"]')
    body = page.locator("body")

    # Test E: Pasar a Dark
    btn_theme.click()
    expect(body).to_have_class("dark-theme")

    # Test F: Volver a Light
    btn_theme.click()
    expect(body).not_to_have_class("dark-theme")
