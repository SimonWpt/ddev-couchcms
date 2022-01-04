# Remove possible orphaned
rm -rf ./web/
rm -rf ./couch-add-ons/

# Install Couch without git and markdowns
git clone --depth=1 --branch=master https://github.com/CouchCMS/CouchCMS.git ./web
rm -rf ./web/.git
rm -f ./web/*.md

# Install standard couch addons (and database tools)
git clone --depth=1 --branch=master https://github.com/fallingsprings/couch-add-ons.git
rm -rf ./couch-add-ons/.git
mv ./couch-add-ons/* ./web/couch/addons
mv ./web/couch/addons/database ./web/couch/
rm -rf ./couch-add-ons

# Install HTML minifier addon
git clone --depth=1 --branch=master https://github.com/SimonWpt/tiny-html-minifier.git 
rm -rf ./tiny-html-minifier/.git
mv ./tiny-html-minifier ./web/couch/addons

# Install addons from forum
cp -r ./addons ./web/couch
mv ./web/couch/addons/redirector/redirections.php ./web


# Create directories
mkdir -p ./web/snippets/
mkdir -p ./web/snippets/fe
mkdir -p ./web/snippets/be
mkdir -p ./web/snippets/inc
mkdir -p ./web/uploads/
mkdir -p ./web/assets/css
mkdir -p ./web/assets/fonts
mkdir -p ./web/assets/images
mkdir -p ./web/assets/js
mkdir -p ./web/assets/scss


# Copy config and additional files
cp -n ./config/_config.php ./web
cp -n ./config/_db.php ./web
cp ./config/config.php ./web/couch
cp -n ./config/humans.txt ./web
cp -n ./config/index.php ./web
cp ./config/kfunctions.php ./web/couch/addons
cp ./config/_kfunctions.php ./web
cp -n ./config/robots.txt ./web
cp ./config/sitemap.php ./web
cp -r ./config/head ./web/snippets
cp -r ./config/icons ./web/assets
