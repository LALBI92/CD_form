TRAVAIL EN LOCAL PUIS 

git checkout local           # S'assurer d'être sur local
git add .                   # Ajouter modifications
git commit "Message"        # Commit local
git push origin local       # Sauvegarder sur GitHub

INTEGRATION VERS DEVELOP

git checkout develop
git merge local
./deploy-to-dev.sh "Message" # Ce script fait le push automatiquement

Dans CPANEL => git pull origin develop

Testez https://dev2.cityrecyclage.com/CD_form/ si OK

DEVELOP VERS PROD

./deploy-to-prod.sh  

Dans CPANEL => git pull origin master
