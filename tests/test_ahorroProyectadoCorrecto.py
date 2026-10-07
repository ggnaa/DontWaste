import pytest
from playwright.sync_api import Page, expect

BASE_URL = "http://localhost:8000/index.php"

def test_ahorro(page: Page):
    page.goto(BASE_URL)
    page.locator("input[name='nombre_inicial']").fill("QA")
    page.get_by_role("button", name="Empezar").click()

    page.get_by_text("+ Nuevo").click()
    page.locator("select[name='tipo']:visible").select_option("ingreso")
    page.locator("input[name='monto']:visible").fill("1000")
    page.locator("input[name='descripcion']:visible").fill("Sueldo octubre")
    page.get_by_role("button", name="Guardar").click()
    page.wait_for_timeout(1000)

    page.get_by_text("+ Nuevo").click()
    page.locator("input[name='monto']:visible").fill("200")
    page.locator("input[name='descripcion']:visible").fill("Hamburguesa")
    page.get_by_role("button", name="Guardar").click()

    expect(page.get_by_role("heading", name="Ahorro proyectado").locator("xpath=..//p")).to_have_text("+ $ 800")
