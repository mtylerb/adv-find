I was unable to find a solution to the problem of consolidating multiple archived threads into one feed. The problem was that Wolf only allowed you to search one archive at a time. Therefore, you could only list articles in order of age, if they were in the same archive. This function changes that by allowing you to search multiple archives at a time and list them all in one consolidated feed in the same manner that was previously available. All of the find() function's capability is there, I just expanded it to include a larger number of articles. This is great for consolidating multiple archives that each have their own pages into one RSS feed.

Install: Simply extract one of the archives below into your /wolf/plugins/ directory. At this point, all you need do is go to your Administration tab in the backend of your Wolf installation and click the checkbox on the far right of the plugin's name.

Use: To use, simply call the adv_find() function and specify the following arguments:

  - Required: array('/archive-tree1/','/archive-tree2/') - Specify any number of archive trees here. For example, on my page, I might use: adv_find(array('/articles/free-market/','/articles/plugins/'));
  - Optional: array('order' => 'table value here DESC', 'limit' => '2+') - Specify your order and limit values here. Limit needs to be 2 or greater for now until I can fix the logic for a single entry.

An example I do use on my RSS page is:

<?php $articles = adv_find(
array('articles/free-market/', 'articles/guns/', 'articles/politics/'),
array('order' => 'published_on DESC')); ?>

Please note: At this point it is not necessary to use the children() function, it has already been performed and the objects sorted for you.