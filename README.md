Module ISeries
===============
Suivi des traitements ISeries en passant par l'IFS.

Les appels de commandes se font par ssh en appelant la commande système de l'IFS. 

Pré-requis
----------
Modules:
- Core
- User

Installation
------------
### SSH 

La bibliothèque __phpseclib__ doit être téléchargée et copiée dans le répertoire __vendor__.

Configuration
-------------

Configuration du fichier __app/config/parameters.yml__:

    # Module ISeries
        I5:
            host: NOM ou IP Iseries
            user: login
            password: mot de passe


Releases
--------
__0.1__
- Maquette du module
- Affichage des jobs par WRKACTJOB
- Détail du job (DSPJOB et DSPJOBLOG)

