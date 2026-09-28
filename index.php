<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: index.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_inicial'])){
	$_SESSION['nombre_usuario'] = trim($_POST['nombre_inicial']);
	if(empty($_SESSION['nombre_usuario'])){
		$_SESSION['nombre_usuario'] = 'Usuario Demo';
	}
	header("Location: index.php");
	exit;
}

$mostrarLanding = !isset($_SESSION['nombre_usuario']);
?>

<?php require 'components/header.php'; ?>

<script>
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-theme');
    }
</script>

<?php if ($mostrarLanding): ?>
	<div x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" style="min-height: 100vh; display: flex; flex-direction: column;">
	        <!-- Fondo Animado -->
	        <div class="landing-bg">
	            <div class="orb orb-1"></div>
	            <div class="orb orb-2"></div>
	        </div>
	    
	        <!-- Botón Dark Mode -->
	        <div style="display: flex; justify-content: flex-end; padding: 30px;">
	            <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light'); darkMode ? document.body.classList.add('dark-theme') : document.body.classList.remove('dark-theme')" style="background: none; border: none; cursor: pointer; padding: 10px; background: rgba(255,255,255,0.5); border-radius: 50%; backdrop-filter: blur(5px);">
	                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-secondary" style="color: #333;">
	                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
	                </svg>
	            </button>
	        </div>
	    
	        <!-- CONTENEDOR CENTRAL RECUPERADO (Este es el que centra todo) -->
	        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 20px; gap: 40px;">
	        
	            <div class="glass-card fade-up" style="max-width: 650px;">
	                <h1 class="text-primary" style="font-size: 32px; color: #178A40; margin-bottom: 20px; line-height: 1.3;">¿Alguna vez sentiste que no estás al tanto de tu dinero?</h1>
	                <p class="text-secondary" style="font-size: 18px; color: #666; margin-bottom: 5px;">¿No sabés cuánto ahorraste el mes pasado?</p>
	                <p class="text-dark" style="font-size: 18px; font-weight: bold; color: #333;">Este lugar es para vos.</p>
	            </div>
	        
	            <!-- Formulario con las clases de animación nuevas -->
	            <form method="POST" action="index.php" class="fade-up delay-1" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 15px;">
	                <input type="text" name="nombre_inicial" class="landing-input" placeholder="Ingresá tu nombre de usuario" required maxlength="12" autocomplete="off">
	                <button type="submit" class="landing-btn">Empezar</button>
	            </form>
	        
	            <div class="glass-card fade-up delay-2" style="max-width: 500px; padding: 25px;">
	                <p class="text-secondary" style="color: #666; font-size: 15px; margin: 0; line-height: 1.5;">Un ecosistema financiero minimalista, privado y sin bases de datos externas. Toda tu información vive de forma segura en la memoria de tu navegador.</p>
	            </div>
	            
	        </div>
	        <!-- FIN DEL CONTENEDOR CENTRAL -->
	    
	        <!-- Footer -->
	        <div class="text-secondary" style="text-align: center; padding: 20px; color: #888; font-size: 13px;">
	            &copy; <?= date('Y') ?> Tu SaaS Financiero. Todos los derechos reservados.
	        </div>
	    </div>
</div>


<?php else: ?>


