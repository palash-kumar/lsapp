This is the second part of the tutorial where we will be working with the database and models. 
ref: https://www.youtube.com/watch?v=neSHAWdE44c&list=PLillGF-RfqbYhQsN5WMXy6VsDMKGadrJ-&index=5
1. Models & Database Migrations
2. Use **Tinker** to insert data 
3. Interact with DB using laravel
4. Fetching Data eith Eloquent
5. Forms and Saving Data
### 1. Models & Database Migrations
To work with the database and tables we will create only the database manually. And use **artisan** to export models to DB. 

After creating a database we will create a seperate controller to work with the requests to the DB:
```shell
php artisan make:controller PostController
```
Than we will create our model 
```shell
php artisan make:model Post -m 
```
Here we have used **-m** to migrate our table **Post** to DB.
So now our **Post** model is created under *app* directory. Also we can find a file named **2020_06_22_204821_create_posts_table** under *database* directory.
Once we open the file **2020_06_22_204821_create_posts_table** we can observe three things:
1. It extends *Migration*
2. function up() - Runs when we execute command to migrate and create the table and columns that are declared inside it.
3. function down() - Executed when we wnat to rollback our migration. Which drops the entire table.

> NOTE: Laravel creates **Users** model and migration file automatically to implement auntication.

Now configure the DB credentials to connect to datavase. The database configurations are located in **.env** file and we have to provide the credentials here:
```php
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lsapp
DB_USERNAME=username
DB_PASSWORD=password
```
We are also required to change DB settings in *app -> config -> database.php*
```php
'mysql' => [
    'driver' => 'mysql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST', 'localhost'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'lsapp'),
    'username' => env('DB_USERNAME', 'username'),
    'password' => env('DB_PASSWORD', 'password'),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    ]) : [],
]
```
Than run our PHP migration command:
```shell
php artisan migrate
```

When executing the command there might be case where we get the following exception
```shghell
# Exception
   Illuminate\Database\QueryException 

  could not find driver (SQL: select * from information_schema.tables where table_schema = lsapp and table_name = migrations and table_type = 'BASE TABLE')
```
Then it is requred to make some changes in **php.ini** and export the mysql extension. 
open *php.ini* file
```shell
gedit  /etc/php/php.ini 
```
uncomment the following line:
```shell
extension=mysqli
```
than restart mysql
```shell
sudo systemctl start mysqld
```
Than run the migration command again, and it will work.
Incase if a rollback is required for immidiate last migration:
```shell
php artisan migrate:rollback --step=1
```

If the length of column type is required to be changed it can be achieved by simply doing the following:
Goto *app -> providers -> AppServiceProvider.php* and lengths can be added to the following function:
```php
public function boot()
{
    //
    Schema::defaultStringLength(191);
}
```
Also we are required to import/use *Facades\Schema*
```php
use Illuminate\Support\Facades\Schema;
```
Now for every string variable declared will have the length of 191.
```php
$table->string('title'); // Our title will be of length 191
```

### 2. Using Tinker to insert data
It is better to have some initial data into DB while developing an application. There are two seperate methods which can be used to insert data into DB. 
1. Using DB client pannel such as phpMyAdmin, dbeaver or Toad
2. second is the PHP's **Tinker**

As we are working with PHP therefore we will use **Tinker** just out of curiosity. To use tinker we can use the following command:
```shell
php artisan tinker
```
It will provide you with a Tinker shell. From here we can interact with DB with **Elequent** which is an ORM thant makes it easy to do things.
To access a model:
```shell
>>> App\Post
```
As our model name is **post**.
We can also call function using it. For example to know how many data is in our model POST in DB we use the following command:
```shell
>>> App\Post::count()
```
To create a model instance we can use the following command:
```shell
>>> $post = new App\Post();
```
It it will create a new instance for our model **Post**. Now that our instance is created we can use this variable to set data to our model fields in the following way:
```shell
$post->title = 'Post One';
$post->Body = 'This is a post body for Post One';
```
Now to save our data to the DB we have to execute the following command:
```shell
$post->save();
```
An overview of all the tinker commands we have used till this point with response:
```shell
>>> $post = new App\Post();
=> App\Post {#3054}
>>> $post->title = 'Post One';
=> "Post One"
>>> $post->Body = 'This is a post body for Post One';
=> "This is a post body for Post One"
>>> $post->save();
=> true
>>> App\Post::count()
=> 1
```
For now this is it for Tinker. 

