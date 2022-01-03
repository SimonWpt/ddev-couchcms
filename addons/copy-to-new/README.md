# copy-to-new
A long standing demand has been for a way to create new pages by copying data from existing pages (useful if the data does not change too much between pages or, perhaps, to create sample pages to fill a new site).

Attached is an addon that should help with this.

**Installation:**  
Installation follows the regular method -
1. Extract the folder named `copy-to-new` from the attached zip and place it in `couch/addons` folder.
2. Activate the addon by adding the following entry in `couch/addons/kfunctions.php` file (if this file is not present, rename `kfunctions.example.php` to `kfunctions.php`) -
```
require_once( K_COUCH_DIR.'addons/copy-to-new/copy-to-new.php' );
```

**Usage:**  
Within the 'copy-to-new' folder we added to addons above, you'll find a `config.example.php` file.  
Rename it to `config.php` and open it in your text editor. Add to the following setting the name(s) of the templates you'd like to add this copy to new page feature -
```
$cfg['tpls'] = '';
``` 
For example, if you wanted to add it to `portfolio.php`, the setting will become -
```
$cfg['tpls'] = 'portfolio.php';
```
Multiple templates may be added by separating them with a pipe (i.e. '|') character e.g.
```
$cfg['tpls'] = 'portfolio.php | contacts.php ';
```  
And now when you wish to create a new page copying data from an existing page (of the templates specified in config above), open that page for editing in the admin-panel and you should see a new button on the top of the screen.

![Screenshot:Copy to New](copy-to-new.png)

Clicking it will open the familiar 'Add new' screen but this time the form would be pre-populated with data taken from the page we clicked the button on.

You may edit the page further to make changes if required and press save to create your new page.

Full Source:  [CouchCMS Forum](https://www.couchcms.com/forum/viewtopic.php?f=8&t=11545).  
Credits: [@kksidd](https://github.com/kksidd)