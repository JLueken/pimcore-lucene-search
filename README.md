# Pimcore Lucene Search
Pimcore 4.0 Website Search (powered by Zend Search Lucene)

![lucenesearch crawler](https://cloud.githubusercontent.com/assets/700119/25579028/7da66f40-2e74-11e7-8da5-988d61feb2e2.jpg)

### Requirements
* Pimcore 4.3

## Installation
**Handcrafted Installation**   
Because of additional dependencies you need to install this plugin via composer.

**Composer Installation**  
1. Add code below to your `composer.json`    
2. Activate & install it through backend

```json
"require" : {
    "dachcom-digital/lucene-search" : "1.4.0",
}
```

### Features
* Maintenance driven indexing
* Auto Complete
* Restricted Documents & Usergroups ([member](https://github.com/dachcom-digital/pimcore-members) plugin recommended but not required)
* Authenticated Crawling
* Search with categories

### Usage

**Default**  
The crawler Engine will start automatically every night by default. Please check that the pimcore default maintenance script is properly installed.

**Command Line Command**  
If you want to start the crawler manually, use this command:

```
$ php pimcore/cli/console.php lucenesearch:frontend:crawl crawl -f -v
```

| command | short command | type | description |
|:---|:---|:---|:---|
| ```force``` | `-f` | force crawler start | sometimes the crawler stuck because of a critical error mostly triggered because of wrong configuration. use this command to force a restart |
| ```verbose``` | `-v` | show some logs | good for debugging. you'll get some additional information about filtered and forbidden links while crawling. |


### Logs
You'll find some logs from the last crawl in your backend (at the bottom on the LuceneSearch settings page). Of course you'll also find some logs in your `website/var/log` folder.
**Note:** please enable the debug mode in pimcore settings to get all types of logs.

### Document Restrictions
If you want a seamless integration of protected document crawling, install our [member](https://github.com/dachcom-digital/pimcore-members) plugin.

#### How does the document restriction work?
Each document needs a meta tag in the head section. the crawler extract and stores the usergroup id(s) from that meta property. 
To allow the crawler to follow all the restricted documents, you need to configure the crawler authentication settings. 

**Meta Property Example**

```html
<meta name="m:groups" content="4">
```

If the document is restricted to a specific usergroup, the meta `content` contains its id. Otherwise, the meta property needs to be filled with a `default` value.

## Asset Language restriction
Because Assets does not have any language hierarchy, you need to add a property called `assigned_language`. This Property will be installed during the install process of LuceneSearch.
If you add some additional language afterwards, you need to add this language to the property. if you do not set any information at all, the asset will be found in any language context.

## Asset Country restriction
Because Assets does not have any country hierarchy, you need to add a property called `assigned_country`. This Property will be installed during the install process of LuceneSearch.
If you add some additional countries afterwards, you need to add this country to the property. if you do not set any information at all, the asset will be found in any country context.

## Setup Search Page
- Create a document, call it "search".
- Define a new method in your Controller (eg. search). 
- Create a view template (eg. `content/search.php`) and add this code:

```php
//viewScript = the template file in your website structure.
$this->action('find', 'frontend', 'LuceneSearch', array('viewScript' => 'frontend/find.php'));
```

You'll find the `frontend/find.php` Template in `LuceneSearch/views/scripts/`. If you want to change the markup, just copy the template into your website script folder and change the `viewScript` parameter.

## Using Ajax AutoComplete
Use this snippet to allow ajax driven autocomplete search. you may want to use this [plugin](https://github.com/devbridge/jQuery-Autocomplete) to do the job.

```js
var $el = $('input.search-field'),
    language = $el.data('language'), //optional
    country = $el.data('country'); //optional

$el.autocomplete({
    minChars: 3,
    triggerSelectOnValidInput: false,
    lookup: function(term, done) {

        $.getJSON(
            '/plugin/LuceneSearch/frontend/auto-complete/',
            {
                q: term,
                language : language,
                country: country
            },
            function(data) {

                var result = { suggestions : [] };

                if(data.length > 0) {
                    $.each(data, function(index, suggession) {
                        result.suggestions.push( {value : suggession });
                    });
                }

                done(result);

        });

    },
    onSelect: function(result) {

        $el.val(result.value);
        $el.parents('form').submit();

    }

});
```
## Search with Categories
Add your category to the available categories in the Lucene Search config
in lucenesearch_configurations.php:

```php
"key" => "frontend.categories",
"data" => [
    "category1",
    "category2",
    "category3",
    "category4"
],
```

In *Document* => *Settings* go to *Meta Data* and add a new field with any number of categories:
```html
<meta name="lucene-search:categories" content="category1,category3">
```

## Custom Meta Content
In some cases you need to add some content or keywords to improve the search accuracy. 
But it's not meant for the public crawlers like Google. LuceneSearch uses a custom meta property called `lucene-search:meta`.
This Element should be visible while crawling only.

**Custom Meta in Documents**  
In *Document* => *Settings* go to *Meta Data* and add a new field:

```html
<meta name="lucene-search:meta" content="your content">
```

**Custom Meta in Objects**  
Because Object may have some front-end capability (a news detail page for example), you have to integrate the custom meta field by yourself.

**Example:**

```php
if( \LuceneSearch\Tool\Request::isLuceneSearchCrawler() )
{
    $this->view->headMeta()->setName( 'lucene-search:meta', $product->getInternalSearchText( $lang ) );
}
```

**Custom Meta in Assets**  
TBD

## Copyright and license
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.ch)  
For licensing details please visit [LICENSE.md](LICENSE.md)  

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)
