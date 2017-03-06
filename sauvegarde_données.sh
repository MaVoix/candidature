#!/bin/bash

# Script à exécuter sur l'ordinateur qui accueille la sauvegarde

dir=/home/user/backup
current_dir=${pwd}

# Saisie du mot de passe pour l'archive ZIP chiffrée
echo -n ZIP password:
read -s password
echo

date=`date +%Y-%m-%d:%H:%M:%S`

cd $dir


# Si une archive de sauvegarde n'existe pas, on la crée, sinon on extrait son contenu avec le mot de passe fourni
if [ ! -f data.zip ]; then
mkdir sql data
else
unzip -P $password data.zip
fi

# "server" est un alias SSH qui désigne une adresse et un port, ici le serveur de "candidature" distant
# On crée un "dump" (export) de la base de données MySQL du serveur
ssh server 'mysqldump -u user -p candidature > dump.sql'

# On copie ce dump depuis le serveur de candidature vers l'ordinateur via SSH ("secure shell", connexion sécurisée), dans le même répertoire que les précédents dumps (si ce n'est pas la première sauvegarde)
scp server:dump.sql sql/$date.sql

# Ne pas laisser de trace du dump sur le serveur
ssh server 'shred -v -n5 dump.sql'

# Récupération des fichiers uploadés par les candidats, en ne copiant que les nouveaux fichiers (syncronisation intelligente). Le tout, toujours via SSH.
rsync -arvz server:candidature/web/data .

# Archivage du tout (anciennes + nouvelles données) dans un ZIP chiffré
zip -P $password -r data.zip data sql

# Suppression des fichiers téléchargés
rm -rfv data sql

# Retour au répertoire où l'on se trouvait
cd $current_dir
