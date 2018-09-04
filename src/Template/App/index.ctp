<?php

use Cake\Core\Configure;

echo $this->assign('title', $title = 'Password Importer');
echo $this->Html->css('themes/default/api_main.min.css?v=' . Configure::read('passbolt.version'), ['block' => 'css', 'fullBase' => true]);
echo $this->Html->css('https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');
echo $this->Html->css('PassboltPasswordImporter.style');

?>


<?php
echo $this->element('Header/meta');
echo $this->element('Navigation/default');

echo $this->element('PassboltPasswordImporter.import_form');


echo $this->element('Footer/default');
echo $this->Html->script('https://code.jquery.com/jquery-3.3.1.min.js');
echo $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js');
echo $this->Html->script('https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js');
echo $this->Html->script('PassboltPasswordImporter.script');
?>
