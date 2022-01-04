<?php
if (!defined('K_COUCH_DIR')) {
    die();
} // cannot be loaded directly

require_once(K_COUCH_DIR . 'addons/bootstrap-grid/bootstrap-grid.php');
require_once(K_COUCH_DIR . 'addons/phpmailer/phpmailer.php');
require_once(K_COUCH_DIR . 'addons/tiny-html-minifier/TinyMinify.php');

/*
 * Tweaking Ckeditor
 * see https://simonwpt.github.io/CouchDocs/concepts/tweaking-ckeditor.html
 *
 * ie. <cms:editable type='richtext' name='my_headline' label='Headline' toolbar='custom'
 *      custom_toolbar='bold, italic, strike' class='ck-enter-br' />

$FUNCS->add_event_listener('ckeditor_alter_config', function (&$config, $f) {
    $classes = explode(' ', $f->class);
    if (count($classes)) {
        $arr_enter = array('ck-enter-p' => 'CKEDITOR.ENTER_P', 'ck-enter-br' => 'CKEDITOR.ENTER_BR', 'ck-enter-div' => 'CKEDITOR.ENTER_DIV');
        $arr_shift_enter = array('ck-shiftenter-p' => 'CKEDITOR.ENTER_P', 'ck-shiftenter-br' => 'CKEDITOR.ENTER_BR', 'ck-shiftenter-div' => 'CKEDITOR.ENTER_DIV');

        foreach ($classes as $class) {
            if (array_key_exists($class, $arr_enter)) {
                $config['enterMode'] = $arr_enter[$class];
            }

            if (array_key_exists($class, $arr_shift_enter)) {
                $config['shiftEnterMode'] = $arr_shift_enter[$class];
            }
        }
    }
});
*/

/*
 * Tweaking navigation
 * see https://simonwpt.github.io/CouchDocs/tutorials/tweaking-navigation.html
 *
 * i.e <cms:template title='Historie' parent='about-us' icon='people' />
 *
$FUNCS->add_event_listener( 'register_admin_menuitems', 'my_register_admin_menuitems' );

function my_register_admin_menuitems(){
    global $FUNCS;

    $FUNCS->register_admin_menuitem( array('name'=>'about-us', 'title'=>'Über uns', 'is_header'=>'1', 'weight'=>'0')  );
    $FUNCS->register_admin_menuitem( array('name'=>'services', 'title'=>'Angebote', 'is_header'=>'1', 'weight'=>'0')  );
    $FUNCS->register_admin_menuitem( array('name'=>'contact', 'title'=>'Kontakt', 'is_header'=>'1', 'weight'=>'0')  );

}
*/