### 3. Interact with DB using laravel
Till now we were interacting with tinker. Now we will be Larabvel controller to interact with our DB. To interact with our **Post** model we will create a controller named **PostsController**:
```shell
php artisan make:controller PostsController --resource
```
It can be noticed that unlike the previous *make:controller* command we have used **--resource** along with it. The benefit of using this command is that when making the controller it includes the necessary functions as well:
1. index() - used for Linsting
2. create() - creating new resource 
3. store(Request $request) - to store the newly created data/ / Inserting new data
4. show($id) - To retrieve specific data from DB
5. edit($id) - To edit specific resource
6. update(Request $request, $id) - To update specific resource
7. destroy($id) - Remove specific resource

Now let's check our route list using:
```shell
php artisan route:list
```
Result:
|--------|----------|-----------|----------|-----------------------------------------------|------------|
| Domain | Method   | URI       | Name     | Action                                        | Middleware |
|--------|----------|-----------|----------|-----------------------------------------------|------------|
|        | GET/HEAD | /         |          | App\Http\Controllers\PagesController@index    | web        |
|        | GET/HEAD | about     | about    | App\Http\Controllers\PagesController@about    | web        |
|        | GET/HEAD | api/user  |          | Closure                                       | api        |
|        |          |           |          |                                               | auth:api   |
|        | GET/HEAD | services  | services | App\Http\Controllers\PagesController@services | web        |
|        | GET/HEAD | user/{id} |          | Closure                                       | web        |
|--------|----------|-----------|----------|-----------------------------------------------|------------|
As we can see our current routes. As we have created a new controller **PostsController** therefore we are required to map our routes to access **PostsController**'s function. Curently we are declaring our routes in **web.php** in the following manner:
```php
Route::get('/about', 'PagesController@about')->name('about');
```
But Implementing each and every routes manually can be tiresome. Therefore *Laravel* has got a simple way to implement the routes for a controller's resources. Which can be achieved in the following way:
```php
Route::resource('posts','PostsController');
```
Here we can notice that instead of using *get, post, update, delete* we have used *resource* and we have passed two values in the parameter. The first value **posts** represents the controller and the second parameter is the controller **PostsController** whose function we want to include in route. Now we if we run the artisan route list command again we can see the following reslut:

| Domain | Method    | URI               | Name          | Action                                        | Middleware |
|--------|-----------|-------------------|---------------|-----------------------------------------------|------------|
|        | GET/HEAD  | /                 |               | App\Http\Controllers\PagesController@index    | web        |
|        | GET/HEAD  | about             | about         | App\Http\Controllers\PagesController@about    | web        |
|        | GET/HEAD  | api/user          |               | Closure                                       | api        |
|        |           |                   |               |                                               | auth:api   |
|        | GET/HEAD  | posts             | posts.index   | App\Http\Controllers\PostsController@index    | web        |
|        | POST      | posts             | posts.store   | App\Http\Controllers\PostsController@store    | web        |
|        | GET/HEAD  | posts/create      | posts.create  | App\Http\Controllers\PostsController@create   | web        |
|        | GET/HEAD  | posts/{post}      | posts.show    | App\Http\Controllers\PostsController@show     | web        |
|        | PUT/PATCH | posts/{post}      | posts.update  | App\Http\Controllers\PostsController@update   | web        |
|        | DELETE    | posts/{post}      | posts.destroy | App\Http\Controllers\PostsController@destroy  | web        |
|        | GET/HEAD  | posts/{post}/edit | posts.edit    | App\Http\Controllers\PostsController@edit     | web        |
|        | GET/HEAD  | services          | services      | App\Http\Controllers\PagesController@services | web        |
|        | GET/HEAD  | user/{id}         |               | Closure                                       | web        |

