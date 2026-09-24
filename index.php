<?php
	// 1. Si la URL trae la orden de reset, destruimos la sesión ANTES de cualquier HTML
	if (isset($_GET['action']) && $_GET['action'] === 'reset') {
	    session_start();
	    $_SESSION = array();
	    session_destroy();
	    
	    // Redirigimos para limpiar la URL, pero JavaScript ya sabrá que se ejecutó
	    header("Location: index.php");
	    exit;
	}
?>

<?php require 'components/header.php'; ?>

<script>
        // Este código se ejecuta antes de que el usuario vea la página
        // Comprobamos si es la primera vez que entra en esta pestaña
        if (!sessionStorage.getItem('pagina_cargada')) {
            // Guardamos la marca en el navegador de que ya entró
            sessionStorage.setItem('pagina_cargada', 'true');
            
            // Forzamos el reset en PHP
            window.location.href = "index.php?action=reset";
        }

        // DETECTAR F5: Si el usuario recarga la página, borramos la marca y recargamos con reset
        window.addEventListener('beforeunload', function () {
            // Al salir o recargar, borramos la marca para que el próximo inicio ejecute el reset
            sessionStorage.removeItem('pagina_cargada');
        });
    </script>

<div class="app-container" x-data="{ isModalOpen: false, isCardModalOpen: false, tipo: 'gasto', vistaActual: 'dashboard'}">
	<?php require 'components/nav.php'; ?>

	<div class="main-wrapper">
		<header class="top-bar">
			<div class="search-container">
				<input type="text" 
					placeholder="buscador de palabra clave..."
					name="q"
					hx-get="buscar.php"
					hx-trigger="keyup changed delay:500ms"
					hx-target="#main-content">
				<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#666" stroke-width="2">
					<circle cx="11" cy="11" r="8"></circle>
					<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
				</svg>
			</div>
			<div class="top-bar-actions">
				<button class="btn-primary" x-show="vistaActual === 'dashboard'" @click="isModalOpen = true">+ Nuevo</button>
				
				<button aria-label="Dark Mode">
					<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
					</svg>
				</button>
				
				<button aria-label="Notificaciones">
					<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
						<path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
					</svg>
				</button>
			</div>
		</header>

		<main id="main-content" class="content">
			<?php include 'components/dashboard_content.php' ; ?>
		</main>
	</div>

	<!-- EL MODAL FLOTANTE -->
	    <!-- x-show reacciona al estado. x-cloak evita destellos al cargar. -->
	    <div class="modal-overlay" x-show="isModalOpen" style="display: none;" x-transition.opacity>
	        <!-- @click.away cierra el modal si hacés clic fuera de la caja blanca -->
	        <div class="modal-content" @click.away="isModalOpen = false" x-show="isModalOpen" x-transition>
	            <div class="modal-header">
	                <h3>Registrar Movimiento</h3>
	                <button class="close-btn" @click="isModalOpen = false">&times;</button>
	            </div>
	            
	            <form class="modal-form"
	            		hx-post="processors/guardar_movimiento.php"
	            		hx-target=".history-list"
	            		hx-swap="afterbegin"
	            		@htmx:after-request="if($event.detail.successful) { isModalOpen = false; $el.reset(); tipo = 'gasto'; }">
	                <div class="form-row">
	                    <div class="form-group">
	                        <label>Tipo</label>
	                        <select name="tipo" x-model="tipo">
	                            <option value="gasto">Gasto</option>
	                            <option value="ingreso">Ingreso</option>
	                        </select>
	                    </div>
	                    <div class="form-group">
	                        <label>Monto</label>
	                        <input type="number" name="monto" placeholder="$ 0.00" step="0.01" required>
	                    </div>
	                </div>
	                
	                <div class="form-group">
	                    <label>Categoría</label>
	                    <select name="categoria" x-show="tipo === 'gasto'" :disabled="tipo !== 'gasto'">
	                        <option>Gastronomía</option>
	                        <option>Supermercado</option>
	                        <option>Vivienda</option>
	                        <option>Servicios</option>
	                        <option>Transporte</option>
	                        <option>Ocio</option>
	                        <option>Inversiones</option>
	                        <option>Varios</option>
	                    </select>

	                    <select name="categoria" x-show="tipo === 'ingreso'" :disabled="tipo !== 'ingreso'">
	                    	<option>Sueldo</option>
	                    	<option>Honorarios</option>
	                    	<option>Rendimientos</option>
	                    	<option>Ventas</option>
	                    	<option>Otros Ingresos</option>
	                    </select>
	                </div>
	
	                <div class="form-group">
	                    <label>Descripción / Detalle</label>
	                    <input type="text" name="descripcion" placeholder="Ej: Cena en pizzeria...">
	                </div>

	                <div class="form-group" x-show="tipo === 'gasto'">
	                	<label>Método de Pago</label>
	                	<select name="metodo_pago" id="selector-metodo" :disabled="tipo !== 'gasto'">
	                		<option value="efectivo">Débito / Efectivo</option>
	                		<?php
	                		$tarjetasActivas = $_SESSION['tarjetas'] ?? [];
	                		foreach($tarjetasActivas as $index => $t){
	                			$nombreTarjeta = ucfirst($t['marca']) . ' **** ' . $t['numeros'];
	                			echo "<option value='tarjeta_{$index}'>Crédito: " . htmlspecialchars($nombreTarjeta) . "</option>";
	                		}
	                		?>
	                	</select>
	                </div>
	
	                <button type="submit" class="btn-submit">Guardar Registro</button>
	            </form>
	        </div>
	    </div>
	
<!-- MODAL PARA AGREGAR TARJETA -->
    <div class="modal-overlay" x-show="isCardModalOpen" style="display: none;" x-transition.opacity>
        <div class="modal-content" @click.away="isCardModalOpen = false" x-show="isCardModalOpen" x-transition>
            <div class="modal-header">
                <h3>Vincular Tarjeta</h3>
                <button class="close-btn" @click="isCardModalOpen = false">&times;</button>
            </div>
            
            <form class="modal-form" 
                  hx-post="processors/guardar_tarjeta.php" 
                  hx-target="#lista-tarjetas" 
                  hx-swap="afterbegin"
                  @htmx:after-request="if($event.detail.successful) { isCardModalOpen = false; $el.reset(); }">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Marca</label>
                        <select name="marca" required>
                            <option value="visa">Visa</option>
                            <option value="mastercard">Mastercard</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Últimos 4 números</label>
                        <input type="text" 
                        	name="numeros" 
                        	placeholder="Ej: 4281" 
                        	maxlength="4" 
                        	required 
                        	oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Titular</label>
                        <input type="text" name="titular" placeholder="Ej: JUAN PEREZ" required>
                    </div>
                    <div class="form-group">
                        <label>Vencimiento</label>
                        <input type="text" 
                        	name="vencimiento" 
                        	placeholder="MM/AA" 
                        	maxlength="5" 
                        	required 
                        	oninput="let v = this.value.replace(/\D/g, ''); this.value = v.length > 2 ? v.slice(0,2) + '/' + v.slice(2,4) : v;">
                	</div>
                </div>

                <div class="form-group">
                    <label>Límite de Crédito</label>
                    <input type="number" name="limite" placeholder="$ 0.00" step="0.01" min="0" required>
                </div>

                <button type="submit" class="btn-submit">Guardar Tarjeta</button>
            </form>
        </div>
    </div>
    
</div>





<?php require 'components/footer.php'; ?>
