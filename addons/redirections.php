<?php require_once("couch/cms.php"); ?>
<cms:template title='Redirections' clonable='0' executable='0' parent='_modules_' order='100'>

    <cms:repeatable name='redirections' label='Redirections' order='-1'>
        <cms:editable name='match' label='Match' type='dropdown' opt_values='Simple=simple | RegEx=regex' opt_selected='simple' col_width='100' />
        <cms:editable name='uri' label='URI' type='text' required='1' validator='kredirector::validate_match' />
        <cms:editable name='redirect' label='Redirect' type='dropdown' opt_values='Temporary=temporary | Permanent=permanent' opt_selected='temporary' col_width='120' />
        <cms:editable name='to' label='To' type='text' required='1' validator='regex=/^(http|\/)/i' validator_msg="regex=URL should begin with either '/' or 'http'" separator='#'/>
        <cms:editable name='skip_qs' label='Skip QS?' type='checkbox' opt_values='Yes=yes' col_width='90' />
    </cms:repeatable>

</cms:template>
<?php COUCH::invoke(); ?>
