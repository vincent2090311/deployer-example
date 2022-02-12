# Webscale Stratus deployer-example
Webscale Stratus Deployment example repo using Deployer.org application

What is Deployer? <BR>
A deployment tool written in PHP with support for popular frameworks out of the box. Deployer used by hundreds thousands of projects around the world, performing more than a million of deploys each month. Deployer comes with more than 50 ready to use recipes for frameworks and third-party services.
  
This repository is created to help you to adjust your Deployer.org deployment processes and use it to deploy application on Webscale Stratus platform.
  
Basically to get started first we need to install Deployer application as per https://deployer.org/docs/7.x/installation guidelines.
```
curl -LO https://deployer.org/deployer.phar
mv deployer.phar /usr/local/bin/dep
chmod +x /usr/local/bin/dep
```

Now, we can cd into the project and run following command:
```
dep init
```

Deployer will ask you a few question and after finishing you will have a deploy.php or deploy.yaml file. This is our deployment recipe. It contains hosts, tasks and requires other recipes. All framework recipes that come with Deployer are based on the common recipe.
https://deployer.org/docs/7.x/recipe/common

Deployer.org already covered Magento 2 recpie and it can be downloaded/reviewed here:
https://github.com/deployphp/deployer/blob/master/recipe/magento2.php
  
How to install this package:
```
composer require magemojo/deployer-example --dev
```

How to use
After install it, you can add the line below after the namespace and run dep to check:

```
// Webscale Stratus Recipe
require __DIR__ . '/vendor/magemojo/deployer-example/stratus.php';
```

This recipe when installed automatically will clean all caches after the deploy success, but if you want to restart all services, add these into the bottom:

```
// Webscale Stratus start/stop/restart services
before('deploy', 'stratus:cron:stop');
after('deploy', 'stratus:cron:start');
after('success', 'stratus:zdd:init');
after('success', 'stratus:zdd:switch');

// Webscale Stratus clean all caches
after('success', 'stratus:cloudfront:clear');
after('success', 'stratus:varnish:clear');
after('success', 'stratus:redis:clear');
after('success', 'stratus:cache:clear');
```

For example:
```
<?php

namespace Deployer;
// Webscale Stratus Recipe
require __DIR__ . '/vendor/magemojo/deployer-example/stratus.php';

// Project
set('application', 'My Project Name');
set('repository', 'git@bitbucket.org:mycompany/my-project.git');
set('default_stage', 'production');

// Project Configurations
host('production')
    ->hostname('iuse.magemojo.com')
    ->user('my-user')
    ->port(22)
    ->set('deploy_path', '/home/my-project-folder')
    ->set('branch', 'master')
    ->stage('production');

// Webscale Stratus restart services
after('success', 'stratus:zdd:init');
after('success', 'stratus:zdd:switch');

// Webscale Stratus clean all caches
after('success', 'stratus:cloudfront:clear');
after('success', 'stratus:varnish:clear');
after('success', 'stratus:redis:clear');
after('success', 'stratus:cache:clear');
```

Summary of all available commands:
stratus:cron:stop	Stop Crons from running
stratus:cron:start	Start crons
stratus:zdd:init	Zero Downtime Deployment Init
stratus:zdd:switch	Zero Downtime Deployment Switch with check
stratus:autoscaling:reinit	It will issue a redeploy of PHP-FPM services
stratus:cc	Clears everything
stratus:cloudfront:clear	Clears Cloudfront cache
stratus:opcache:clear	Clears OPCache cache
stratus:redis:clear	Clears Redis cache
stratus:varnish:clear	Clears Varnish cache
stratus:nginx:update Reload Nginx
