import pytest
from playwright.sync_api import Page, expect

BASE_URL = "http://localhost:8000/index.php"

def test_borrado_suscripcion(page: Page):

    page.goto(BASE_URL)
    page.locator("input[name='nombre_inicial']").fill("QA")
    page.get_by_role("button", name="Empezar").click()

    page.get_by_text("Suscripciones", exact=True).click()
    page.wait_for_timeout(1000)

    page.get_by_role("button", name="+ Nueva Suscripción").click()

    page.locator("select[name='plataforma']").select_option("Kick")
    page.locator("input[name='dia']").fill("5")
    page.locator("input[name='usd']").fill("10")
    page.get_by_text("Guardar Suscripción").click()

    page.wait_for_timeout(1500)

    lista_subs = page.locator("#list-subs")
    expect(lista_subs).to_contain_text("Kick")

    fila_subs = page.locator(".subs-row", has_text="Kick")
    fila_subs.get_by_title("Cancelar suscripcion").click()

    page.wait_for_timeout(1500)
    expect(lista_subs).not_to_contain_text("Kick")
