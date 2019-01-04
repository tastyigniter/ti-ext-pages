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
    
'[pageNav]':
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

**Example of displaying the page navigation sidebar**
```            
<?= partial('pageNav::sidebar'); ?>
``` 

**Example of displaying the page navigation links**

The pageNav component provides the following variables on the page its loaded.

`$headerPageList`: Contains an array of pages with header navigation selected.
`$footerPageList`: Contains an array of pages with footer navigation selected.

```            
<?php if (!empty($headerPageList)) foreach ($headerPageList as $page) { ?>
    <li class="nav-item">
        <a class="nav-link"
           href="<?= page_url('pages', ['slug' => $page->permalink_slug]); ?>"
        ><?= $page->name; ?></a>
    </li>
<?php } ?>
``` 

### License
[The MIT License (MIT)](https://tastyigniter.com/licence/)