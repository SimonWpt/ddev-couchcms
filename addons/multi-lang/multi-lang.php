<?php
    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

    if( !defined('K_ADMIN') ){
        class MultiLang{

            private $supported_langs = array(); // all accepted languages
            private $exclude_templates = array();
            private $use_prettyurls = 0;
            private $use_browser_lang = 0;

            var $lc = ''; // language code

            function __construct(){
                global $FUNCS;

                // get the supported languages..
                $this->populate_config();
                if( !is_array($this->supported_langs) || !count($this->supported_langs) ) return; // nothing to do

                // initialize the language code variable
                $lc = "";
                if( isset($_GET['lc']) ){
                    $lc = trim( $_GET['lc'] );
                }

                if( $lc=='' && $this->use_browser_lang && isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ){
                   $lc = strtolower( substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) );
                }

                if( !array_key_exists($lc, $this->supported_langs) ){
                    reset( $this->supported_langs );
                    $lc = key($this->supported_langs); // first language is the default
                }
                $this->lc = $lc;

                // hook into events
                $FUNCS->add_event_listener( 'alter_page_set_context', array($this, 'alter_page_context') );
                $FUNCS->add_event_listener( 'alter_tag_link_execute', array($this, 'alter_link_tag') ); // replace <cms:link /> tag
                $FUNCS->add_event_listener( 'alter_tag_route_link_execute', array($this, 'alter_link_tag') ); // replace <cms:route_link /> tag
                if( $this->use_prettyurls ){
                    //$FUNCS->add_event_listener( 'pre_process_page', array($this, 'ignore_canonical_url') );
                    $FUNCS->add_event_listener( 'get_url', array($this, 'get_url') );
                    $FUNCS->add_event_listener( 'alter_rewrite_rules', array($this, 'alter_rewrite_rules') );
                }
                $FUNCS->add_event_listener( 'alter_final_page_output', array($this, 'check_url') );
                $FUNCS->add_event_listener( 'skip_qs_params_in_paginator', array($this, 'skip_qs_params') );

                // tags
                $FUNCS->register_tag( 'show_with_lc', array($this, 'show_with_lc_handler') ); // <cms:show_with_lc /> tag
            }

            private function populate_config(){
                if( file_exists(K_COUCH_DIR.'addons/multi-lang/config.php') ){
                    $cfg = array();

                    require_once( K_COUCH_DIR.'addons/multi-lang/config.php' );

                    if( is_array($cfg['langs']) && count($cfg['langs']) ){
                        foreach( $cfg['langs'] as $k=>$v ){
                            $k = strtolower( trim($k) );
                            if( strlen($k) ){
                                $this->supported_langs[$k] = trim( $v );
                            }
                        }
                    }

                    if( is_array($cfg['exclude']) && count($cfg['exclude']) ){
                        $this->exclude_templates = array_filter( array_map("trim", $cfg['exclude']) );
                    }

                    $this->use_prettyurls = ( K_PRETTY_URLS && $cfg['prettyurls'] ) ? 1 : 0;
                    $this->use_browser_lang = ( $cfg['use_browser_lang'] ) ? 1 : 0;

                    unset( $cfg );
                }
            }

            function alter_page_context( &$vars ){
                global $FUNCS, $CTX;
                static $init=0;

                $tpl_name = $vars['k_template_name'];
                $page_link = $vars['k_page_link'];

                if( !$init ){
                    // set it as a Couch variable
                    $CTX->set( 'k_lang', $this->lc, 'global' );

                    // set matching links of the other languages
                    foreach( $this->supported_langs as $k=>$v ){
                        if( !in_array($tpl_name, $this->exclude_templates) ){
                            $CTX->set( 'k_link_'.$k, $this->_add_lc($page_link, $k), 'global' );
                        }
                        else{
                            $CTX->set( 'k_link_'.$k, $page_link, 'global' );
                        }
                    }

                    $CTX->set( 'k_supported_langs', $this->supported_langs, 'global' );
                    $init=1;
                }

                // add 'lc' to page_link
                $vars['k_template_is_multi_lingual'] = ( in_array($tpl_name, $this->exclude_templates) ) ? '0' : '1';
                $vars['k_page_orig_link'] = $page_link;
                if( !in_array($tpl_name, $this->exclude_templates) ){
                    $vars['k_page_link'] = $this->_add_lc( $page_link );
                }
            }

            function alter_link_tag( $tag_name, &$params, &$node, &$html ){
                global $FUNCS, $TAGS, $CTX;

                extract( $FUNCS->get_named_vars(
                                array(
                                       'masterpage'=>'',
                                      ),
                                $params)
                           );

                // sanitize params
                $masterpage = trim( $masterpage );
                if( $masterpage=='' ){ return; } // No masterpage, no link

                if( in_array($masterpage, $this->exclude_templates) ){ return; }

                // call original tag
                if( $tag_name=='link' ){ //<cms:link />
                    $html = $TAGS->link( $params, $node );
                }
                else{ //<cms:route_link />
                    global $KROUTES;
                    $html = $KROUTES->process_route_link( $params, $node );
                }

                // add 'lc' to the returned link
                $html = $this->_add_lc( $html );

                return 1; // skip original tag code
            }

            function ignore_canonical_url( &$html, &$pg, &$ignore_canonical_url ){
                if( !in_array($pg->tpl_name, $this->exclude_templates) ){
                    $ignore_canonical_url = 1;
                }
            }

            function check_url( &$html, &$pg, &$k_cache_file, &$redirect_url ){
                global $FUNCS;

                if( in_array($pg->tpl_name, $this->exclude_templates) ) return;

                // add lc to URL ..
                if( $redirect_url ){
                    $redirect_url = $this->_add_lc( $redirect_url );
                }
                elseif( $_SERVER['REQUEST_METHOD']!='POST' ){
                    $lc_in_url = ( $this->use_prettyurls ) ? preg_match("/(\?|&)lc=/i", $_SERVER['REQUEST_URI']) : 0;

                    $lc = trim( $_GET['lc'] );
                    if( ($lc != $this->lc || $lc_in_url) && !is_null($pg->link) ){
                        $cur_link = $FUNCS->get_qs_link( K_SITE_URL . $pg->link );
                        $redirect_url = $this->_add_lc( $cur_link );
                    }
                }
            }

            function get_url( &$url ){
                global $PAGE;

                // remove the lc from URL ..
                if( !in_array($PAGE->tpl_name, $this->exclude_templates) ){
                    $url = $this->_lc_to_prettyurl( $url, $this->lc, 1 /*remove*/ );
                }
            }

            function skip_qs_params( &$arr_skip_qs ){
                $arr_skip_qs[] = 'lc';
            }

            function alter_rewrite_rules( $tpl, &$val, &$rules, &$rules_index ){
                if( in_array($tpl, $this->exclude_templates) ) return;

                $rules_index = array();
                $rules = array();

                $lc = ''; // e.g. 'en|pt';
                $sep = '';
                foreach( $this->supported_langs as $k=>$v ){
                    $lc .= $sep.$k;
                    $sep = '|';
                }

                // Redirect if not trailing slash
                if( strlen($val['pretty_name']) ){
                    //RewriteRule ^(?:en|pt)/news/test$ "$0/" [R=301,L,QSA]
                    $rules[] = 'RewriteRule ^(?:'.$lc.')/'.substr( $val['pretty_name'], 0, strlen($val['pretty_name'])-1 ).'$ "$0/" [R=301,L,QSA]';
                }
                else{
                    //RewriteRule ^(?:en|pt)$ "$0/" [R=301,L,QSA]
                    $rules[] = 'RewriteRule ^(?:'.$lc.')$ "$0/" [R=301,L,QSA]';
                }

                // Home
                //RewriteRule ^(en|pt)/news/test/$ news/test.php?lc=$1 [L,QSA]
                $rules[] = 'RewriteRule ^('.$lc.')/'.$val['pretty_name'].'$ '.$val['name'].'?lc=$1 [L,QSA]';

                if( !$val['has_custom_routes'] ){
                    // Page
                    //RewriteRule ^(en|pt)/news/test/.*?([^\.\/]*)\.html$ news/test.php?pname=$2&lc=$1 [L,QSA]
                    $rules[] = 'RewriteRule ^('.$lc.')/'. $val['pretty_name'].'.*?([^\.\/]*)\.html$ '.$val['name'].'?pname=$2&lc=$1 [L,QSA]';

                    // Archives
                    //RewriteRule ^(en|pt)/news/test/([1-2]\d{3})/(?:(0[1-9]|1[0-2])/(?:(0[1-9]|1[0-9]|2[0-9]|3[0-1])/)?)?$ news/test.php?d=$2$3$4&lc=$1 [L,QSA]
                    $rules[] = 'RewriteRule ^('.$lc.')/'.$val['pretty_name'].'([1-2]\d{3})/(?:(0[1-9]|1[0-2])/(?:(0[1-9]|1[0-9]|2[0-9]|3[0-1])/)?)?$ '.$val['name'].'?d=$2$3$4&lc=$1 [L,QSA]';

                    // Folder
                    //RewriteRule ^(en|pt)/news/test/[^\.]*?([^/\.]*)/$ news/test.php?fname=$2&lc=$1 [L,QSA]
                    $rules[] = 'RewriteRule ^('.$lc.')/'.$val['pretty_name'].'[^\.]*?([^/\.]*)/$ '.$val['name'].'?fname=$2&lc=$1 [L,QSA]';

                    // Folder redirect if not trailing slash
                    //RewriteRule ^(?:en|pt)/news/test/[^\.]*?([^/\.]*)$ "$0/" [R=301,L,QSA]
                    //RewriteRule ^(?:en|pt)/\w[^\.]*?([^/\.]*)$ "$0/" [R=301,L,QSA]
                    $n = (strlen($val['pretty_name'])) ? $val['pretty_name'] : '\w';
                    $rules[] = 'RewriteRule ^(?:'.$lc.')/'.$n.'[^\.]*?([^/\.]*)$ "$0/" [R=301,L,QSA]';
                }
                else{
                    //RewriteRule ^(en|pt)/news/test/(+*?)$ news/test.php?q=$2&lc=$1 [L,QSA]
                    $rules[] = 'RewriteRule ^('.$lc.')/'. $val['pretty_name'].'(.+?)$ '.$val['name'].'?q=$2&lc=$1 [L,QSA]';
                }
            }

            function show_with_lc_handler( $params, $node ){ // tag
                global $FUNCS, $CTX;

                return $this->_add_lc( trim($params[0]['rhs']) );
            }

            function _add_lc( $link, $lc='' ){
                if( $lc=='' ){ $lc = $this->lc; }
                $link = trim( $link );

                if( $link!='' && strpos($link, K_SITE_URL)===0 ){ // make sure it is an internal link..
                    if( $this->use_prettyurls ){
                        $link = $this->_lc_to_prettyurl( $link, $lc );
                    }
                    else{
                        if( strpos($link, 'lc=')===false ){
                            $sep = ( strpos($link, '?')===false ) ? '?' : '&';
                            $link .= $sep . 'lc='.$lc;
                        }
                    }
                }
                return $link;
            }

            private function _lc_to_prettyurl( $link, $lc, $remove=0 ){
                $path = substr( $link, strlen(K_SITE_URL) );
                $prefix = substr($path, 0, 3);

                if( $remove ){
                    if( $prefix=="$lc/" ){
                        $path = substr( $path, 3 );
                    }
                }
                else{
                    $sep = $prefix[2];
                    $prefix = rtrim( $prefix, '/' );
                    if( !($sep=='/' && array_key_exists($prefix, $this->supported_langs)) ){
                        $path = $lc . '/' . $path; // add if not already present
                    }
                }
                $link =  K_SITE_URL . $path;

                return $link;
            }

        }// end class MultiLang

        $MultiLang = new MultiLang();
    }
