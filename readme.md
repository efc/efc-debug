# EFC Debug Plugin

- Contributors: efc
- Tags: debugging
- Requires at least: 3.5
- Tested up to: 3.7.1
- Stable tag: master
- License: The MIT License 
- License URI: http://opensource.org/licenses/MIT

This is a very simple plugin to facilitate the display of debugging messages. Nothing fancy here.

## Description

This plugin will place a small menu with an umbrella toward the right side of the admin bar. Clicking on this menu reveals a hidden div where debug messages are available.

### Features

- Recognizes a variable number of arguments.

- This plugin distinguishes between strings and other variables being reported. Any non-string will automatically be dumped with ```print_r()``` so that all elements can be seen.

- The umbrella in the admin bar will be on a red background if ```WP_DEBUG``` is true. This is an extra reminder to turn off debugging before putting code into production.

- Always provides a copy of ```$wp_query``` and ```$ENV```.

### Usage

Include ```efc-debug-include.php``` somewhere at the top of your functions.php (or other PHP file)...

```include_once(dirname(__FILE__).'/../../plugins/efc-debug/efc-debug-include.php');```

...note that you may well have to edit that line to include the proper reference to the file.

Then anywhere else in your code use this to add items to the debug report...

```if (function_exists('dbug_report')) dbug_report('message: ',$variable);```

Include as many arguments as you need. Any non-string variable you include will be expanded to reveal its elements.

Don't forget you can use PHP magic constants too:

```if (function_exists('dbug_report')) dbug_report(__CLASS__, __METHOD__);```

When looking at your page, you can reveal the debug report by clicking on the umbrella in the adming bar.

## Notes

This is really a plugin for developers. It will be of little use to regular WordPress users.

In fact, you should probably deactivate this plugin whenever you are not actively working on your site's code.

## Frequently Asked Questions

### Aren't there better debugging plugins around?

Sure! Many people swear by [Debug Bar](https://wordpress.org/plugins/debug-bar/). Go check it out. For me, though, it was just too complex. I wanted something simple.

