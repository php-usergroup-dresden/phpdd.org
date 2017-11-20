# Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via pull requests on [GitHub](http://github.com/php-usergroup-dresden/phpdd.org).

## Issues

- Please report issues here on [GitHub](http://github.com/php-usergroup-dresden/phpdd.org/issues)

## Pull Requests

- **Add tests!** - Your patch will not be accepted if it does not have tests.

- **Document any change in behaviour** - Make sure the documentation in `README.md` and the `CHANGELOG.md` is kept up-to-date.

- **Consider our release cycle** - We follow [SemVer v2.0.0](http://semver.org/). Randomly breaking public APIs is not an option.

- **Create topic branches** - Do not ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, please send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please squash them before submitting.


## Edit the website

The website is (obviously) generated static content.
 
If you want to make changes, fix typos or add missing content, please follow these steps.

### 1. Fork & clone the respository

* Please fork the repository to your github account.
* Clone the repository to your local machine

```bash
$ git clone https://github.com/<your-github-user>/phpdd.org.git
$ cd phpdd.org/
```

### 2. Install the static page generator

The static page generator is a PHAR that is installed using [composer](https://getcomposer.org) 
and [tm/tooly-composer-script](https://github.com/tommy-muehle/tooly-composer-script).

Simply run:

```bash
$ mkdir -p vendor/bin
$ composer update
```

The static page generator PHAR is placed to `vendor/bin/spg.phar` and is executable.

### 3. Make it a Document Root of a webserver

The simplest way is to use the build-in PHP webserver, like this:

```bash
$ php -S 127.0.0.1:8088 -t public/
# Should print something like this
PHP 7.0.11 Development Server started at Fri Nov  4 11:03:32 2016
Listening on http://127.0.0.1:8088
Document root is /Users/hollodotme/Sites/website
Press Ctrl-C to quit.
```

### 4. Generate the pages and sitemap for your locale base URL

In order to generate the pages for your local URL (http://127.0.0.1:8088 or whatever your webserver's URL is), you need 
to run the static page generator with the option `--baseUrl="http://127.0.0.1:8088"`.

```bash
$ vendor/bin/spg.phar generate:pages --baseUrl="http://127.0.0.1:8088" ./Project2018.json
$ vendor/bin/spg.phar generate:sitemap --baseUrl="http://127.0.0.1:8088" ./Project2018.json
```

**Note:** Use the `Project<YEAR>.json` file of the year you want to edit.

### 5. Open in browser

Now you should be able to view the pages in your browser when visiting http://127.0.0.1:8088.

### 6. Make changes

* You can edit the content files located in the `./src/Contents/<YEAR>/` directory.
* Please **do not edit** the `.html` files, those changes will be gone when generating the pages.
* You can add new images in `./public/<YEAR>/assets/images/`
* You can add new downloads in `./public/<YEAR>/assets/downloads/`
* You can add new pages in the `./Project<YEAR>.json` file. Please refer to the already existing page configs there.

**Pro-Tip:**

If you're using PhpStorm, you can set up a [FileWatcher](https://www.jetbrains.com/help/phpstorm/2016.2/using-file-watchers.html) for the `./src/Contents/<YEAR>/` directory and let the page generator 
automatically be executed as soon as you saved changes. You won't need to trigger the generator every time yourself. 

### 7. Commit & push your changes

**THIS IS IMPORTANT!**

**Before you commit your changes**, please generate the pages and sitemap again **without** the `--baseUrl`-option, 
so that the real base URL from the settings will take effect.

```bash
$ vendor/bin/spg.phar generate:pages ./Project<YEAR>.json
$ vendor/bin/spg.phar generate:sitemap ./Project<YEAR>.json
$ git add -A
$ git commit -m '...'
$ git push
```

### 8. Create a pull request

Please create a pull request to the origin repository. We'll then check your changes and merge them.

---

Thanks for your help!
