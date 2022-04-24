This is the first part of the tutorial working with Laravel after completing setting up our environment. 
ref: [Traversy Media](https://www.youtube.com/watch?v=EU7PRmCpx-0&list=PLillGF-RfqbYhQsN5WMXy6VsDMKGadrJ-&index=1)

In this part of the tutorial we will go with the basics of Laravel which includes the following:
1. working with routes
2. working with controller
3. sending values to frontend
4. Creating template and fragments
5. Working with Resources: .css, .js, .scss
6. Including fragments and yeilding contents

Now let's begin with playing with *routes* and *views*

To start with let's create a directory under views anmed *pages* and create a file under it named *about.blade.php*.
Then let's go to *routes > web.php* and add some codes to it
```php
Route::get('/about', function () {
    return view('pages.about');
});
```
**php artisan serve** to run the project as local server

Dynamic route is reuqired when we are looking for an specific user or an item. in such cases we send an id along with the url. Such url's are known as the dynamic URL's. 

Adding dynamic route :
```php
Route::get('/user/{id}', function ($id) {
    return $id;
});
```
In general we are not to return any views from route. To return a view firstly we are to create a **Controller** *function* and set the **route** to the *controller* function which will return the view.

So to create a controller we will use the following *artisan* command: 
```shell 
php artisan make:controller PagesController
```

It will create a controller for our application and include the basic required codes
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    //
}
```
Path or namespace is added *App\Http\Controllers* also includes the reqquired library *use Illuminate\Http\Request* also extends the base controller *extends Controller* which is also required by any newly created controller.

Now let's go through the basics *how the controller works with route*:
let's create a function in our **PagesController**:
```php
public function index(){
    return 'INDEX';
}
```
Now in the route page we will call the *index()* function/method from the *PagesController*. For that we will replace our route **/** function 
```php
Route::get('/', function () {
    return view('welcome');
});
```
with the following:
```php
Route::get('/', 'PagesController@index');
```
Now if we save the file and call the url from browser we will see **INDEX** written on the page which was sent by the controller. 

Before doing so we are to make an small change for laravel 8.x.x onward:
```php
// go to the following location and open file
app > providers > RouterServiceProvider.php

// now uncomment the following line:
protected $namespace = 'App\\Http\\Controllers';
```

Now as we want to forward a page instead of a simple text so we will be creating a view page for this purpose in our *pages* directory with the name **index.blade.php**; and we will write some basic code. For now we want our index page to display the app name, and we will find the app name in the **.env** file, to get the app name from the *.env* file to our index page 
```php
<title>{{config('app.name', "LS-APP")}}</title>
```
> NOTE : Here *app.name* gets the name form the **.env** file, and if the file name is not present or empty in **.env** it will print the name provided in the second parameter in this case *LS-APP*

Than we have created three different *controller* functions, *routes* and *layouts* wich are */* for index, */about* for our about page, and a */service* page.

Now if we review our layouts we can see that there are repetive codes. which is not a good way. So we will be creating a layout which will contain the common contents. 

* create a directory under **views** named **layouts**
* create our layout file unde **layouts** named **app.blade.php**

To work with *blade* it's better to install a extension in our **vsCode**. To install the extension let's search it with *ext install laravel-blade* and than install **Laravel Blade Snippets**

Now in *app.blade.php* file write this:
```php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{config('app.name', 'LS-APP')}}</title>

    </head>
    <body>
        @yield('content')
    </body>
</html>
```
In the above code og *app.blade.php* the HTML codes are the common codes which contained in the *index, about, and services* page; and **@yield('content')** is the blade syntax which tells the layout what to load. 
At this poit we have created a simple layout file for our application. Now we will have to extend this layout to our pages which will require the imports of *app.blade.php*. 
Therefore now our pages *index, about, and services* code will be very simple. Such as:
```php
@extends('layouts.app')

