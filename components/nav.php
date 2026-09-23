<aside class="sidebar" id="sidebar" x-data="{collapse:false}" :class="collapsed ? 'collapsed' : ''">
	<button id="toggle-btn" class="toggle-btn" @click="collapsed = !collapsed">
			<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#178A40" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
				<polyline points="15 18 9 12 15 6"></polyline>
			</svg>
		</button>
		
	<div class="profile-section">
		<div class="avatar">
			<span class="avatar-text">Perfil</span>
			<div class="gear-icon">
				<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<circle cx="12" cy="12" r="3"></circle>
					<path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
				</svg>
			</div>
		</div>
	</div>
	<nav class="menu-links" hx-target="#main-content" hx-swap="innerHTML">
		<a href="#" 
			hx-get="components/dashboard_content.php" 
	 	    hx-target="#main-content"
	 	    @click="isModalOpen = false">Dashboard</a>
	 	    
	 	<a href="#" 
		    hx-get="components/tarjetas_content.php" 
	 	    hx-target="#main-content"
	 	    @click="isModalOpen = false">Tarjetas</a>
		       
		<a href="suscripciones.php" hx-get="suscripciones_content.php" hx-push-url="true">Suscripciones</a>
		<a href="actividad.php" hx-get="actividad_content.php" hx-push-url="true">Actividad</a>
		<a href="inversiones.php" hx-get="inversiones_content.php" hx-push-url="true">Inversiones</a>
		<a href="configuracion.php" hx-get="configuracion_content.php" hx-push-url="true">Configuración</a>
	</nav>
</aside>
