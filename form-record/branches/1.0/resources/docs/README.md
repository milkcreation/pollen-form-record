# USAGE
```php
/**
 * USAGE :
 * ---------------------------------------------------------------------------------------------------------------------
 * Configuration standard des champs :
 * 'fields'    => [
 *      {...}
 *      [
 *          {...}
 *          'addons'        => [
 *              //
 *              'record'        => [
 *                  // Active l'affichage de la colonne pour ce champ. true par défaut.
 *                  // Par défaut le label du champ de formulaire est utilisé en tant qu'intitulé de colonne.
 *                  // Utiliser une chaîne de caractère pour personnaliser l'intitulé.
 *                  // Utiliser un tableau pour personnaliser la colonne
 *                  // @see \tiFy\Template\Templates\ListTable\Factory.
 *                  // @var $column boolean|string|array
 *                  'column'         => true,
 *                  // Active l'affichage de l'aperçu en ligne pour ce champ. true par défaut.
 *                  // Par défaut le label du champ de formulaire est utilisé en tant qu'intitulé de qualification.
 *                  // Utiliser une chaîne de caractère pour personnaliser.
 *                  'preview'        => true
 *                  // Active l'enregistrement du champ. true par défaut.
 *                  // Par défaut l'identifiant du champ de formulaire est utilisé en tant qu'indice de qualification.
 *                  // Utiliser une chaîne de caractère pour personnaliser.
 *                  'save'        => true
 *              ]
 *              {...}
 *          ]
 *      ]
 *      {...}
 * ];
 */
```
