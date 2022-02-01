<?php

function bootstrap_italia_form_alter(&$form, $form_state, $form_id){
  if ( $form_id === 'user_login_form' ) {
      $form['name']['#description'] = 'Inserisci il tuo nome utente';
      $form['actions']['register'] = [
        '#type' => 'inline_template',
        '#template' => '<span style="margin-left: 15px;">Non sei registrato? <a href="/admin/config/gestioneutenti/creaUtente">Registrati ora</a> oppure <a href="/user/password" class="">Recupera la password</a></span>',
      ];
  }

  if($form_id === 'user_pass') {
      $form['actions']['submit']['#value'] = 'Invia';	
  }

  if($form_id === 'user_pass_reset' || $form_id === 'user_form' || $form_id === 'modificaoperatoreform_form') {
      $form['#prefix'] = '<div class="container pl-4 pr-4 mt-5 mb-5">';
      $form['#suffix'] = '</div>';
  }
}