From the above result we can see that all the functions route in the *PostsController* are listed as route and all of the routes from *PostsController** has **posts** at the beginning.

### 4. Fetching Data eith Eloquent
As we recall from our privous sections we hav e also created a model which extends **Model**. By default when migrating the table is named after model in the database. The column names can be changed in the model. such as:
```php
//Table name 
protected $table = 'posts';

// primary key
public $primaryKey = 'id';

// Timstamps
public $timeStamps = true;
```
But currently these are not for reference we are not using it this time.
Now let's create pages for our **PostsControllers**. At first we will be creating a directory for our *PostsController* and name it **views->posts** and a page for our *index()* named *index.blade.php* in the directory.

Than we will use the layout file which is in *resources -> views -> layouts -> app.blade.php*. 

Now we will use our model to fetch data using **Eloquent** and show it in our *posts -> index.blade.php*. To do this in our controller we will fetch our data using the following function:
```php
public function index()
{
    $posts = Post::all();
    return view('posts.index')->with('posts', $posts);
}
```
So now we have loaded all the data from our **post** table into the variable *$posts* and sending it to *index.blade.php* by using *->with* with our *view()* function. 
Now in the frontend *index.blade.php* page we will be showing the list which was fetched by our controller. 
```php
//index.blade.php
@extends('layouts.app')

@section('content')
    <h1>Posts</h1>
    @if (count($posts) > 0)
        @foreach ($posts as $post)
            <div class="well">
                <h3>{{$post->title}}</h3>
                <small>Written on {{$post->created_at}}</small>
            </div>
            
        @endforeach
    @else
        <p>No Posts Found</p>
    @endif
@endsection
```
Now in our browser we can see that our posts are printed. 
As we have got a list of posts now we will try to view the an individual post when clicked by the user. To do this we will add an anchor tag to our *h3* tag to call the clicked post. So now our modified *h3* tag contents will look like :
```php
<h3><a href="/posts/{{$post->id}}"> {{$post->title}}</a></h3>
```
Now when the post is clicked on the client/user's end it will take us to a new page with the post details which was clicked on. For this Laravel uses the **show($id)** function in *PostController* as we can see that from *route mapping* in **Section 3**. To get the specific item from the DB we will use: 
```php
public function show($id)
{
    $post = Post::find($id);
    return view('posts.show')->with('post', $post);
}
```
So to view the details of the post we will now create a new page *resources -> views -> posts -> show.blade.php* which will display the posts content from DB. 
```php
@extends('layouts.app')

@section('content')
    <h1>{{$post->title}}</h1>
    <div class="well">
        <p>{{$post->body}}</p>
    </div>
    <small>Written on {{$post->created_at}}</small>
    
@endsection
```
 At this point we are done with fetching the list and display an item. But what if we require to call the the elements or items in ascending or descending order based on column? Here is the solution:
 ```php
$posts = Post::orderBy('title','desc')->get();
 ```
* Now if we want to fetch a post or data based of other column instead of using the *id* or *primary key* than we can acheive the result like this:
 ```php
$psot = Post::where('title', 'Post Two');
 ```
* If we would like to limit our query result to a specific number than we can go with:
```php
$posts = Post::orderBy('title','desc')->take(1)->get();
```
Now notice that we have added a tailing conditiion before *get()*, which limits our query result to 1.