<div class="app-container" 
	x-data="{ isModalOpen: false, isCardModalOpen: false, tipo: 'gasto', vistaActual: 'dashboard', isSubModalOpen: false, isInvModalOpen: false, darkMode: localStorage.getItem('theme') === 'dark', showNotif: false, notifRead: false}"
	:class="darkMode ? 'dark-theme' : ''">
	<?php require 'components/nav.php'; ?>

	<div class="main-wrapper">
		<header class="top-bar" style="display: flex; justify-content: space-between; align-items: center;">
		    <div class="header-title">
		        <h2 style="margin:0; color: #333; font-size: 18px; text-transform: capitalize;" x-text="vistaActual"></h2>
		    </div>
		
		    <div class="top-bar-actions" style="display: flex; gap: 15px; align-items: center;">
		        <button class="btn-primary" x-show="vistaActual === 'dashboard'" @click="isModalOpen = true" style="padding: 8px 15px;">+ Nuevo</button>
		        
		        <button aria-label="Dark Mode" @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light'); darkMode ? document.body.classList.add('dark-theme') : document.body.classList.remove('dark-theme')" style="background: none; border: none; cursor: pointer;">
		            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
		                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
		            </svg>
		        </button>
		        
		        <div style="position: relative;">
		           <button aria-label="Notificaciones" @click="showNotif = !showNotif; notifRead = true" style="background: none; border: none; cursor: pointer; position: relative;">
		                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
		                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
		                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
		                </svg>
		                <?php if(isset($_SESSION['suscripciones']) && count($_SESSION['suscripciones']) > 0): ?>
		                    <span x-show="!notifRead" style="position: absolute; top: -2px; right: -2px; width: 10px; height: 10px; background: #E50914; border-radius: 50%;"></span>
		                <?php endif; ?>
		            </button>
		
		            <div x-show="showNotif" @click.away="showNotif = false" class="notif-menu" style="display: none;" x-transition>
		                <h4>Notificaciones</h4>
		                <ul>
		                    <?php 
		                    $subsActivas = count($_SESSION['suscripciones'] ?? []);
		                    if ($subsActivas > 0): ?>
		                        <li>
		                            🟢 Tienes <strong><?= $subsActivas ?> suscripciones</strong> activas con débito automático mensual.
		                        </li>
		                    <?php else: ?>
		                        <li style="opacity: 0.6;">No hay notificaciones nuevas.</li>
		                    <?php endif; ?>
		                </ul>
		            </div>
		        </div>
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
	                        <input type="number" name="monto" placeholder="$ 0.00" step="0.01" min="0.01" required>
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
	                    <input type="text" 
	                    name="descripcion" 
	                    :placeholder="tipo === 'gasto' ? 'Ej: Cena en pizzeria...' : 'Ej: Remuneración por servicios...'">
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
                        <input type="text" 
                        name="titular" 
                        placeholder="Ej: JUAN PEREZ" 
                        required
                        oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
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

    <!-- Modal para agregar Suscripcion -->
    <div class="modal-overlay" x-show="isSubModalOpen" style="display: none;" x-transition.opacity>
    	<div class="modal-content" @click.away="isSubModalOpen = false" x-show="isSubModalOpen" x-transition>
    		<div class="modal-header">
    			<h3>Nueva Suscripcion</h3>
    			<button class="close-btn" @click="isSubModalOpen = false">&times;</button>
    		</div>

    		<form class="modal-form"
    			hx-post="processors/guardar_suscripcion.php"
    			hx-target="#main-content"
    			hx-swap="innerHTML"
    			@htmx:after-request="if($event.detail.successful) { isSubModalOpen = false; $el.reset(); }">

    			<div class="form-group">
    				<label>Plataforma</label>
    				<select name="plataforma" id="plataformas-disponibles" required>
						<option value="" disabled selected>Elegí una plataforma...</option>
    					<option value="Netflix">Netflix</option>
    					<option value="Spotify">Spotify</option>
    					<option value="Google Gemini">Google Gemini</option>
    					<option value="Kick">Kick</option>
    					<option value="HBO Max">HBO Max</option>
    					<option value="Github Sponsors">Github Sponsors</option>
    					<option value="Paramount+">Paramount+</option>
    					<option value="iCloud+">iCloud+</option>
    					<option value="Deezer">Deezer</option>
    					<option value="Apple TV">Apple TV</option>
    				</select>
    			</div>

    			<div class="form-row">
    				<div class="form-group">
    					<label>Día de cobro</label>
    					<input type="number" name="dia" placeholder="Ej: 15" min="1" max="31" required>
    				</div>
    				<div class=form-group>
    					<label>Costo (USD)</label>
    					<input type="number" name="usd" placeholder="Ej: 8.99" step="0.01" min="0" required>
    				</div>
    			</div>

    			<button type="submit" class="btn-submit">Guardar Suscripción</button>
    		</form>
    	</div>
    </div>

	<!-- Modal para Nueva Inversión -->
	<div class="modal-overlay" x-show="isInvModalOpen" style="display: none;" x-transition.opacity>
	    <div class="modal-content" @click.away="isInvModalOpen = false" x-show="isInvModalOpen" x-transition>
	        <div class="modal-header">
	            <h3>Comprar Activo</h3>
	            <button class="close-btn" @click="isInvModalOpen = false">&times;</button>
	        </div>
	        
	        <form class="modal-form" 
	              hx-post="processors/guardar_inversion.php" 
	              hx-target="#main-content" 
	              hx-swap="innerHTML"
	              @htmx:after-request="if($event.detail.successful) { isInvModalOpen = false; $el.reset(); }">
	            
	            <div class="form-group">
	                <label>Activo (Ticker)</label>
	                <select name="ticker" required>
	                    <option value="SPY">S&P 500 ETF (SPY)</option>
	                    <option value="AAPL">Apple Inc. (AAPL)</option>
	                    <option value="MSFT">Microsoft (MSFT)</option>
	                </select>
	            </div>
	            <div class="form-row">
	                <div class="form-group">
	                    <label>Monto Invertido (u$s)</label>
	                    <input type="number" name="monto" placeholder="Ej: 100" step="0.01" min="1" required>
	                </div>
	                <div class="form-group">
	                    <label>Precio de Compra (u$s)</label>
	                    <input type="number" name="precio_compra" placeholder="Ej: 540.20" step="0.01" min="0.01" required>
	                </div>
	            </div>
	            
	            <div class="form-group">
	                <label>Fecha de Operación</label>
	                <input type="date" name="fecha" required>
	            </div>
	            <button type="submit" class="btn-submit">Confirmar Compra</button>
	        </form>
	    </div>
	</div>
</div>
<?php endif; ?>



<?php require 'components/footer.php'; ?>
