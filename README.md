handlelars
========

A Laravel wrapper for handlebars.php, a PHP implementation of http://handlebarsjs.com/

# Acknowledgements
- Original credit to the creator of this repo https://github.com/brightmachine/laratash, all I did was rename everything to handlebars and configure the handlebars PHP library.

## Supports
- `Laravel 5`
- `Handlebars 2.0+`

# Installation

Add handlelars as a dependency to your `composer.json` file:

```json
"require": {
	"laravel/framework":      "~5.0",
	"truemedia/handlelars": "dev-master"
}
```

run `composer update`, or `composer install` if this is a brand new project
	
## Add the Service Provider

Open: `config/app.php`

```php
...

'Handlelars\HandlelarsServiceProvider',
	
...
```

You are all setup!


# Usage

Handlelars is merely a wrapper for the [Handlebars.php](https://github.com/XaminProject/handlebars.php) library that integrates it into Laravel 5+.

Handlelars registers itself with the Laravel View class, providing seamless integration with Laravel.  You can use Handlebars just as you would Blade!
The Laravel View class will choose the right template engine to use based on the file extension of the view.  So all you have to do to render Handlebars files, is ensure that your view has a `.hbs` file extension.  Handlelars will take care of the rest.

You can even mix and match template engines.  For instance maybe you have a Blade layout file, and you want to nest a Handlebars view, that's fine! 
The Handlebars view will be rendered into a variable named whatever section you passed the view to.  So for example if you were to do:

```php
$view->nest('content', 'some.view');
$view->nest('sidebar', 'some.sidebar');
```

The contents of the parsed `some.view` file will be available in the template file under a variable called `$content`.
The contents of the parsed `some.sidebar` would be available in the template file, under a variable called `$sidebar`.

By default, Handlebars partials are also loaded using Laravel's ViewFinder, so you can feel free to use dot-notation to specify a view.

```html
{{#posts}}
	{{> posts._post}}
{{/posts}}
```

Other than that it is business as usual!

# Examples:

- Example using View::make()

	app/views/test.hbs
	
		<h1>{{ pageHeading }}</h1>
		<div>
			{{ pageContent }}
		</div>
		
	app/router.php
	
		Route::get('/', function()
		{
			return View::make('test', array(
				'pageHeading' => 'Rendered with Handlebars.php',
				'pageContent' => 'But still looks like Laravel!'
			));
		});

- Example using a Blade controller layout
	
	app/views/layouts/master.blade.php

		<html>
		<head></head>
		<body>
			{{ content }}
		</body>
		</html>
		
	app/views/test.mustache
	
		<h1>{{ pageHeading }}</h1>
		<div>
			{{ pageContent }}
		</div>
	
	app/controllers/TestController.php

		<?php

		class TestController extends BaseController {
		
		    public $layout = 'layouts.master';
		    
		    public function index()
		    {
		 	$this->layout->nest('content', 'test', array(
		 		'pageHeading' => 'Rendered with Handlebars.php',
				'pageContent' => 'But still looks like Laravel!'
		 	));   
		    }
		    
	    	}
	    	
- Example using a Handlebars layout

	app/views/posts/_post.hbs
		
		<article>
			<h2>{{ title }}</h2>
			<div>
				{{ content }}
			</div>
		</article>
	
	app/views/blog/index.hbs

		<html>
		<head></head>
		<body>
			<h1>My Blog</h1>
			
			{{#posts}}
				{{> posts._post}}
			{{/posts}}
		</body>
		</html>
		
	app/routes.php
	
		Route::get('/', function()
		{
			$posts = array(
				array(
					'title' => 'This is a Title',
					'content' => 'lorem ipsum...'
				),
				array(
					'title' => 'This is a another title',
					'content' => 'lorem ipsum...'
				),
				array(
					'title' => 'This is yet another Title',
					'content' => 'lorem ipsum...'
				),
			);
			
			return View::make('blog.index', compact('posts));
		});

## A note on template data

Laravel expects [view data](http://laravel.com/docs/5.0/views#basic-usage) to be be passed as an `array`.

Handlebars PHP, however, also allows a Context object to be used.

If you wish to use a Context object, pass through an array with a `__context` key and this will be used. E.g.

    `return view('my.view', ['__context' => new Context]);`

# Configure

You can alter the configuration options that are passed to Handlebars.php in your `ConfigServiceProvider`. E.g. 

	config([
		'handlelars.cache' => storage_path() . '/framework/views/handlebars',
	]);
	
All `handlelars.` options are passed directly to the Mustache_Engine constructor, so you can use any of the options that you would use with [Mustache.php](https://github.com/bobthecow/mustache.php)
