#!/bin/bash

# cache project
shopt -s extglob
shopt -s dotglob
mkdir tmp
mv !(..|.|tmp) tmp

# prepare docker env
mkdir environment
git clone https://gitlab.ghostthinker.de/score/score-prototype-2-docker.git -b gitpod ./environment


# setup project structure
cd ./environment/ 
mkdir web
cp default.env .env
cd ..
mv ./tmp/* ./environment/web/
rm -d tmp
cd ./environment/web/
cp .env.docker .env

# permissions so docker can use the file
chmod +x scripts/init_gitpod.sh

# create .gitmodules dummy so the vscode source-control plugin can be used for the nested project: https://github.com/microsoft/vscode/issues/37947#issuecomment-460340426
cd ..
echo $'[submodule "score-prototype-2"]\npath = ./web\nurl = dummy_string_needed_here' > .gitmodules

# create empty dummyfile to enable snapshots, snapshots somehow execute the .gitpod.yml-init when waking up 
# which they probably shouldn't: https://www.gitpod.io/docs/config-start-tasks/ 
cd ..
mkdir gitpod
touch ./gitpod/init.sh

# copy files which usually belong in root folder
mkdir .vscode
cp -a ./environment/web/.gitpod.Dockerfile ./
cp -a ./environment/web/gitpod/.vscode/* ./.vscode/

# prepare debugger config with host data
cp -a ./environment/web/gitpod/docker-php-ext-xdebug-template.ini ./environment/web/gitpod/docker-php-ext-xdebug.ini
hostname -I >> ./environment/web/gitpod/docker-php-ext-xdebug.ini

cd ./environment/
docker-compose build
