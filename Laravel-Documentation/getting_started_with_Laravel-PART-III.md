This is the third part of the tutorial and by now we learnt the CRUDE functionality. In this part we will be lerning how to work with Authentication, login and access control of our application.
1. Authentication
2. Model Relationships
3. Access Control

### 1. Authentication
For authentication we need a user to login. And the Users are registered or stored in a table **USERS** in our DB. Now Laravel creates a Table or model named **users** with basic fields id, name, email, password and required timestap along with migration when we cerate a new project, and than when we use the migrate command it migrates the *users* model to our DB. No for now we have to enable authentication for our application. To enable authentication we will use the following command:
```shell
php artisan make:auth
```
The above command works only for Laravel version 5.x and below. So for the updated versions we will be using the following commands:
```shell
composer require laravel/ui --dev

php artisan ui vue --auth
```
From the above commands **composer require laravel/ui --dev** this command is not required if it has been executed before in the project. 
Now when the command **php artisan ui vue --auth** is executed it will provide us with the following message:
```shell
The [layouts/app.blade.php] view already exists. Do you want to replace it? (yes/no) [no]:
```
It will appear if the application already has a layout named *app.blade.php* in the layout directory. So it's better to copy a backup for the created *app.blade.php* file and let the auth command to overwrite it, so that in later time we can modify the *auth.blade.php* file with our code.

Once it's done than we will have to run the following command as instructed while installing the *auth* using the previous command.
```shell
npm install

npm run dev
```

Now as everything is done let's review the *app.blade.php* after the override. We can see that alot of codes have been added to the file such as:
1. csrf token has been added
2. Login link is added
3. Registration link has been added

As we have our navbar in *inc -> nabvbar.blade.php* so we will move the new navbar from *app.blade.php* and modify it so that we have our previous navbar functionality as well along with out new navbar items.

Than our navigation page and app.blade.php page is set and fixed.

Now if we register or login we will see that out controller is mapped to **/home** we can also find a route in our *web.php* with **Route::get('/home', 'HomeController@index')->name('home');** So if we want to change th eroute and controller name from home to dashboard we are to change it in all the pages that has it. 
1. Controller name  **HomeController** -> **DashboardController**
2. in *web.php* change */home* -> */dashboard*
3. view name *home.blade.php* -> *dashboar.blade.php*
4. Change *public const HOME = '/home'* in **providers -> RouteServiceProvider.php** to **'/dashboard'

Now lets register a user using the register link provided by the auth with the following:
e-mail: palash@gmail.com
pass: 12345678

When we click on *Register* the user gets registered and automatically signed in and sent to our dashboard page. If we logout and login again using the same creds we can successfully logged in.

Now let's add our *create post* link in our dashboard page. With this link we can go to create post page created previously and save the post. 

As a fact there can be manny users with different posts. So we need to map each of the user to the post created by the user, but we do not have *user_id* field in our post table to map the post to the user. So we will create a migration that will the *user_id* column to the post table using the following command and run it:
```shell
php artisan make:migration add_user_id_to_posts
```
we can see that a new migration file is created under *databse -> migrations -> 2020_06_26_013942_add_user_id_to_posts*. And now if we open it we can see that there are only two functions **up()** and **down()** with the schema included in both of the functions:
```php
Schema::table('posts', function (Blueprint $table) {
    //
});
```
We have to declare our columns here to be created in the **posts** table in DB:
```php
//to add the new user_id column
public function up()
{
    Schema::table('posts', function (Blueprint $table) {
        $table->integer('user_id');
    });
}
```
and inscase if we need to rollback:
```php
// To Reverse the migrations.
public function down()
{
    Schema::table('posts', function (Blueprint $table) {
        $table->dropColumn('user_id');
    });
}
```
The we execute the migration command to update our **posts** table with the new column:
```shell
php artisan migrate
```
Now in DB we can see that our **posts** table has been updated with the new column and preserved all the previous data.
Now update the usr_id column with an user id as the column has just now been added to the table.

As we have updated our previous data now let's go to our posts controller and edit our *store(Request $request)* function to save the logged in user's id as we have added authentication to our application so we can get the logged in users id from the **auth()** and the code will be:
```php
$post->user_id = auth()->user()->id;
```
so the updated code will look like:
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
    $post->user_id = auth()->user()->id;
    $post->save();
    return redirect('/posts')->with('success', 'Post Created');
}
```
Now if we create a new post and submit it than we can see that the new post has got a *user_id* who was logged in and created the post.

### 2. Model Relationships
In this section we will learn about how to make a relation between two tables, in our case we will be making relation between our *users* and *posts* table so that when logged in it will show the posts of the logged in user's posts.
To create a relationship with the posts model let's open the **Post.php** file and add the following code:
```php
public function user(){
    return $this->belongsTo('App\User');
}
```
Here **$this** represents the model **Post**. And what this function means is that model **Post** has the relationship with the **Users** model and a single post belongs to a user. Than we will have to update the **User** model:
```php
public function posts(){
    return $this->hasMany('App\Post');
}
```
This function represents that a user has many posts, or a user can have many post. 

So now the relation between **User** & **Post** is **OneToMany** reation. As a user can have more than one posts, but a post can only belong to a single user.

Now in the dashboard let's display the posts of the logged in user. To do this let's go to our **DashboardController.php** as this contain the primary function **index()** for */dashboard* route.
```php
public function index()
{
    $user_id = auth()->user()->id ;
    $user = User::find($user_id);
    return view('dashboard')->with('posts', $user->posts);
}
```
Here we have takekn the id from the auth as the user is logged in, than found out the user from DB by user_id and than using **with** we have called the **posts** which is the function declared in **Users.php** model. And in the dashboad we will be printing the posts.
```php
<table class="table table-striped">
    <tr>
        <th>Title</th>
        <th></th>
        <th></th>
    </tr>
    @foreach ($posts as $post)
        <tr>
        <td>{{$post->title}}</br><small>Written on {{$post->created_at}} created by {{$post->user->name}}</small></td>
            <td><a href="/posts/{{$post->id}}/edit" class="btn btn-info">Edit</a> </td>
            <td></td>
        </tr>
    @endforeach
