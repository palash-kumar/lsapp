### File Uploading
In this part we will see how to upload a file to server and load it on the frontend. To upload a file or image file for out post let's edit our *create.blade.php* file:
```php
{!! Form::open(['action'=>'PostsController@store', 'method'=>'POST', 'enctype'=>'multipart/form-data']) !!}
    <div class="fom-group">
        {{Form::label('title', 'Title')}}
        {{Form::text('title', '', ['class'=>'form-control', 'placeholder'=>'Title'])}}
    </div>
    <div class="fom-group">
        {{Form::label('body', 'Body')}}
        {{Form::textarea('body', '', ['id'=>'article-ckeditor','class'=>'form-control', 'placeholder'=>'Body text'])}}
    </div>
    <div class="fom-group">
        {{Form::file('cover_image')}}
    </div>
    {{Form::submit('Submit',['class'=>'btn btn-primary'])}}
{!! Form::close() !!}
```
Whenever we are to upload a file we have to use **'enctype'=>'multipart/data'**, and we have added a file tag **{{Form::file('cover_image')}}** to browse our directory to select file for upload. Now in the database we need another column in our **post** table to saveour image/image location. So we will be creating another anothere migartion for adding *cover_image* column:
```shell
php artisan make:migration add_cover_image_to_posts
```
And update our newly created migration file:
```php
public function up()
{
    Schema::table('posts', function (Blueprint $table) {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('cover_image');
        });
    });
}

public function down()
{
    Schema::table('posts', function (Blueprint $table) {
        //
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('cover_image');
        });
    });
}
```
As we are done with our migration file lets save the newly created colummn to DB and update our **posts** table:
```shell
php artisan migrate
```
Now our posts table has got the new column. Let's continue with uploading images/files. As we have prepared our create post page to submit image so let's update our **store(Request $request)** function to upload our file to server. Our modified code will be like:
```php
public function store(Request $request)
{
    $this->validate($request,[
        'title'=>'required',
        'body'=>'required',
        'cover_image'=>'image|nullable|max:1999'
    ]);

    // Handle File Upload
    if($request->hasFile('cover_image')){
        // Get filename with extension
        $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
        // Get just the filename
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        // Just Get the Extension
        $extension  = $request->file('cover_image')->getClientOriginalExtension();
        // Filename to store
        $filenameToStore = $filename.'_'.time().'.'.$extension;

        // Upload Image
        $path = $request->file('cover_image')->storeAs('public/cover_images', $filenameToStore);
    }else{
        $filenameToStore = "noImage.jpg";
    }

    // Creating Post
    $post = new Post;
    $post->title = $request->input('title');
    $post->body = $request->input('body');
    $post->user_id = auth()->user()->id;
    $post->cover_image = $filenameToStore;
    $post->save();
    return redirect('/posts')->with('success', 'Post Created');
}
```

Now we can save image for our posts. But what if we want to delete the post? Than we have to delte the image from the server as well. Therefore our **destroy($id)** code will be modified to :
```php
public function destroy($id)
{
    $post = Post::find($id);
    // Check for the correct user
    if(auth()->user()->id !== $post->user_id){
        return redirect('/posts')->with('error', 'Un-authorized Page!');
    }

    if($post->cover_image != 'noImage.jpg'){
        // Delete Image
        Storage::delete('public/cover_images/'.$post->cover_image);
    }

    $post->delete();
    return redirect('/posts')->with('success', 'Post Deleted');
}
```

As we can see from the code by following the code **storeAs('public/cover_images', $filenameToStore)** is going to create a folder named **cover_images** in the location **storage -> app -> public** directory. And this directory is not accessible through the browser so we will not be able to load the image. Therefore we have to create a *symlink* to the **public** driectory at the root. To do that we have to run this command.
```shell
php artisan storage:link
```
Now if we check our **public** directory at the root we can see a new **storage** directory is created.

### Uploading Project to Hosting server
Firstly create a database using the cpanel. Note down the Database name, username and password. We will require it later.
Export the DB from local database. Select the newly created database in cpanel. and import the sql script which we have exported from our local db. 

Create a new directory named after project and copy all the files from project except the public directectory, and upload it to the cpanle's created new project directory created for the app.

Than go to the **public_html** and copy everything from the projects **public** directory and paste it in the **public_html** directory in cPanel. 

Now we will have to edit *index.php* file which we have just copied along with other files from our project **public** direcotry. Than edit the following two lines in the *index.php* file:
```php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
```
and show the path to our project directory in cPanel in this case **lsapp**:

```php
require __DIR__.'/../lsapp/vendor/autoload.php';

$app = require_once __DIR__.'/../lsapp/bootstrap/app.php';
```
Now we wil have to edit our **.env** folder and change the db creadentials to our hosting DB credentials.

The only thing left is the image symlink. As a beginner create a new php file **createSymlink.php** and write the following code:
```php
symlink('/home/cPanel_name/app-name/storage/app/public','/home/cPanel_name/public_html/storage');
```
and save it than upload it to **public_html** directory. Than in the browser do the following:
with the host url call the php file **createSymlink.php** as *www.host-provider.com/createSymlink.php*. Now if everything runs correctly than we will see a new directory named **storage** created in **public_html**. Then delete the symlink file which we have just created.

>NOTE to create a storage link from cPanel one can use the following method by accessing the trminal from **cPanel >> Home >> Advanced >> Terminal** and type the following command
>``` ln -s /home/asimgcco/asimgc-site/storage/app/public/ /home/asimgcco/public_html/devSite/storage ```
>
And the deployment is done.
