# Réponses de contrôleur


Les réponses de contrôleur sont gérées par le service Response `lyssal.response`


## Redirection

Les redirections permettent notamment de simplifier les retours de contenu lors des appels AJAX.

### JSON

`redirectJson(array $response)` permet de retourner un objet Json après une redirection.

```php
public function monAction()
{
    // ...

    return $this->container->get('lyssal.response')->redirectJson(array('success' => true));
}
```
