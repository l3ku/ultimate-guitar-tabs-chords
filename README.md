# Ultimate Guitar Tabs & Chords
[![Build Status](https://travis-ci.org/l3ku/ultimate-guitar-tabs-chords.svg?branch=master)](https://travis-ci.org/l3ku/ultimate-guitar-tabs-chords)
[![Total Downloads](https://poser.pugx.org/l3ku/ultimate-guitar-tabs-chords/downloads)](https://packagist.org/packages/l3ku/ultimate-guitar-tabs-chords)
[![Latest Unstable Version](https://poser.pugx.org/l3ku/ultimate-guitar-tabs-chords/v/unstable)](https://packagist.org/packages/l3ku/ultimate-guitar-tabs-chords)
[![License](https://poser.pugx.org/l3ku/ultimate-guitar-tabs-chords/license)](https://packagist.org/packages/l3ku/ultimate-guitar-tabs-chords)

A WordPress plugin that fetches tabs and chords from Ultimate Guitar.

## Installation

This plugin is yet not available via wordpress.org. Installation begins by navigating to the WordPress plugins directory and cloning the plugin git repository by cloning this project from GitHub:
```git clone git@github.com:l3ku/ultimate-guitar-tabs-chords.git```.

If Composer is used in your project, this plugin can also be included by running `composer require l3ku/ultimate-guitar-tabs-chords`.

The plugin should now be available for activation in the WordPress admin plugins page. After activation, a new admin menu link with the text "UGTC" should be available, which provides access to all plugin settings pages.

## Usage
Specify your content search settings from `UGTC->Search Settings` (e.g. whether
to show tabs and/or chords and how to order them).

Add the plugin shortcode to the desired page or post. You can generate the plugin shortcode by using the shortcode generator available on the main plugin page.

## Tests
PHPUnit based unit tests are included in this project in the `tests/` directory. Before running the tests, install the WordPress test suite by running the installation script in the plugin directory:

```bash bin/install-wp-tests.sh wordpress_test <DB_USER> <DB_PASSWORD> localhost latest```

<b>NOTE: </b> *the database user needs permissions to create tables*

The tests can then be run by simply running the command `phpunit`.

## Development
This project is very new and the features of this plugin are still quite limited. Feedback, issues and PR:s are welcome! However, before submitting a pull request make sure that:
1. All tests complete successfully
2. The commits in your PR are logical entities (use `git rebase -i` if necessary)
3. Your code is in line with [WordPress plugin guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/) and the style of the existing code
4. You clearly explain in the PR what is done and why