@section('content')
<h1>Welcome to Laravel</h1>
<p>This is the laravel application from the "Laravel from scratch" youtube series.</p>
@endsection
```
In the above code **@extends('layouts.app')** tells the page which layoout to be extended, for this page we are extending the newly created layout file *app.blade.php* in *layouts* directory. 
To load the contents of this page into the layout we have set the contents inside a blade tag named **@section('content')**. In the tag we have given a name *content* which is the same name as we have used in **@yeild('content')**.

#### Passing values into pages
To test sending values to our pages let's declare a variable in our *index()* function in *PagesController*.
When it comes to sneding variables to a page there are two ways to do it. 
**First way is:**
```php
public function index(){
    $title = "Welcome to Laravel!";
    return view('pages.index', compact('title'));
}
```
and in HTML we call it this way:
```php
<h1>{{$title}}</h1>
```
also we can call the variable in HTML in the following way as well:
```php
<h1><?php echo $title; ?></h1>
```

***Second way is:**
```php
public function index(){
    $title = "Welcome to Laravel!";

    // Second way to send variable to page
    return view('pages.index')->with('title', $title); 
}
```

Generally it is good to use the second way with the regular expression.

Now we wil try sending multiple data which can be achieved by using array, which is generally make up of key value pairs similar to json. 
```php
public function services(){
    $data = array(
        'title' => 'Our Services',
        'services' => ['Web Design', 'Programming', 'SEO']
    );
    
    return view('pages.services')->with($data);
}
```
In the above controller function we have used an array type variable *$data* and set our values in key-value pair manner. In the above code we can see that the second parameter is an array rather than simple string. To access these variables in the frontend :
```php
<h1>{{$title}}</h1>
@if(count($services))
    <ul>
    @foreach($services as $service)
        <li>{{$service}}</li>
    @endforeach
    </ul>
@endif
```
Here before looping through the variable *$services* we checked for an empty array.

#### Assets and CSS in Laravel
In Laravel or any web application we require resources from styling to automation to use different libraries. In an application it is wise to keep our resources in our **resource** directory.

In laravel when we want to include an asset from the public folder of the application we have to do it in this way:
```html
<link rel="stylesheet" href="{{asset('css/app.css')}}">
```

Instead of manually including the assets we can do it through *npm* therefore let's install npm in our project:
```shell
npm install
```
Incase if npm requires an update:
```shell
npm install -g npm
```
After installation is complete we can see a new directory in our project named **node_modules**. 
> NOTE: After changing any resource .saas or .js files the project is required tobe compiled useing *npm run dev*. But running the command each time the file is changed is very tiring task. Therefore, we can use *npm run watch*, so it will look for any changes made to the resources and compile it once the change is saved.

Now adding our own custom style to the project:
1. Create a file named **_custom.scss** in *resources > sass* directory.
2. add the newly created *_custom.scss* to our *app.scss* file which was created by default while creating the project.
```php
//Custom CSS
@import "custom";
```
> NOTE: while importing the the *_custom.scss* filet to *app.scss* we have written the file name **custom** only without the trailing *_* or the file extension *.scss*.

To import resources without managing it manually we will use *composer* to do it for us. To use UI in this project such as *.css* or *.scss* or *saas* we require to import Laravel's UI library:
```shell 
composer require laravel/ui
```
After successfully installing the package, we install Bootstrap 4 in our application using the following command:
```shell
php artisan ui bootstrap
```
Finally, you need to install the bootstrap package and the related frontend dependencies such as jquery from npm using the following command and then run dev command to compile the new resources:
```shell
npm install

npm run dev
```
Now bootstrap is integrated to our project. 

#### including php files
While developing an web application throughout the way we will require to seperate some of fragments and keep it in a different file so that we can include it in a required page or in a required position. As for example for out project we require Navigation bar at every page and sometimes it can get messy. Therefore we will create our navigation bar and include it in our layout file as we require it throughout our application.

1. Create a new directory **inc** short for *includes*
2. Create a file in *inc* named **navbar.blade.php**
3. Include the file into our layout file **app.blade.php**
```php
@include('inc.navbar')
```
