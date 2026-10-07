# conftest.py
import pytest

def pytest_addoption(parser):
    parser.addoption("--viewport", action="store", default="1280x720")

@pytest.fixture(scope="session")
def browser_context_args(browser_context_args, request):
    viewport_str = request.config.getoption("--viewport")
    width, height = map(int, viewport_str.lower().split('x'))
    
    return {
        **browser_context_args,
        "viewport": {
            "width": width,
            "height": height,
        }
    }
