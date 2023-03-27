# Symlinked Plugin Branch

[![Packagist](https://img.shields.io/packagist/v/josephfusco/symlinked-plugin-branch.svg?style=flat-square)](https://packagist.org/packages/josephfusco/symlinked-plugin-branch)

Easily identify the current git branch of your symlinked WordPress plugins.

<img width="1112" alt="Screen Shot 2023-01-26 at 10 46 19 PM" src="https://user-images.githubusercontent.com/6676674/215005640-3e4eca5e-c3ca-49e5-8f2e-d1f3b3140f81.png">

## Example for Faust development

The following example will symlink the Faust WordPress plugin into a WordPress site managed by [Local](https://localwp.com/).

> _Note that the [Faust WordPress plugin](https://wordpress.org/plugins/faustwp/) source code lives inside the [wpengine/faustjs](https://github.com/wpengine/faustjs) so the plugin would display the branch of the faustjs monorepo._

### Create the symlink

```sh
ln -s \
  ~/Code/faustjs/plugins/faustwp/ \
  ~/Local\ Sites/faust/app/public/wp-content/plugins
```

At this point, you should be able to see your plugin within your WordPress site.


You can test your work by viewing the plugins page of your website, or by using `ls -l <plugins-directory>`
