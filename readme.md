# [Advanced Find Plugin for Wolf CMS](http://www.tbeckett.net/articles/plugins/adv-find.xhtml)

I was unable to find a solution to the problem of consolidating multiple archived threads into one feed. The problem was that Wolf only allowed you to search one archive at a time. Therefore, you could only list articles in order of age, if they were in the same archive. This function changes that by allowing you to search multiple archives at a time and list them all in one consolidated feed in the same manner that was previously available. All of the find() function's capability is there, I just expanded it to include a larger number of articles. This is great for consolidating multiple archives that each have their own pages into one RSS feed.

## Install:
Simply extract the archive into your /wolf/plugins/ directory. At this point, all you need do is go to your Administration tab in the backend of your Wolf installation and click the checkbox on the far right of the plugin's name.

## Use:
To use, simply call the adv_find() function and specify the following arguments:

1. _Required:_ array('/archive-tree1/','/archive-tree2/') - Specify any number of archive trees here. For example, on my page, I might use: adv_find(array('/articles/free-market/','/articles/plugins/'));
2. _Optional:_ array('order' => 'table value here DESC', 'limit' => '1+') - Specify your order and limit values here.  Fixed as of version 1.0.3.  Limit now needs to be 1 or greater.

## Example:

    <?php $articles = adv_find(
    array('articles/free-market/', 'articles/guns/', 'articles/politics/'),
    array('order' => 'published_on DESC')); ?>

## Please Note:
At this point it is not necessary to use the children() function, it has already been performed and the objects sorted for you.