* Eloquent also provides use with the facility to **paginate** our query result. We can achieve it by doing the following:
```php
$posts = Post::orderBy('title','desc')->paginate(1);
```
and than adding the following code to our frontend:
```php
{{$posts->links()}}
```
This will provide us with possible pages required to load our complete list. And just by navigating over the page numbers we can view the results.

 Till now we were using **Eloquent**, which is an ORM, to communicate or fetch our data from DB. But we can also use native sql queries by do the following:
 1. Import the DB library
 ```php
 use DB;
 ```
 2. The quesry goes like this:
 ```php
 $posts = DB::select('SELECT * FROM posts');
 ```

 ### 5. Forms and Saving Data
 In this section we will be working with submitting form and saving ddata to our server.

 To send and save data to our DB we will require a form to take input from user and submit it.
We will create a new page for our **create()** function in our **resources -> views -> posts** directory named *create.blade.php*. We will be using the create function because by default it is mapped with our route for creating data which is to be saved to DB.

Too 0work with forms in laravel we will be using **laravelcollective**, which was included with laravel up until 5.0, and later on it was removed. But we can still use it by installing it using **composer**. 
ref: https://laravelcollective.com/docs/6.0/html
```shell
composer require laravelcollective/html
```
After the installations completes we will have to add few things to our **config -> app.php** file. 
Add these under **providers** list:
```php
Collective\Html\HtmlServiceProvider::class,
```
And these under **aliases** list:
```php
'Form' => Collective\Html\FormFacade::class,
'Html' => Collective\Html\HtmlFacade::class,
```

AS our configurations are done, now we will continue with creating our form in *create.blade.html*.
```php
{!! Form::open(['action'=>'PostsController@store', 'method'=>'POST']) !!}
    <div class="fom-group">
        {{Form::label('title', 'Title')}}
        {{Form::text('title', '', ['class'=>'form-control', 'placeholder'=>'Title'])}}
    </div>
    <div class="fom-group">
        {{Form::label('body', 'Body')}}
        {{Form::textarea('body', '', ['class'=>'form-control', 'placeholder'=>'Body text'])}}
    </div>
    {{Form::submit('Submit',['class'=>'btn btn-primary'])}}
{!! Form::close() !!}
```
These are the syntax rule followed by *laravelCollective*. Notice that in the action we have called **store** function from  **PostsController** instead of using the URL endpoint **/posts/store** as we got from the route list. That is because *laravelCollective* automatically maps to the controller route.

Now we will look into our controllers *store($request)* function to receive our data from the form upon submit, process it, than validate it and finally save it to our DB.
In Laravel validation is really simple which can be achieved in this way:
```php
public function store(Request $request)
{
    $this->validate($request,[
        'title'=>'required',
        'body'=>'required'
    ]);

    return 'Form validated';
}
```
Now that we have validated our form and if we try to submit an empty form the form will not be submitted, but it will also not show us any message. To show our message we will creat3e a file in **vew -> inc** directory named **messages.blade.php**. We will be using this page to check three things:
1. The Errors array created when we have failed validation
2. The session vlaue for success
3. The session value for error
```php
@if (count($errors)> 0)
    @foreach($errors->all() as $error)
        <div class="alert alert-danger">
            {{$error}}
        </div>
    @endforeach
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{session('success')}}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{session('error')}}
    </div>
@endif
```
Than we will include our *messages.blade.php* to our layout file just before the content.
```php
<body>
    @include('inc.navbar')
    <div class="container">
        @include('inc.messages')
        @yield('content')
    </div>
</body>
```
Now to create and save our new post we have to modify our store function in this way:
```php
public function store(Request $request)
{
    $this->validate($request,[
        'title'=>'required',
        'body'=>'required'
    ]);

    // Creating Post
    $post = new Post;
    $post->title = $request->input('title');
    $post->body = $request->input('body');
    $post->save();
    return redirect('/posts')->with('success', 'Post Created');
}
```
In the store function first we have validated the request. than we have created a new post object and set the values, later we have called *save()* function to save the new post, and finally in return we have redirected to */posts* croute along with a success message.

