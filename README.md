# Candidature #MAVOIX v1.4.0

Soumission et publication des inscriptions au tirage au sort [#MAVOIX](https://mavoix.info).

Le formulaire se trouve à l'adresse : https://candidature.mavoix.info.

Version de test : https://candidature.maudry.fr


## Notes de version

### 1.4.0

- possibilité d'envoyer l'extrait de casier judiciaire 3B
- les candidats peuvent modifier leur candidature, mais elle est alors mise hors-ligne
- ajout d'un menu sur la version mobile
- ajout de Twitter cards pour les partages sur Twitter
- ajout de métadonnées OpenGraph (Facebook) sur la page du formulaire
- ajout d'insctructions de contact en cas de soucis
- corrections dans la mise en majuscule des noms de villes et des prénoms
- possibilité d'envoyer l'autre face de la carte d'identité dans un deuxième fichier

### 1.3.0

- Sur l'écran d'édition, possibilité pour les validateurs d'enregistrer et de mettre en ligne une candidature
- Ajout d'un champ de commentaire dans la vue d'édition (les commentaires sont visibles dans la vue liste)
- Ajout d'un bouton de mise en ligne dans la vue liste
- Majuscule aux noms de villes et noms de famille tout en majuscules
- Modification du texte du chapeau au-desssus de la liste publique des candidats

### 1.2.3

- Suppression de la vérification de la pièce d'identité obligatoire en mode édition (car fichier précédent conservé si pas de nouveau fichier)

### 1.2.2

- Correction d'un bug concernant la vérification de la pièce d'identité obligatoire
- Correction de la balise META open-graph sur la page de liste des volontaires

### 1.2.1

- Ajout d'un lien vers le site Web de #MAVOIX dans la page de liste des candidats
- "CandidatureS" dans le titre de la page

### 1.2.0

- Ajout de métadonnées OpenGraph pour améliorer les partages sur Facebook.
- Modifications rédactionnelles

### 1.1.0

- Visualisation de la liste de candidats à valider sous forme de tableau (bêta)
- Suppression de l'affichage public de la première lettre du nom de famille des candidats
- Ajout du script de sauvegarde
- Ajout d'un menu jaune dans l'interface d'admin
- Suppression des dernière références aux attestations d'inscription aux listes électorales (document bien plus compliqué à obtenir que l'extrait de casier judiciaire B3)
- Pour les validateur, plus besoin de télécharger les documents pour les valider : visualisation directe dans le navigateur

#### 1.0.2

- Clarification du caractère public de certaines informations fournies

#### 1.0.1

- Le deuxième tour est le **18 juin**
- Autres coquilles
- Augmentation de la taille de la police de caractères de base (14px => 18px)

### 1.0.0

- Formulaire fonctionnel et stylisé
- Interface admin fonctionnelle mais non stylisée


## Prerequisites

- PHP 5 or later
- Composer
- PHP mcrypt module
- PHP curl module
- MySQL database


## Installation

1. Set documentRoot of your VHOST on /web/
1. Set chmod 777 on /web/data folder
1. Set chmod 777 on /tmp folder
1. execute CREATE-DATABASE.sql in a new database
1. Create a user admin (use PASSWORD function on pass field)
1. Run composer update
1. Copy web/config.sample.php into web/config.php and customize it
