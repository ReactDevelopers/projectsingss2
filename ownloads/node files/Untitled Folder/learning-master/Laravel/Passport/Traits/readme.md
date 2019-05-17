#Laravel Passport Token Hack

###For generating password grant token(*Bearer Token*) without making a external request.

After doing the ***Laravel setup*** and installing ***Passport***

Steps to setup:
* Make a Traits folder inside the ***app*** repository.
* Put the file *PassportToken.php* in it.

Usage:
* Use the trait in the working class.
```
use App\User;
use App\Traits\PassportToken;
class AnyController extends Controller {

    use PassportToken;
}
```
* For generating tokens.
```
$user = User::find(1);
// return  response 
return $this->getBearerTokenByUser($user, 1, true);
// return array
return $this->getBearerTokenByUser($user, 1, false);
```

