###Please adjust info###
SITE_URL="https://melfredborzall.local"
DB_HOST="localhost"
DB_NAME="forix_melfredborzall"
DB_USER="root"
DB_PASS="root"
WEB_HTML="/home/nghiaho/Sites/melfredborzall.local/html"
ADMIN_USER="forixadmin"
ADMIN_PASS="forixadmin!123"
GIT_REPO="git@bitbucket.org:forix/melfred-borzall.git"
#########################
mkdir -p $WEB_HTML
cd $WEB_HTML
git clone $GIT_REPO .
git fetch && git checkout -b integration origin/integration
composer install
php bin/magento setup:install --base-url=$SITE_URL --backend-frontname=mpanel \
--db-host=$DB_HOST --db-name=$DB_NAME --db-user=$DB_USER --db-password=$DB_PASS \
--admin-firstname=Magento --admin-lastname=User --admin-email=user@forixwebdesign.com \
--admin-user=$ADMIN_USER --admin-password=$ADMIN_PASS \
--language=en_US --currency=USD --timezone=America/Chicago \
--use-rewrites=1 --session-save=db
./bin/magento deploy:mode:set developer
./bin/magento setup:upgrade
./bin/magento cache:flush
./bin/magento cache:enable
