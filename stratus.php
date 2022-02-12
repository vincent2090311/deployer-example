<?php
declare(strict_types=1);

namespace Deployer;

set('stratus_cli', '/usr/share/stratus/cli');

desc('Stratus clear all caches');
task('stratus:cc', function () {
    if (!isDev()) {
        run('{{stratus_cli}} cache.all.clear;');
    }
});

desc('Stratus Cloudfront clear');
task('stratus:cloudfront', function () {
    if (!isDev()) {
        run('{{stratus_cli}} cache.cloudfront.invalidate');
    }
});

desc('Stratus Varnish clear');
task('stratus:varnish', function () {
    if (!isDev()) {
        run('{{stratus_cli}} cache.varnish.clear');
    }
});

desc('Stratus Nginx Reload');
task('stratus:nginx:update', function () {
    if (!isDev()) {
        run('{{stratus_cli}} nginx.update');
    }
});

desc('Turn off cron before deploy');
task('stratus:cron_disable', function () {
    if (isProduction()) {
        writeln('<info>Disabling crons...</info>');
        run('{{stratus_cli}} crons.stop;');
    }
});
desc('Turn on cron after deploy');
task('stratus:cron_enable', function () {
    if (isProduction()) {
        writeln('<info>Enabling crons...</info>');
        run('{{stratus_cli}} crons.start;');
    }
});

desc('Zero Downtime Deployment Init (Blue/Green)');
task('stratus:zdd:init', function () {
    run('{{stratus_cli}} zerodowntime.init');
});

desc('Zero Downtime Deployment Switch (Blue/Green)');
task('stratus:zdd:switch', function () {
    run('{{stratus_cli}} zerodowntime.switch');
});

desc('Sync scaled php-fpm pods with new codebase Autoscaling depreciated method');
task('stratus:autoscale:reinit', function () {
    run('{{stratus_cli}} autoscaling.reinit && sleep 120s');
});

desc('Handle Webscale Stratus autoscaling');
task('stratus:autoscale', function () {
    if (!isDev()) {
        writeln('<info>Clearing caches...</info>');
        invoke('stratus:cc');
        writeln('<info>Initializing zero downtime...</info>');
        invoke('stratus:zdd:init');
        writeln('<info>Switching to new code base</info>');
        invoke('stratus:zdd:switch');
        writeln('<info>Reload Nginx in case of Docroot symlink change</info>');
        invoke('stratus:nginx:update');
        writeln('<info>Clearing caches one more time after blue or green switched...</info>');
        invoke('stratus:cc');
    }
});
