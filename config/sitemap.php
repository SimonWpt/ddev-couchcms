<?php require_once( 'couch/cms.php' ); ?>
<cms:ignore>
	The sitemap is made by Bartonsweb, the code is taken from
https://www.couchcms.com/forum/viewtopic.php?f=8&t=11000#
</cms:ignore>
<cms:content_type 'text/xml' /><cms:concat '<' '?xml version="1.0" encoding="' k_site_charset '"?' '>' />
<cms:template title='Sitemap' parent='_modules_' >
	<cms:editable type='group' name='pages' label='Pages' >
		<cms:templates show_hidden='1' order='asc'>
			<cms:if k_template_is_executable='1'>
				<cms:editable type='radio' name="tpl_<cms:show k_template_id />_page" opt_values='In sitemap=1 | Not in sitemap=0' label="<cms:show k_template_title />" group='pages' opt_selected = '1' desc="<cms:show k_template_link />" />
			</cms:if>
		</cms:templates>
	</cms:editable>
	<cms:editable type='group' name='post_types' label='Post Types'>
		<cms:templates show_hidden='1' order='asc'>
			<cms:if k_template_is_clonable='1' && k_template_is_executable='1'>
				<cms:editable type='radio' name="tpl_<cms:show k_template_id />" opt_values='In sitemap=1 | Not in sitemap=0' label="<cms:show k_template_title /> Posts" group='post_types' opt_selected = '1' />
				<cms:if k_template_has_dynamic_folders = '1'>
					<cms:editable type='radio' name="tpl_<cms:show k_template_id />_folder" opt_values='In sitemap=1 | Not in sitemap=0' label="<cms:show k_template_title /> - Folder Views" group='post_types' opt_selected = '0' />
				</cms:if>
			</cms:if>
		</cms:templates>
	</cms:editable>
	<cms:editable type='group' name='post_excludes' label='Exclude Posts'>
		<cms:editable type='text' name='excluded_posts' label='Exclude Posts' desc='You can exclude posts by entering a comma separated string. E.g.: 1,2,99,100' validator="regex=/^\d+(,\d+)*$/"/>
	</cms:editable>
</cms:template>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	  xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
            

   <cms:templates show_hidden='1' order='asc' > 
		<cms:if k_template_is_clonable="1" && k_template_is_executable="1">
			<cms:if "<cms:get "tpl_<cms:show k_template_id />_page" />">
				<url>
					<loc><cms:show k_template_link /></loc>
					<lastmod>
						<cms:pages limit='1' masterpage=k_template_name orderby='modification_date'>
							<cms:date "<cms:if k_page_modification_date='0000-00-00 00:00:00'><cms:show k_page_date /><cms:else /><cms:show k_page_modification_date /></cms:if>" format='Y-m-d' />
						</cms:pages>
					</lastmod>
					<changefreq>daily</changefreq>
				</url>
			</cms:if>
			<cms:if "<cms:get "tpl_<cms:show k_template_id />" />">
					<cms:pages masterpage=k_template_name>
						<cms:php>
							global $CTX;
							$array = array(<cms:show excluded_posts />);
							$post_id = $CTX->get( 'k_page_id' );
							$CTX->set ( 'excluded', '0' );
							if (in_array($post_id, $array)) { $CTX->set ( 'excluded', '1' );}
						</cms:php>
						<cms:if excluded='0'>
							 <url>
								<loc><cms:show k_page_link /></loc>
								<lastmod>
									<cms:date "<cms:if k_page_modification_date='0000-00-00 00:00:00'><cms:show k_page_date /><cms:else /><cms:show k_page_modification_date /></cms:if>" format='Y-m-d' />
								</lastmod>
								<changefreq>daily</changefreq>
							 </url>
						 </cms:if>
					</cms:pages>
			</cms:if>
			<cms:if "<cms:get "tpl_<cms:show k_template_id />_folder" />">
				<cms:folders masterpage=k_template_name>
						 <url>
							<loc><cms:show k_folder_link /></loc>
							<lastmod>
								<cms:pages limit='1' masterpage=k_template_name orderby='modification_date'>
									<cms:date "<cms:if k_page_modification_date='0000-00-00 00:00:00'><cms:show k_page_date /><cms:else /><cms:show k_page_modification_date /></cms:if>" format='Y-m-d' />
								</cms:pages>
							</lastmod>
							<changefreq>daily</changefreq>
						 </url>
				</cms:folders>
			</cms:if>
		<cms:else />
			<cms:if k_template_is_executable='1'>
				<cms:if "<cms:get "tpl_<cms:show k_template_id />_page" />">
					<url>
						<loc><cms:show k_template_link /></loc>
						<lastmod>
							<cms:query sql="SELECT p.modification_date FROM couch_pages p WHERE p.template_id = '<cms:show k_template_id/>'">
								<cms:date "<cms:if modification_date='0000-00-00 00:00:00'><cms:show k_page_date /><cms:else /><cms:show modification_date /></cms:if>" format='Y-m-d' />
							</cms:query>
						</lastmod>
						<changefreq>daily</changefreq>
					</url>
				</cms:if>
			</cms:if>
		</cms:if>
   </cms:templates>
</urlset>
<?php COUCH::invoke(); ?>