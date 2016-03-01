# Réponses de contrôleur


Les réponses de contrôleur sont gérées par le service Response `lyssal.response`


## Redirection

Les redirections permettent notamment de simplifier les retours de contenu lors des appels AJAX.

### JSON

`routeRedirect($route, $parameters = array())` : Redirige l'internaute vers une route
`jsonRedirect(array $response)` : Retourne un tableau JSON après une redirection
`getJsonUrl(array $response)` : Retourne l'URL d'une réponse JSON

Exemple d'utilisation :
```php
public function monAction()
{
    // ...

    return $this->container->get('lyssal.response')->jsonRedirect(array('success' => true));
    // OU return $this->redirect($this->container->get('lyssal.response')->getJsonUrl(array('success' => true)));
}
```
