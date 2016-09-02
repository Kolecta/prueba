<?php
  require_once("access.php");
  $mensajeError = "";
  $lista = parametroEntrada("li");
  $busca = parametroEntrada("busca");
  $buscak = parametroEntrada("buscak");
  $avisopromo = false;
  $euros_bonificacion = 0.0;
  $bote = new TBote();
  try{
    if ((! $kolectaBD->cargarBote((int)parametroEntrada("idbote"), $bote)) ||
        ($bote->getIdOrganizador() <> $usuario->getId())) {
      throw new Exception("No se localiza el bote.");
    } else if ($bote->getEstado() == BOTE_ESTADO_PENDIENTE){
      throw new Exception("No se puede cobrar el bote si no está cerrado.");
    } else if ($bote->getEstado() == BOTE_ESTADO_COBRADO){
      throw new Exception("No se puede cobrar el bote porque ya fué cobrado.");
    } else if (! $bote->getIdUsuarioRegaloIsNull()){
      throw new Exception("No se puede regalar el bote porque ya fué regalado.");
	} else if ($bote->codpromo != "") {
		$avisopromo = $kolectaBD->IncumplePromo($bote, $euros_bonificacion);
	}
  }catch (Exception $e) {
    $mensajeError = $e->getMessage();
   }
  if ($mensajeError <> "") 
    header ("Location: kolectaerror.php" );
	//echo $mensajeError;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<?php
  include("plts03/scriptredirect.php");
  $claim = '¡INDICA A QUIEN REGALAS EL BOTE!';
  $pagina = 'Mis Kolectas';
  $title = 'Mis Kolectas';
  include("head3.php");
?>
<body>
	
<?php
  include("cabecera-usuario.php");
?>
<main class="regalar_bote">
	<div class="regalar_bote_content">
		<dl id="info_sup" class="info_sup">
			<dt>
				Kolecta:
			</dt>
			<dd>
				<?php echo htmlspecialchars($bote->nombre); ?>
			</dd>

			<dt>
				Importe:
			</dt>
			<dd>
				<?php echo $bote->formatCurrency($bote->recaudado()); ?> €
			</dd>

			<dt>
				Fecha Límite:
			</dt>
			<dd>
				<?php echo $bote->getLimite()->format("d/m/Y"); ?>
			</dd>
		</dl>
		<div id="info_inf" class="info_inf">
			<div class="info_comentario_adi">
				<p>	
					Añade <b class="upperCase">el email del beneficiario</b> y haz click en “Regalar”
				</p>
			</div>
			<div class="escritura_emails">
				<h4>Email</h4>
				<form name="frm_email" method="post" action="regala-amigo.php" id="email_a_regalar">
				  <input type="hidden" name="idbote" value="<?php echo $bote->getId(); ?>">		      
				  <textarea id="emailrel" name="email" class="textArea_emailrel" placeholder="Escribe la dirección de e-mail del amigo para regalarle la KOLECTA"></textarea>
					<div id="aclaracion_regalar">
				      	<a href="#" onclick="return apuntaemail(document.frm_email);">
				      		Regala a este amigo la kolecta
				  		</a>
				  </div>
				</form>
			</div>

			<ul class="menupest">
				<?php
					if ($lista == '') { echo '<li class="selected">'; } else { echo '<li>'; }
				?>
				        <a href="regalar-bote.php?idbote=<?php echo $bote->getId(); ?>">
				        	<img src="img/bot.facebook.png" width="16" height="16" align>
				        </a>
			        </li>
				<?php
					if ($lista == 'k') { echo '<li class="textelem selected">'; } else { echo '<li class="textelem">'; }
				?>
		        		<a href="regalar-bote.php?idbote=<?php echo $bote->getId(); ?>&li=k">Kontactos</a>
		        	</li>
				<!--<li><a href="regalar-bote.php?idbote=<?php //echo $bote->getId(); ?>"><img src="img/gmail.16.png" width="45" height="16"></a></li>-->
			</ul>

			<?php
				if ($lista == '') { require_once("plts02/contactos-fb-r.php"); } 
				else { require_once("plts02/contactos-k-r.php"); }  
			?>	
		</div>
		<aside class="promocion_cumples">
			<?php
				if ($avisopromo)
					require_once("plts02/avisoincumplepromo.php");
			?>	
		</aside>
		<div class="cb"></div>
	</div>
</main>


<?php
  include("pie.php");
?>
<script language="javascript"> 
  function apuntaemail(componente){
    aux=document.getElementById("emailrel").value;
    alert(aux);
    if (aux.indexOf('@') != -1) {
      if(confirm('¿Estás seguro de que quieres regalar tu Kolecta?. Si lo haces sólo el destinatario podrá cobrarla.')) {
	    componente.submit();
	  } 
	} else {
	  alert('Debes indicar el email de la persona a la que quieres regalar tu Kolecta.');
	}
    return false;
   }
  function regalacontacto(componente){
    if(confirm('¿Estás seguro de que quieres regalar tu Kolecta?. Si lo haces sólo el destinatario podrá cobrarla.')) {
	    componente.submit();
	 } 
    return false;
   }
</script>  
</body>
</html>
