{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}

	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="margin-right: 10px;" width="32" height="32" alt="" />CREAR UN NUEVO CACHE
	</div>

	<div id="cachedescinfo" class="content-txtbox-noshade" style="padding-right: 25px;">

  <p><strong>Nombre:</strong> Ha cada cache se le da un nombre. Múltiples caches pueden tener el mismo nombre, pero se debe evitar esto. Buscando un nombre que tenga que ver con el cache -y evitar significativa denominaciones tales como &quot;A81 # 589&quot;. La única restricción en el nombre de la longitud, con un máximo de 60 caracteres están permitidos.</p>

  <p id="cachetype"><strong>Tipo de caches:</strong> Hay varios tipo de caches.
  <table class="table cachedesctable">
      <tr>
        <td valign="top"><img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/traditional.gif" width="32" border="0" height="32" align="left" alt="Normaler Cache" title="Normaler Cache" /></td>
        <td><span class="subtitle">Cache Tradicional</span>: Las coordenadas son la ubicación de la cache que está disponible como una caja de tupperware encuentralá y abrir el contenedor hay obsequios, etc.</td>
      </tr>
      <tr>
        <td valign="top"><img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/drivein.gif" width="32" border="0" height="32" align="left" alt="Drive-In" title="Drive-In" /></td>
        <td><span class="subtitle">Drive-In</span>: como un cache normal, pero hay muy cerca de un parking. Para encontrar el cache no se requerire ningún equipo especial.</td>
      </tr>
      <tr>
        <td valign="top"><img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/multi.gif" width="32" border="0" height="32" align="left" alt="Multi Cache" title="Multi Cache" /></td>
        <td><span class="subtitle">Multi-Cache</span>: Las coordenadas dadas no representan la ubicación de la cache, pero si un posible punto de partida para encontrar el caché. Esto puede lograrse mediante la resolución de problemas o rompecabezas, sino también por el encontrar estaciones o puntos intermedios. </td>
      </tr>
      <tr>
        <td valign="top"><img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/mystery.gif" width="32" border="0" height="32" align="left" alt="Rätsel Cache" title="Rätsel Cache" /></td>
        <td><p><span class="subtitle">Puzzle Cache</span>: Un puzzle  puede ser cualquier cache (normal, varios, etc virtual). La solución del puzzle te permitira encontrar el contenedor.</p></td>
      </tr>
      <tr>
        <td valign="top"><img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/mathe.gif" width="32" border="0" height="32" align="left" alt="Mathe-/Physikcache" title="Mathe-/Physikcache" /></td>
        <td><p><span class="subtitle">Matemática-/Físicache</span>: Para resolver el cache, tendra uno o más problemas de matemáticas o la física que ser resueltos.</p></td>
      </tr>
      <tr>
        <td valign="top"><img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/moving.gif" width="32" border="0" height="32" align="left" alt="Beweglicher Cache" title="Beweglicher Cache" /></td>
        <td><p><span class="subtitle">Cache Móvil</span>: Este tipo de cache está escondido, y a lo largo de su vida tendra varias nuevas ubicaciones e incluso tareas. Las nuevas coordenadas y la nueva tarea se publicará en la entrada del registro.</p></td>
      </tr>
      <tr>
        <td valign="top"><img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/virtual.gif" width="32" border="0" height="32" align="left" alt="Virtueller Cache" title="Virtueller Cache" /></td>
        <td><p><span class="subtitle">Cache Virtual</span>: El objetivo no es una caja de Tupperware y no hay ningún libro de registro. Dependiendo de la tarea, es necesario responder a una pregunta que propondra el propietario del cache o para hacer una foto para demostrar que ha estado en ese  sitio.</p></td>
      </tr>
      <tr>
        <td valign="top"><img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/webcam.gif" width="32" border="0" height="32" align="left" alt="Webcam" title="Webcam" /></td>
        <td><p><span class="subtitle">Webcam Cache</span>: A menudo se encuentra como la cámara web en las coordenadas y donde tendrá que tomarse una  imagen de sí mismo. La imagen se añade a la entrada del registro. No hay contenedores o un libro de registro.</p></td>
      </tr>
      <tr>
        <td valign="top"><img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/event.gif" width="32" border="0" height="32" align="left" alt="Event" title="Event" /></td>
        <td><p><span class="subtitle">Evento</span>: Reunión de geobuscadores..</p></td>
      </tr>
      <tr>
        <td valign="top"><img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/unknown.gif" width="32" border="0" height="32" align="left" alt="Unbekannter Cachetyp" title="Unbekannter Cachetyp" /></td>
        <td><p><span class="subtitle">Cache de tipo desconocido</span>: Todo lo que no encaja en los tipos de caché de otros.</p></td>
      </tr>
    </tbody>
  </table>
  </p>

  <p><strong>Coordenadas:</strong> posición de la cache, la zona de destino o el estacionamiento. Introduzca las coordenadas de la posición de cache.</p>
  <p><strong>País:</strong> ¿En qué país esta el cache? Especialmente importante es esta información en caches cercanos a las fronteras.</p>
  <p id="difficulty"><strong>Clasificación:</strong> dificultad y el terreno - en una escala de 1 a 5 se indican lo difícil que son las tareas, lo difícil que es un cache y en que terreno se encuentra.</p>
  <p><a name="time" id="time"></a><strong>Esfuerzo:</strong> ¿Cuánto tiempo tardaré en encontrar el cache desde el punto de partida?</p>

  <p>
		<strong>Atributos del Cache:</strong> los atributos donde se puede obtener más datos de la cache.
		{include file="articles_res_attributes.tpl"}
	</p>

  <p><strong>Resumen:</strong> En una frase breve una descripción del cache.</p>
  <p><strong>Descripción:</strong> Aquí debe especificar todos los posibles de lo necesario para encontrar el cache.</p>
  <p><strong>Nota/Ayuda/Hint:</strong> Ayuda o consejo para encontrar el cache.</p>
  <p><strong>Escondido desde:</strong> ¿Cuándo se oculta el cache? Si la fecha es en el futuro, ya que el cache no está disponible actualmente &quot;fuera&quot; - tiene que cambiar la situación, pero incluso entonces, cuando el cache en realidad oculta. En un Event Cache debe estar rodeado de la fecha en que se celebre la reunión.</p>
  <p><a name="logpw" id="logpw"></a><strong>Inicie sesión </strong></p>
  <p><strong>Condiciones:</strong> Se recomienda leer atentamente las presentes Condiciones. Hemos tratado esto y justo lo más corto posible.</p>
  <p><strong>Imágenes:</strong> Las imágenes se pueden cargar sólo si el cache ya se ha creado. Para hacer esto usted debe registrarse y abrir la página del cache. Entonces, justo encima de la &quot;Edición&quot;, haz clic en - a continuación encontrará lo que usted está buscando.</p>

</div>
<br />
