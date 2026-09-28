<aside class="sidebar" id="sidebar" x-data="{collapse:false}" :class="collapsed ? 'collapsed' : ''">
	<div class="profile-section">
		<div class="avatar">
			<span class="avatar-text" id="nombre-usuario-nav"><?= htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Usuario Demo') ?></span>
		</div>
	</div>
	<nav class="menu-links" hx-target="#main-content" hx-swap="innerHTML">
		<a href="#" 
			hx-get="components/dashboard_content.php" 
	 	    @click="isModalOpen = false; vistaActual = 'dashboard'"
	 	    :class="vistaActual === 'dashboard' ? 'active' : ''">Dashboard</a>
	 	    
	 	<a href="#" 
		    hx-get="components/tarjetas_content.php" 
	 	    @click="isModalOpen = false; vistaActual = 'tarjetas'"
	 	    :class="vistaActual === 'tarjetas' ? 'active' : ''">Tarjetas</a>
		       
		<a href="#" 
			hx-get="components/suscripciones_content.php" 
			@click="isModalOpen = false; vistaActual = 'suscripciones'"
			:class="vistaActual === 'suscripciones' ? 'active' : ''">Suscripciones</a>

		<a href="#"
			hx-get="components/actividad_content.php"	
			@click="isModalOpen = false; vistaActual = 'actividad'"
			:class="vistaActual === 'actividad' ? 'active' : ''">Actividad</a>
			
		<a href="#"
			hx-get="components/inversiones_content.php"
			@click="isModalOpen = false; vistaActual = 'inversiones'"
			:class="vistaActual === 'inversiones' ? 'active' : ''">Inversiones</a>

		<a href="#"
			hx-get="components/configuracion_content.php"
			@click="isModalOpen = false; vistaActual = 'configuracion'"
			:class="vistaActual === 'configuracion' ? 'active' : ''">Configuración</a>
	</nav>
</aside>
