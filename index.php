<? php session_start(); ?>

<?php require 'components/header.php'; ?>

<div class="app-container" x-data="{ isModalOpen: false, tipo: 'gasto' }">
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
				<button class="btn-primary" @click="isModalOpen = true">+ Nuevo</button>
				
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
	            		hx-post="guardar_movimiento.php"
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
	
	                <button type="submit" class="btn-submit">Guardar Registro</button>
	            </form>
	        </div>
	    </div>
	
</div>


<?php require 'components/footer.php'; ?>
