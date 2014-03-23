<?php
//<form action="admin-post.php" method="post" name="adduser_form" id="adduser_form" class="grid-form validate">

function solis_add_user_draw_form(){
?>
<form name="democracy_adduser_form" id="democracy_adduser_form" class="grid-form">

	<?php wp_nonce_field( 'solidarnost-add-user-nonce' ); ?>
<input type="hidden" name="action" value="solidarnost_submit" />
	<fieldset>
		<legend>Osebni podatki</legend>
		<div data-row-span="5">
			<div data-field-span="1" id="first_name-span">
				<label class="required">Ime</label><input type="text" id="first_name" name="first_name" required onblur="validate('first_name');">
			</div>
			<div data-field-span="1" id="last_name-span">
				<label class="required">Priimek</label><input type="text" id="last_name" name="last_name" required onblur="validate('last_name');">
			</div>
			<div data-field-span="1">
				<label>Rojstni datum (mm/dd/llll)</label>
				<input type="text" id="birthdate" name="birthdate">
			</div>
			<div data-field-span="1">
				<label>Spol M/Ž</label>
				<input type="text" id="gender" name="gender">
			</div>
			<div data-field-span="1">
				<label>Članska št.</label>
				<input type="text" id="member_id" name="member_id" value="<?php echo get_max_member_id()+1; ?>">
			</div>
		</div>
	</fieldset>
	<br><br>
	<fieldset>
		<legend>Elektronska identiteta uporabnika</legend>
		<div data-row-span="2">
			<div data-field-span="1">
				<label class="required">Uporabniško ime</label><input type="text" name="username" id="username">
<p name="userprev" id="userprev" class="button button-primary" onClick="solis_load_user_pn(-1);">Prejsnji</p>
<p name="userbyusername" id="userbyusername" class="button button-primary" onClick="solis_load_user('username');">Nalozi uporabnika</p>
<p name="usernext" id="usernext" class="button button-primary" onClick="solis_load_user_pn(1);">Naslednji</p>
				<br><br><br>
			</div>
			<div data-field-span="1">
				<label>Uporabnikove želje</label>
				<label><input type="checkbox" id="notifications" name="notifications" checked="true" class='cbox'>Želim prejemati obvestila (spletni časopis, novice)</label>
	<br>
				<label><input type="checkbox" id="enableduser" name="enableduser" class='cbox' checked='true'>Se lahko prijavim v portal (sem aktiven član) kot <select name="role" id="role">
			<?php wp_dropdown_roles( get_option('default_role') ); ?>
			</select></label>

				<br><label><input type="checkbox" id="signed" name="signed" checked="true" class='cbox'>Sem podpisal pristopno izjavo</label>
			</div>
			
	</fieldset><br><br>

	<fieldset>
		<legend>Prebivališče</legend>
		<div data-row-span="5">
			<div data-field-span="2">
				<label>Stalno prebivališče</label><input type="text" id="address" name="address">
			</div>
			<div data-field-span="1" id="postcode-span">
				<label>Poštna številka</label><input type="text" name="postcode" id="postcode" onblur="validate('postcode');">
			</div>
			<div data-field-span="2">
				<label>Pošta</label><input type="text" name="postname" id="postname">
			</div>
		</div>
		<div data-row-span="2">
			<div data-field-span="1">
				<label>Občina</label><input type="text" id="municipality" name="municipality">
			</div>
			<div data-field-span="1">
				<label>Volilna enota</label><input type="text" id="voting_unit" name="voting_unit">
			</div>

		</div>
	</fieldset>
	<br><br>
	<fieldset>
		<legend>Kontaktni podatki</legend>
		<div data-row-span="2">
			<div data-field-span="1">
				<label>Telefonska številka</label><input type="text" id="phone" name="phone">
			</div>
			<div data-field-span="1">
				<label>GSM</label><input type="text" id="gsm" name="gsm">
			</div>
		</div>
		<div data-row-span="1">
			<div data-field-span="1" id="email-span">
				<label>Naslov elektronske pošte</label><input type="text" name="email" id="email" onblur="validate('email');">
			</div>
		</div>
	</fieldset>
	<br><br>
	<fieldset>
		<legend>Zaposlitveni podatki</legend>
		<div data-row-span="2">
			<div data-field-span="1">
				<label>Izobrazba</label><?php
					wp_dropdown_categories('show_option_none=NiPodatka&show_count=0&hide_empty=0&orderby=name&echo=1&taxonomy=soledu&name=education&hierarchical=1');
					?>
			</div>
			<div data-field-span="1">
				<label>Poklic</label><input type="text" id="occupation" name="occupation">
			</div>
		</div>	
		<div data-row-span="1">
			<div data-field-span="1">
				<label>Zaposlen pri</label><input type="text" id="employer" name="employer">
			</div>
		</div>	
	</fieldset>
	<br><br>
	<fieldset>
		<legend>Podatki o strankarski aktivnosti</legend>
		<div data-row-span="3">
			<div data-field-span="1">
				<label>Želim pomagati pri</label>
				<?php

					$taxonomy='solcomp';
					$options=array('orderby'=>'name', 'order'=> 'ASC' ,'hide_empty' => false );
					$terms=get_terms($taxonomy,$options);
					$no=count($terms);
					$columns=3;
					$per_col=round(($no+$columns)/$columns);
					$cnt=0;

					foreach($terms as $term){
//						$id='comp-'.$term->term_id;
						$id=$term->slug;
						$name=$term->name;
						echo "<label class='solfunc'><input type='checkbox' name='$id' id='$id' class='cbox'>$name</label>  ";
							$cnt++;
						if($cnt==$per_col){
							echo "</div><div data-field-span='1'><label>Želim pomagati pri</label>";
							//new column
							$cnt=0;
						}

					}	
				?>
<?php /*
						<label class='solfunc'><input type='checkbox' name='comp-other1' id='comp-other1'>Drugo: <input type="text" name="comp-other1-text"></label>
						<label class='solfunc'><input type='checkbox' name='comp-other2' id='comp-other2'>Drugo: <input type="text" name="comp-other2-text"></label>
						<label class='solfunc'><input type='checkbox' name='comp-other2' id='comp-other2'>Drugo: <input type="text" name="comp-other3-text"></label>
*/ ?>
			</div>
		</div>
		<div data-row-span="2">
			<div data-field-span="1">
				<label>Vloga v stranki</label>
				<?php

					$taxonomy='solfunc';
					$options=array('orderby'=>'name', 'order'=> 'ASC' ,'hide_empty' => false );
					$terms=get_terms($taxonomy,$options);
					foreach($terms as $term){
						//$id='solfunc-'.$term->term_id;
						$id=$term->slug;
						$name=$term->name;
						echo "<label class='solfunc'><input type='checkbox' name='$id' id='$id' class='cbox'>$name</label>  ";

					}
				?>
			</div>
			<div data-field-span="1">
				<label>Delovne skupine</label>
				<?php

					$taxonomy='solwg';
					$options=array('orderby'=>'name', 'order'=> 'ASC' ,'hide_empty' => false );
					$terms=get_terms($taxonomy,$options);
					$no=count($terms);
					$columns=3;
					$per_col=round($no/$columns);
					$cnt=0;
					foreach($terms as $term){
						//$id='solwg-'.$term->term_id;
						$id=$term->slug;
						$name=$term->name;
						echo "<label class='solfunc'><input type='checkbox' name='$id' id='$id' class='cbox'>$name</label>  ";
						$cnt++;
						if($cnt==$per_col){
							//new column
							$cnt=0;
						}

					}
				?>
			</div>
		</div>	
	
	</fieldset>
	<br><br>
</form>
<button name="sform" id="sform" class="button button-primary" onClick="solidarnost_submit();">Dodaj novega uporabnika</button>
<button name="debug" id="debug" class="button button-primary" onClick="solis_fill_in_form(1);">Nalozi uporabnikove nastavitve</button>
<?php

}

?>
