This TastyIgniter extension allows end users to create and edit dynamic pages with a simple WYSIWYG user interface.

### Admin Panel
In the admin user interface you can create, edit or delete pages. 

### How to use the Pages Component

**Example**

```
---
title: Site Pages
permalink: /pages/:slug

'[sitePage]':
    slug: ':slug'
---
<?
function onEnd()
{
    $this->title = $this['sitePage'] ? $this['sitePage']->title : $this->title;
}
?>
---
...
<?= component('sitePage'); ?>
...
```

### License
[The MIT License (MIT)](https://tastyigniter.com/licence/)