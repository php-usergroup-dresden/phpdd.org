require 'mina/rails'
require 'mina/git'

# Basic settings:
#   domain       - The hostname to SSH to.
#   deploy_to    - Path to deploy into.
#   repository   - Git repo to clone from. (needed by mina/git)
#   branch       - Branch name to deploy. (needed by mina/git)

set :domain, 'phpdd.org'
set :deploy_to, '/var/www/phpdd.org'
set :repository, 'https://github.com/php-usergroup-dresden/phpdd.org.git'
set :branch, 'master'
set :keep_releases, 3

# Manually create these paths in shared/ (eg: shared/config/database.yml) in your server.
# They will be linked in the 'deploy:link_shared_paths' step.
set :shared_files, [
    "config/App.php",
    "config/Discounts.php",
    "config/Email.php",
    "config/MySql.php",
    "config/Paypal.php",
    "config/phpbu.xml",
    "config/Sentry.php",
    "config/Session.php",
    "config/Slack.php",
    "config/Stripe.php",
    "config/Twig.php",
]
set :shared_dirs, [
    "public/2017/media",
    "public/2018/downloads",
    "data/static/done",
    "data/mail/attachments"
]

# Optional settings:
   set :user, 'deploy'    # Username in the server to SSH to.
   set :port, '22'     	  # SSH port number.

desc "Deploys the current version to the server."
task :deploy => :remote_environment do
  deploy do
    # Put things that will set up an empty directory into a fully set-up
    # instance of your project.

    invoke :'git:clone'
    invoke :'deploy:link_shared_paths'
    invoke :'composer_install'
    invoke :'generate_static_pages'

    on :launch do
      invoke :'reload_env'
      invoke :'clear_twig_cache'
      invoke :'check_static_pages'
      invoke :'deploy:cleanup'
    end
  end
end

desc "Composer install"
task :composer_install do
    command 'composer install -a --no-interaction --no-dev'
end

desc "Generate static pages"
task :generate_static_pages do
    command 'php vendor/bin/spg.phar generate:pages -b https://2018.phpdd.org Project2018.json'
    command 'php vendor/bin/spg.phar generate:sitemap -b https://2018.phpdd.org Project2018.json'
    command 'php vendor/bin/spg.phar generate:search-index -b https://2018.phpdd.org Project2018.json'
end

desc "Check static pages"
task :check_static_pages do
    command 'php vendor/bin/spg.phar check:links -b https://2018.phpdd.org Project2018.json'
end

desc "Clear twig cache"
task :clear_twig_cache do
    command 'sudo rm -rf /var/www/phpdd.org/shared/twig/*'
end

desc "Reloading nginx and php-fpm"
task :reload_env do
    command 'sudo service php7.2-fpm reload'
    command 'sudo service nginx restart'
end

desc "Rolls back the latest release"
task :rollback => :remote_environment do
  command! %[echo "-----> Rolling back to previous release for instance: #{domain}"]

  # Delete existing sym link and create a new symlink pointing to the previous release
  command %[echo -n "-----> Creating new symlink from the previous release: "]
  command %[ls "#{deploy_to}/releases" -Art | sort | tail -n 2 | head -n 1]
  command! %[ls -Art "#{deploy_to}/releases" | sort | tail -n 2 | head -n 1 | xargs -I active ln -nfs "#{deploy_to}/releases/active" "#{deploy_to}/current"]

  # Remove latest release folder (active release)
  command %[echo -n "-----> Deleting active release: "]
  command %[ls "#{deploy_to}/releases" -Art | sort | tail -n 1]
  command! %[ls "#{deploy_to}/releases" -Art | sort | tail -n 1 | xargs -I active rm -rf "#{deploy_to}/releases/active"]
end
