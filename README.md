# Ultimate Guitar Tabs & Chords
[![Build Status](https://travis-ci.org/l3ku/ultimate-guitar-tabs-chords.svg?branch=master)](https://travis-ci.org/l3ku/ultimate-guitar-tabs-chords)
[![Total Downloads](https://poser.pugx.org/l3ku/ultimate-guitar-tabs-chords/downloads)](https://packagist.org/packages/l3ku/ultimate-guitar-tabs-chords)
[![Latest Unstable Version](https://poser.pugx.org/l3ku/ultimate-guitar-tabs-chords/v/unstable)](https://packagist.org/packages/l3ku/ultimate-guitar-tabs-chords)
[![License](https://poser.pugx.org/l3ku/ultimate-guitar-tabs-chords/license)](https://packagist.org/packages/l3ku/ultimate-guitar-tabs-chords)

A WordPress plugin that fetches tabs and chords from Ultimate Guitar by scraping HTML.

## Installation

This plugin is yet not available via the wordpress.org plugins directory. Installation is done by navigating to the WordPress plugins directory and cloning the plugin git repository by cloning this project from GitHub:
```git clone git@github.com:l3ku/ultimate-guitar-tabs-chords.git```.

If Composer is used in your project, this plugin can also be included by running `composer require l3ku/ultimate-guitar-tabs-chords`.

The plugin should now be available for activation in the WordPress admin plugins page. After activation, a new settings page (under "Settings") should be available with the name `UG Tabs & Chords`.

## Usage

Add the plugin shortcode to the desired page or post. You can generate the plugin shortcode by using the shortcode generator available on the plugin settings page.

## Tests
PHPUnit based unit tests are included in this project in the `tests/` directory. Before running the tests, install the WordPress test suite by running the installation script in the plugin directory:

```bash bin/install-wp-tests.sh wordpress_test <DB_USER> <DB_PASSWORD> localhost latest```

<b>NOTE: </b> *the database user needs permissions to create tables*

The tests can then be run by simply running the command `phpunit`.

## Development
This project is very new and the features of this plugin are still quite limited. Feedback, issues and PR:s are welcome! However, before submitting a pull request make sure that:
1. All tests complete successfully
2. The commits in your PR are logical entities (use `git rebase -i` if necessary)
3. Your code is in line with [WordPress plugin guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/), [WordPress coding standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#naming-conventions) and
the already existing plugin code
4. You clearly explain in the PR what is done and why
