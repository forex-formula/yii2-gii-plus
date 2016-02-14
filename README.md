# yii2-gii-plus

Yii 2 Gii Plus Extension

## Base Model Generator

```
yii gii/base_model --interactive=0
```
This command generates the following files:

* models/base/BlogBase.php
* models/base/CommentBase.php
* models/base/PostBase.php
* models/query/base/BlogQueryBase.php
* models/query/base/CommentQueryBase.php
* models/query/base/PostQueryBase.php

## Custom Model Generator

```
yii gii/custom_model --interactive=0
```
This command generates the following files:

* models/Blog.php
* models/Comment.php
* models/Post.php
* models/query/BlogQuery.php
* models/query/CommentQuery.php
* models/query/PostQuery.php

## After Custom Models

You should regenerate base models after custom models.
```
yii gii/base_model --interactive=0 --overwrite=1
yii gii/base_model --interactive=0 --overwrite=1
```
The first regeneration inserts relations. The second regeneration updates phpDoc @return directives.