Now we are able to save data to our DB. But plain texts, we see it always. What about if we can add some styles to our texts eg: Bold, Italic or underlined. Lets download **Laravel-ckeditor** from here [Laravel-ckeditor](https://github.com/UniSharp/laravel-ckeditor) and add it to our app by following the instruction. 

**OR** *ckeditor* can be installed or used by downloading the ck editor from here [ckeditor](https://ckeditor.com/ckeditor-4/download/) and follow the following steps:
1. Extract the downloaded *ckeditor* zip 
2. Copy the **ckeditor** directory to the project's *public* directory.
3. Include *ckeditor.js*:
```php
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'article-ckeditor' );
</script>
```
4. Now simply use the *textarea*'s id inside the script. 
And we are all set.

With our textarea features setup now if we type anything in our body and format it eg: make the text bold and save it than in the post body on the front-end it will view the text with html formatting instead of viewing the text as we have written and formated. such as : <p>This is post <strong>Four</strong></p>
Now to view the text properly with the formatting than for the body we will use one curly bracket and two exclamation mark **!!** in this way:
```php
{!!$post->body!!}
```
Now we can see our text with proper formatting. Eg. This is post **Four**.

To edit or update our post we will create a button in our show post file *show.blade.php*:
```php
<a href="/posts/{{$post->id}}/edit" class="btn btn-info">Edit</a>
```
And update our **eidt*($id)** function:
```php
public function edit($id)
{
    $post = Post::find($id);
    return view('posts.edit')->with('post', $post);
}
```
Now lets create our edit page in our **posts** directory named as *edit.blade.php* and create our edit form same as our *create* form but it will be a bit different. As we have seen from our route list that the update function will not work with post as it supports **put** and form have only two methods **GET** and **POST** so we will modify our code in the following way:
From Route:list
|        | PUT|PATCH | posts/{post}      | posts.update  | App\Http\Controllers\PostsController@update   | web        |

```php
@extends('layouts.app')

@section('content')
    <h1>Create Post</h1>

    {!! Form::open(['action'=>['PostsController@update', $post->id], 'method'=>'POST']) !!}
        <div class="fom-group">
            {{Form::label('title', 'Title')}}
            {{Form::text('title', $post->title, ['class'=>'form-control', 'placeholder'=>'Title'])}}
        </div>
        <div class="fom-group">
            {{Form::label('body', 'Body')}}
            {{Form::textarea('body', $post->body, ['id'=>'article-ckeditor','class'=>'form-control', 'placeholder'=>'Body text'])}}
        </div>
        {{Form::hidden('_method','PUT')}}
        {{Form::submit('Submit',['class'=>'btn btn-primary'])}}
    {!! Form::close() !!}
    
@endsection
```
Notice that we have used **{{Form::hidden('_method','PUT')}}** to spoof the **POST** as submit the form as **PUT**. And update our **update(Request $request, $id)** function:
```php
public function update(Request $request, $id)
{
    $this->validate($request,[
        'title'=>'required',
        'body'=>'required'
    ]);

    // Creating Post
    $post = Post::find($id);
    $post->title = $request->input('title');
    $post->body = $request->input('body');
    $post->save();
    return redirect('/posts')->with('success', 'Post Updated');
}
```
Now we can edit and update our posts.

As we have learned how to fetch list, create, save, edit, and update a post, Now we will learn how to delete a post. To delete a post we cannot just simply use an anchor tag just like we have used for edit as from the route list we can see that the **destroy($id)** function supports only **DELETE** method. So we will have to use form to delete a post or an item:
```php
{!! Form::open(['action'=>['PostsController@destroy', $post->id], 'method'=>'POST', 'class'=>'pull-right']) !!}
    {{Form::hidden('_method','DELETE')}}
    {{Form::submit('Delete',['class'=>'btn btn-danger'])}}
{!! Form::close() !!}
```
Now we will have to update our **destroy($id)** function:
```php
public function destroy($id)
{
    $post = Post::find($id);
    $post->delete();
    return redirect('/posts')->with('success', 'Post Deleted');
}
```
Wth this we have became familer and complted a complete **CRUDE** operation. 

In the next section we will be working with authentication and Login, and Access control.