</table>
```
Now let's logout and register a new dummy user, than create a post and checkout the list in our */dashboar* if the it working or not as we have expected (showing the logged in user's posts only).
creds:
email: test@test.com
password: 12345678

### 3. Access Control
In this section we learn how to restrict access to the posts or actions of our applications. Here we will look at some of the basics access controls:
1. Guest cannot create, Edit or Delete a post.
2. A user cannot edit or delete other users post.
3. A user or Guest cannot access to the links which was not allowed.

In our application at the current stage if we logout and in the browser URL if we write **/posts/create** we can see the page although we are logged out, But it is not supposed to be happening therefore we have to restrict it by authenticating. Now if we look at our **DashboardController** we can see a contructor which is instantiating the class. in it a middleware is declared for *auth* means anythin in the dashboard class will be restricted to be accessed by anyone without logging in. So we have to do the same to our **PostController** class as well by adding the following:
```php
/**
 * Create a new controller instance.
 *
 * @return void
 */
public function __construct()
{
    $this->middleware('auth');
}
```
Now if we try the URL end-point **/posts/create** again we can see that without logging in we cannot access any of the pages from our **PostController** class. But we want our Guest User to to see the list posts and view them. For this we have to add some *exceptions* to our middleware authentication in our **PostController** class:
```php
public function __construct()
{
    $this->middleware('auth', ['except'=>['index', 'show']]);
}
```
Now the guest user is able to view the list and specific post as well. Though there are buttons to *edit* or *delete* a post it won't work as the required functions for editing and deleting the post is not added to the exception rule. Therefore it's better that not to show the buttons to the guest user. This can be achieved by doing:
```php
// in show.blade.php where the buttons are shown
@if (!Auth::guest())
    <a href="/posts/{{$post->id}}/edit" class="btn btn-info">Edit</a>

    {!! Form::open(['action'=>['PostsController@destroy', $post->id], 'method'=>'POST', 'class'=>'pull-right']) !!}
        {{Form::hidden('_method','DELETE')}}
        {{Form::submit('Delete',['class'=>'btn btn-danger'])}}
    {!! Form::close() !!}
@endif
```
With the above code it will check that the user is guest or not(logged in). If it is a Guest means not logged in the edit and delete buttons will not be displayed. Now restrictions to edit and delete to the guest user is done. 

But if we log in and view a post the buttons are shown which is a good thing, though the issue is that now a user can edit or delete another user's post which should be restricted as well. A user can view another user's post but should not be able to edit or delete another user's post. This issue can be solved by simply adding another condition after authenticating the user is guest or not:
```php
@if (!Auth::guest())
    @if (Auth::user()->id == $post->user_id)
        <a href="/posts/{{$post->id}}/edit" class="btn btn-info">Edit</a>

        {!! Form::open(['action'=>['PostsController@destroy', $post->id], 'method'=>'POST', 'class'=>'pull-right']) !!}
            {{Form::hidden('_method','DELETE')}}
            {{Form::submit('Delete',['class'=>'btn btn-danger'])}}
        {!! Form::close() !!}
    @endif
@endif
```
With this we have solved the issue of *an user cannot edit or delete anotther user's post*, but only for the frontend. If a user manually types in the url endpoint **/posts/4/edit** than the user can edit the post as the conditions till this point user is restricted only to the frontend, therefore we have to maintain the access in the backend as well. Which we can do by: 
```php
public function edit($id)
{
    $post = Post::find($id);
    
    // Check for the correct user
    if(auth()->user()->id !== $post->user_id){
        return redirect('/posts')->with('error', 'Un-authorized Page!');
    }

    return view('posts.edit')->with('post', $post);
}

public function destroy($id)
{
    $post = Post::find($id);
    // Check for the correct user
    if(auth()->user()->id !== $post->user_id){
        return redirect('/posts')->with('error', 'Un-authorized Page!');
    }
    $post->delete();
    return redirect('/posts')->with('success', 'Post Deleted');
}
```
With this if a user tries to edit or delete another user's post using URL end-point than the user will be redirected to the **/posts** along with an *error* message.