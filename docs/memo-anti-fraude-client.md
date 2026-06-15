# Mémo client — Règle anti-fraude « bonus point de vente »

**Date :** 15 juin 2026
**Objet :** Clarification de la règle des +4 points en point de vente (PDV) et correction des points obtenus abusivement
**Préparé pour :** réunion client 16h00

---

## 1. Le contexte en une phrase

Certains joueurs ont gagné des points en **« passant » dans un point de vente** (check-in / géolocalisation) **sans faire de pronostic sur place**. Ce n'était pas l'intention du jeu et cela fausse le classement.

## 2. Ce qui a changé

**Avant (source de confusion) :**

- Le message affiché aux joueurs laissait croire qu'on gagnait des points en **visitant** un point de vente.
- Le texte parlait d'« un seul bonus par point de vente et par jour », ce qui était à la fois **inexact** et **mal compris**.

**Maintenant (règle clarifiée) :**

- Les **+4 points** sont accordés **uniquement quand un pronostic est réellement enregistré sur place**, dans un point de vente partenaire.
- Un **simple passage sans pronostic = 0 point**.
- Le bonus **se cumule** entre points de vente différents : un joueur qui pronostique dans 3 PDV différents dans la journée gagne bien 3 × 4 points.
- Pour éviter le « farming », un même point de vente ne donne le bonus **qu'une fois par jour** (mais rien n'empêche d'en visiter plusieurs).

> En résumé : **le bonus récompense l'acte de pronostiquer en boutique, pas le simple fait d'y être.**

## 3. Ce qui a été corrigé concrètement

1. **Les textes** vus par les joueurs ont été réécrits (fenêtre d'information à l'accueil, page « Règlement », page « Comment jouer ») pour dire clairement la règle ci-dessus.
2. **Le code** a été nettoyé : l'ancien mécanisme qui pouvait accorder des points sur un simple check-in a été retiré. Désormais, **techniquement**, seul un pronostic en boutique déclenche le bonus.
3. **Bonus d'affichage :** la fenêtre d'information anti-fraude qui s'affichait mal (texte du fond qui passait par-dessus) a aussi été réparée.

## 4. Peut-on retirer les points gagnés abusivement ? — **Oui**

C'est possible et **fiable**, car ces points sont parfaitement identifiables dans la base : ce sont les bonus marqués « visite point de vente » (check-in) **sans pronostic associé**.

**Comment on procède, en toute sécurité :**

1. **Étape 1 — Simulation (aucun changement) :** on lance un rapport qui liste, joueur par joueur, le nombre de check-ins concernés et le nombre de points à retirer, avec le total avant / après. Cela permet de **valider les chiffres avec vous avant toute action**.
2. **Étape 2 — Application (après votre feu vert) :** on retire les points. L'opération :
   - garde **l'historique d'origine** (rien n'est effacé en cachette) ;
   - inscrit une **ligne de correction traçable** pour chaque joueur (on sait qui, combien, quand) ;
   - **ne descend jamais un score en dessous de 0**.
3. **Étape 3 — Vérification :** un contrôle automatique confirme que tous les compteurs restent cohérents.

**Réversibilité :** comme l'historique est conservé et la correction tracée, on peut justifier ou revenir sur l'opération si besoin.

> ⚠️ Les **chiffres exacts** (nombre de joueurs et de points concernés) seront connus dès qu'on lance la **simulation sur les données de production**. Aujourd'hui ils ne sont visibles que côté production, pas sur l'environnement de test.

## 5. Ce dont nous avons besoin de vous

- **Validation de la nouvelle règle** telle que formulée au point 2.
- **Décision** : retire-t-on les points abusifs pour tous les joueurs concernés ? (recommandé pour l'équité du classement)
- Le cas échéant, **accord pour lancer la simulation** afin de vous présenter les chiffres réels avant d'appliquer quoi que ce soit.

## 6. Calendrier proposé

| Étape | Quand |
|------|-------|
| Mise en ligne des textes + correctif technique | Dès aujourd'hui après votre accord |
| Simulation du retrait (rapport chiffré) | Juste après, à vous présenter |
| Application du retrait | Sur votre validation des chiffres |

---

*Document non technique préparé pour la réunion. Les détails d'implémentation sont disponibles côté équipe technique sur demande.*
