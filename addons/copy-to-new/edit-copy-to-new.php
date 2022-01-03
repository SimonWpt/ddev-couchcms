<?php
    if ( !defined('K_ADMIN') ) die(); // cannot be loaded directly

    require_once( K_COUCH_DIR.'edit-pages.php' );

    class KCopyToNewAdmin extends KPagesAdmin{

        function __construct(){
            parent::__construct();
        }

        /////// 1. 'form' action  ////////////////////////////////////////////////////
        function _get_form_redirect_link( &$pg, $_mode ){
            global $FUNCS;

            // this function gets called when page is successfuly saved.
            // raise an event for fields that might demand special treament.
            if( $_mode=='create' ){
                $orig_page_id = $FUNCS->current_route->resolved_values['id'];

                // HOOK: copy_to_new_complete
                $FUNCS->dispatch_event( 'copy_to_new_complete', array(&$pg, $orig_page_id) );
            }

            // default action of this function
            return parent::_get_form_redirect_link( $pg, $_mode );
        }

        // route filters
        static function resolve_page( $route, $act ){
            global $FUNCS, $DB, $PAGE, $CTX;

            $rs = $DB->select( K_TBL_TEMPLATES, array('*'), "name='" . $DB->sanitize( $route->masterpage ). "'" );
            if( !count($rs) ){
                return $FUNCS->raise_error( ROUTE_NOT_FOUND );
            }
            $tpl = $rs[0];

            $tpl_id = $tpl['id'];
            $tpl_name = $tpl['name'];
            $page_id = $route->resolved_values['id'];
            $nonce = $route->resolved_values['nonce'];

            // validate
            $FUNCS->validate_nonce( 'edit_page_' . $page_id, $nonce );

            // get page ..
            if( !$_POST ){
                $pg = new KWebpage( $tpl_id, $page_id );
                if( $pg->error ){
                    return $FUNCS->raise_error( ROUTE_NOT_FOUND );
                }

                // and clone it (in a hackish way) ..
                $pg->id = -1;
                $pg->page_name = $name;
                for( $x=0; $x<count($pg->fields); $x++ ){
                    $f = &$pg->fields[$x];
                    $f->page_id = $pg->id;
                    $f->modified = 1;
                    if( $f->name=='k_page_name' ){
                        $f->data='';
                    }
                    unset( $f );
                }
            }
            else{
                $pg = new KWebpage( $tpl_id, -1 );
                if( $pg->error ){
                    return $FUNCS->raise_error( ROUTE_NOT_FOUND );
                }
            }

            // set cloned page as the page object to edit
            $PAGE = $pg;
            $PAGE->folders->set_sort( 'weight', 'asc' );
            $PAGE->folders->sort( 1 );
            $PAGE->set_context();
        }
    } // end class KCopyToNewAdmin
